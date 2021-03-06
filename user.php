<!--
File description:




 -->



<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<h1> User makes crowd-source changes page: </h1>
<br>
<?php
/*
My Dev Repo: 
https://499sholem.googlecode.com/svn/trunk/

PATH to my mulitlab sandbox: 
/mounts/u-zon-d2/ugrad/mtlank2/HTML/499sholem

*/







//The following php file is needed for getters/setters of directories, running remote server commands,  
require "utility.php";

//VARIABLES:

//This variable holds the directory (the book) of the containing OCR file
$book = $_POST['book'];

//This variable holds the exact OCR file, ie the page
$page = $_POST["page"];

//This variable holds the name of the user that edited the file
$user = "someGuy";

//This variable holds the absolute Multilab file path of the where the currently edited user file is, in other words this is where the file being edited is stored on the server. (this will have to be passed via cgi search function)
$locationOfOcrFile = file_get_contents('constants/ocrFilesPath.txt').$book."/".$page;  //the concatenation of the 0016 hopefully will come from the search function

//This holds the absolute Multilab file path of where the crowd-sourced changes will be saved
$locationOfLogFiles = realpath(__DIR__."logs/"); //Make this relative once you know the setup of your system


echo $locationOfOcrFile."<br>".$locationOfLogFiles."<br>";

//POST EXECUTION (the main heavy-lifting): 

//The following POST method will apply the edited changes that the user made after they click on the button to make those changes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ocrMakeChange'])){


	//Make a new text file named userEditsFileAtTime, which is comprised of the contents of the file before the user made any changes
	$preEditFileName = $user."_".$book."_".$page."_".time();
	echo $preEditFileName."<br>";


	file_put_contents($locationOfLogFiles. $preEditFileName, file_get_contents($locationOfOcrFile));

	//Get the text that was just edited by the user
	$textChangeFromUser = $_POST['editedText'];
	
	//Replace the main repo OCR file with the newly edited text
	file_put_contents($locationOfOcrFile, $textChangeFromUser);
	
	//Now we have 2 files.  The old file (pre-edit without changes) and the new file (post-edit with changes) 
	//Run a diff command on the old file versus the newly edited file. This will give us the exact change(s) made. Save as a diff file
	$command = "diff ".$locationOfOcrFile." ".$locationOfLogFiles.$preEditFileName." > ".$locationOfLogFiles."userEdits0016AtTimeStampChange";
	echo $command;
	echo executeRemoteCommand($command);
}








//NORMAL EXECUTION:

//Display the contents of the file that the user wants to make changes to
echo "
<br>
<br>
This is where the user makes changes: 
<br>".
"
<form action='user.php' method='POST'>
	<textarea rows='25' cols='50' name='editedText'>
	".
	file_get_contents($locationOfOcrFile)
	.
	"
	</textarea>
	<button type='submit' name='ocrMakeChange'>Submit OCR Changes</button>
</form>
";


?>
</body>
</html>


