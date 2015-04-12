#!/usr/bin/perl  -T

=head1 SYNOPSYS

searchSholem.cgi keyword ...

Finds all the places each keyword appears in Ale Verk fun Sholem Aleykhem
and shows a bit of context.

Author: Raphael Finkel 12/2014  GPL

=cut

use strict;
use utf8;
use CGI qw/:standard -debug/;
$ENV{'PATH'} = '/bin:/usr/bin:/usr/local/bin:/usr/local/gnu/bin'; # for security

# constants

my $searchResultsFor = 'Search results for: '; # Yiddish: "Search results for:"
my $resultsPerPage = 10; # Number of results we will show per page

my $dataFile = "/u/peg-d4/TMP/raphael/sholem-aleykhem-ale/output.uyid";

# This form will display:
#	- All Works of Sholem Aleichem
#	- Input search bar
#	- Special Yiddish characters

# Calls searchSholem.cgi and sends text entered in the input text field via the post method
my $form = "
	<div class='container bg-primary text-center col-md-12'>
            <div class='row top'>
		<div class='col-md-offset-3 col-md-6'>
		    <a href='searchSholem.cgi'><h1>אַלע װערק פֿון שלום עליכם</h1></a>
		</div>
		<div class='col-md-offset-1 col-md-1'>
		    <h4><a href='sholem_login.php'>Login</a></h4>
		</div>
            </div>
        <form
	action='" . $0 . "'
	method='post' enctype='multipart/form-data' autocomplete='off'>
		<input type='text' name='keys' size='40'
			placeholder='search'
			id='entry'
			onmouseover='getElementById('entry').focus()'
			/>
		<br />
		<script type=\"text/javascript\">
			printLetters('א אַ אָ ב בֿ ג ד ה ו װ ױ ז ח ט י יִ ײ ײַ כ כּ ך ל מ ם נ ' +
				'ן ס ע פּ פֿ פ ף צ ץ ק ר ש שׂ תּ ת ־');
		</script>
	</form></div>
	";

my $javaScript = '
	function showLetter(letter) {
		document.getElementById("entry").value += letter;
	} // showLetter
	function printLetter(letter) {
		document.write("<span class=\"clickable\" " +
		"onclick=\'showLetter(\"" + letter + "\");\'>" + 
		letter + "<\/span>");
	} // printLetter
	function printLetters(string) {
		var i;
		var splitResult = string.split(" ");
		for (i = 0; i < splitResult.length; i += 1) {
			printLetter(splitResult[i]);
			document.write("&nbsp; &nbsp;");
		}
	} // printLetters
';
my %books = (
	202583 => 'Volume 1',
	202584 => 'Volume 2',
	202585 => 'Volume 3',
	202586 => 'Volume 4',
	202587 => 'Volume 5',
	202588 => 'Volume 6',
	202589 => 'Volume 7',
	202590 => 'Volume 8',
	202591 => 'Volume 9',
	202592 => 'Volume 10',
	202593 => 'Volume 11',
	202594 => 'Volume 12',
	202595 => 'Volume 13',
	202596 => 'Volume 14',
	202597 => 'Volume 15',
);
my $css = '
	pre {
		font-family: "Times New Roman", Times, serif;
		font-size: 125%;
		font-weight: 600;
		display: block;
		border: none;
		background-color: #f8f8f8;	
	}

	h1 {
		font-size: 350%;
		text-align: center;
	}

        h2 {
		text-align: center;
	}

	a {
		text-decoration: underline;
	}

	.container {
		padding-bottom: 10px;
	}

	.edit-view {
		display: inline;
	}

	.top a {
		text-decoration: none;
		color: #fff;
	}

	.top a:hover {
		color: #c6c9c3;
	}

	.login {
		color: #ffffff;
	}
	
	input#edit-button {
		background: #3e9cbf;
		color: #ffffff;
		border-radius: 3em;
		border: none;
	}

	.search-results {
		height: 700px;
		overflow: scroll;
	}

	input#entry {
		border-radius: 3em;
		border: none; //1px solid;
		width: 350px;
		height: 30px;
		padding: 0 15px 0 15px;
	}

	input[type="text"] {
		font-size: 18px;
		color: #000;
	}

	input:focus,
	select.focus,
	textarea:focus,
	button:focus {
		outline: none;
	}
';

my $blue = '<span style="color:#3e9cbf">';
my $green = '<span style="background-color:#AAFFAA">';
my $yellow = '<span style="background-color:yellow">';
my $pink = '<span style="background-color:pink">';
my $red = '<span style="color:red">';

# If a word is found, it is displayed with this span style
my $foundWord = '<span style="color:#3e9cbf;font-weight:bold;font-size:110%;text-decoration:underline;">'; #comment

# variables
my $lineLength;

sub makePattern {
	my ($string) = @_;
	my @answer = ();
	for my $piece (split(/(?=\X)/, $string)) {
		# print "Piece: $piece\n";
		push @answer, $piece;
		push @answer, "[\\pM]*([־―]\n)?";
	} # each piece
	return join('', @answer);
} # makePattern

my $text;

sub readText {
	# print "reading text ... \n";
	open BUCH, $dataFile or die("Can't open $dataFile.  Stopped");
	binmode BUCH, ":utf8";
	$/ = undef; # slurp mode
	$text = <BUCH>;
	close BUCH;
	# print "done: " . (length($text)) . " bytes\n";
} # readText

sub oneWord {
	my ($keyword) = @_;
	# print STDERR "keyword is $keyword\n";
	readText() unless defined($text);
	print "<div class='row col-md-offset-3 col-md-6'>";
	print h2($searchResultsFor . $keyword), br();
	print "</div>";
	my $hits = 0;
	my $pattern = makePattern($keyword);
	my ($book, $page);
	my $numResultPages;
	print "<div class='row search-results col-md-offset-4 col-md-4'>"; # creates div for returning search results;
	for my $entry (split //, $text) {
		if ($entry =~ s/tmp\/nybc(\d+).*\/(pg|nybc\d+_orig)_(\d+)//) {
			($book, $page) = ($1, $3);
		}
		# $entry =~ s/־\s*\n\s*//g; # remove hyphens at end of lines
		if ($entry =~ /$pattern/) { # got a hit; it's worth some effort
			$entry =~ s/reading picture//g; # junk
			$entry =~ s/\&/\&amp;/g;
			$entry =~ s/</\&lt;/g;
			$entry =~ s/>/\&gt;/g;
			$entry =~ s/\b($pattern)\b/$yellow$1<\/span>/g or
				$entry =~ s/($pattern)/$foundWord$1<\/span>/g;
			print "<div class='row'>
			<form 
				action='user.php'
				method='post' enctype='multipart/form-data'>
				<input type='hidden' name='book' value='$book'>
				<input type='hidden' name='page' value='$page'>
				<a target='_blank'
                        href='https://archive.org/stream/nybc$book#page/n$page/mode/1up'>$books{$book},
                        pg. $page<\/a>
				<button type='submit' class='btn btn-primary btn-xs'>edit</button>
			</form>
			<p><pre>$entry</pre></p></div><br/>\n"; # print an entry that is found
			$hits += 1;
		} # a hit
		
   		if ($hits >= $resultsPerPage) {
			return; # stop printing results after the 10th result is displayed
		}
	} # one side
	if ($hits == 0) {
            print "<h2>No results found</h2>";
        }

	print "</div>"; # close div
	
} # oneWord

sub doSearch {
	my ($param, @args);
	$param = untaint(param('keys'));
	if (defined($param)) {
		@args = split(/\s+/, $param);
	} else {
		@args = @ARGV;
	}
	if (@args) {
		oneWord(standardize(join(' ', @args)));
	} 

	# while (@args) {
		# my $keyword = shift @args;
		# oneWord($keyword);
	# }
} # doSearch

sub init {
	my ($title);
	binmode STDOUT, ":utf8";
	binmode STDERR, ":utf8";
	binmode STDIN, ":utf8";
	$lineLength = (defined($1) ? $1 : 80) - 1;
	$title = untaint(param('keys'));
	$title = 'שלום עליכם' unless defined($title) and $title ne '';
	my $analytics = `cat analytics.txt`;
	# my $analytics = ''; # disabled for now
	print header(-type=>"text/html", -expires=>'-1d', -charset=>'UTF-8') .
		start_html(-encoding=>"UTF-8",
			-title=>$title,
			-dir=>'rtl',
			-style=>{code => '.clickable:hover {background-color:lime;}'},
			-head=>Link({-rel=>'stylesheet',
		                  -href=>'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'}),
			-script=>$analytics . $javaScript,
			-style=>{-code=>$css},
		) .
	
		$form; # display first form
} # init

sub finalize {
	# $form =~ s/entry/entry1/g;
	print br(), end_html(), "\n";
	close BUCH;
} # finalize

sub untaint {
	my ($string) = @_;
	$string =~ s/[^\w\s־]//g; # only alphabetic characters make sense.
	$string =~ /(.*)/; # remove taint
	$string = $1;
	# print STDERR "string [$string]\n";
	return ($string);
} # untaint

sub standardize { # to combining, not precomposed
	my ($data) = @_;
	$data =~ s/וו/װ/g;
	$data =~ s/וי/ױ/g;
	$data =~ s/\bיי(?=\P{M})/ייִ/g;
	$data =~ s/יי/ײ/g;
	$data =~ s/ײַ/ײַ/g;
	$data =~ s/ײִ/ייִ/g;
	$data =~ s/ױִ/ויִ/g;
	$data =~ s/וױי(?=\P{M})/װײ/g;
	$data =~ s/­//g; # soft hyphenation mark
	$data =~ s/שׂ/שׂ/g; # combining, not precomposed
	$data =~ s/בּ/בּ/g; # combining, not precomposed
	$data =~ s/כּ/כּ/g; # combining, not precomposed
	$data =~ s/וּ/וּ/g; # combining, not precomposed
	$data =~ s/אָ/אָ/g; # combining, not precomposed
	$data =~ s/אַ/אַ/g; # combining, not precomposed
	$data =~ s/תּ/תּ/g; # combining, not precomposed
	$data =~ s/פֿ/פֿ/g; # combining, not precomposed
	$data =~ s/פּ/פּ/g; # combining, not precomposed
	return($data);
} # standardize

init();
doSearch();
finalize();

