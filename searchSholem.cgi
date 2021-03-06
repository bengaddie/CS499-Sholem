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

my $hitLimit = 10; # number of hits we are willing to print
my $dataFile = "/u/peg-d4/TMP/raphael/sholem-aleykhem-ale/output.uyid";
my $form = "
	<form
	action=\"" . $0 . "\"
	method=\"post\" enctype=\"multipart/form-data\">
		זוכװערטער:
		<input type=\"text\" name=\"keys\" size=\"40\"
			id=\"entry\"
			onmouseover=\"getElementById('entry').focus()\"
			/>
		(אָדער מיט פּינטעלעך אָדער אָן פּינטעלעך)
		<br/>
		נוצלעכע אותיות (צו קליקן): 
		<script type=\"text/javascript\">
			printLetters('א אַ אָ ב בֿ ג ד ה ו װ ױ ז ח ט י יִ ײ ײַ כ כּ ך ל מ ם נ ' +
				'ן ס ע פּ פֿ פ ף צ ץ ק ר ש שׂ תּ ת ־');
		</script>
		<input type=\"submit\" value=\"זוך!\"
			style=\"background-color:#AAFFAA;\"/>
		<input type=\"reset\" value=\"לײדיק אױס\"
			style=\"font-size:90%; background-color:#FFAAAA;\"/>
	</form>
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
		font-size: 100%; 
		font-weight: bold;
	}
	h1 {
		font-size: 300%;
		text-align: center;
	}
';

my $blue = '<span style="color:blue">';
my $green = '<span style="background-color:#AAFFAA">';
my $yellow = '<span style="background-color:yellow">';
my $pink = '<span style="background-color:pink">';
my $red = '<span style="color:red">';

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
	print h2($keyword), br();
	my $hits = 0;
	my $pattern = makePattern($keyword);
	my ($book, $page);
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
				$entry =~ s/($pattern)/$pink$1<\/span>/g;
			print "<a target='_blank'
			href='https://archive.org/stream/nybc$book#page/n$page/mode/1up'>$books{$book},
			pg. $page<\/a>
			<form 
				action='https://www.cs.uky.edu/~raphael/yiddish/editSholem.cgi'
				method='post' enctype='multipart/form-data'>
				<input type='hidden' name='book' value='$book'>
				<input type='hidden' name='page' value='$page'>
				<input type='submit' value='רעדאַגיר די זײַט'>
			</form>
			<br/><pre>$entry</pre>\n";
			$hits += 1;
		} # a hit
		if ($hits >= $hitLimit) {
			print "<hr\/><p>${red}שױן גענוג! 
			מער װי $hitLimit משלים פֿאַר $blue$keyword<\/span> גיב איך ניט.<\/span><\/p>\n";
			return;
		}
	} # one side
} # oneWord

sub doSearch {
	my ($param, @args);
	$param = untaint(param('keys'));
	print p("אַ דאַנק דעם נאַציאָנאַלן ביכער־צענטראַל פֿאַרן סקאַנירטן טעקסט!");
	print p("OCR: רפֿאל פֿינקל");
	if (defined($param)) {
		@args = split(/\s+/, $param);
	} else {
		@args = @ARGV;
	}
	if (@args) {
		oneWord(standardize(join(' ', @args)));
	} 
	print "<a style='background-color:#AAFFAA;' onclick='scroll(0,0);'
		>צוריק צום קאָפּ</a>\n";
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
			-script=>$analytics . $javaScript,
			-style=>{-code=>$css},
		) .
		h1("ale verk fun Sholem Aleykhem") .
		h1("אַלע װערק פֿון שלום עליכם") .
		$form . br() . hr() . br();
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

