<!--
File description:





 -->

<?php

require_once "globals.php";

/*
TO-DO:


-tell finkel the commit times are down. ask him to play with the commits/changes himself.  ask him if he wants changes most recent to most late, or vice versa. ask him to make multiple changes for testing

-is there a need to store a diff file? probably not! why not just execute a diff command at the admin relative to the live copy of the repo!
THIS COULD SAVE U THE HASSLE OF SAVING ~diff !! might be hard tho. find out how to apply diff first

--use 1,2,3,4,5,6, as book numbers
-make file opens and closes error proof
-allow auto commit for users
-develope test cases for different series of changes. should change be presented most recent first?
-figure out how to do user login, ie do SESSIONS
-pass success msgs from validate back to user and admin
-develop gameplan for integration testing, ie ask finkel to go live

-change loop logic under for loop for recent changes. not stable logic. perhaps make sub directories


-make getters and setters for ALL directories PATH uses, even the ssh command
-getter for all diff file changes
-getter: most recent diff file w.r.t singular file




POSSIBLE BUGS:



INFO:	
This the path to the actual OCR files:	
/mounts/u-al-d3/csfac/raphael/projects/ocr/SholemAleykhem
/mounts/u-al-d3/csfac/raphael/projects/ocr/SholemAleykhem
/mounts/u-al-d3/csfac/raphael/projects/ocr/SholemAleykhem
/mounts/u-al-d3/csfac/raphael/projects/ocr/SholemAleykhem
*/












//This command executes a string passed command on the remote Multilab server, and returns the result
function executeRemoteCommand($command){
	putenv("PATH=/bin:/usr/local/bin:/usr/local/bin/csrepo;/usr/local/gnu/bin:/usr/openwin/bin:/usr/local/X11R6/bin:/usr/ccs/bin:/usr/bin:/usr/ucb:/etc:/usr/local/java/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/mounts/u-zon-d2/ugrad/mtlank2/HTML/scripts:/usr/bin/expect:/usr/bin/X11/expect:/usr/share/man/man1/expect.1.gz:/usr/lib/x86_64-linux-gnu/libserf-1.so.1");
	putenv("LD_LIBRARY_PATH=/lib:/usr/lib:/usr/lib/x86_64-linux-gnu:/usr/lib/x86_64-linux-gnu/libserf-1.so.1");
	//echo "<br>debug: ".'2>&1 ssh mtlank2@penstemon.cs.engr.uky.edu "'.$command.'"'."<br>";
	//return passthru('ssh mtlank2@penstemon.cs.engr.uky.edu "'.$command.'"');
	//return passthru('ssh mtlank2@iris.cs.engr.uky.edu "'.$command.'"');
	//return shell_exec('2>&1 ssh mtlank2@penstemon.cs.engr.uky.edu "'.$command.'"');
	//return shell_exec('ssh mtlank2@iris.cs.engr.uky.edu "'.$command.'"');
	return shell_exec('ssh mtlank2@penstemon.cs.engr.uky.edu "'.$command.'"');
}


//This command takes the ABSOLUTE (pwd) path of where the OCR files are located and stores this string in a file
function setOcrFilesPath($directoryLocation){
file_put_contents("constants/ocrFilesPath.txt" ,$directoryLocation); 
}


//This command stores all files in the logs directory into an array of arrays.  The format of the containing arrays is [name][book][page][time][diff]
function parseLogFiles (){
	global $locationOfLogFiles;
	
	$files = scandir($locationOfLogFiles);
	$parsedLogFiles = array();
	for ($i = 2 ; $i < sizeof($files); $i++){
		array_push($parsedLogFiles, explode("~", $files[$i]));
	}
	return $parsedLogFiles;
	
	
	

	
	/*
	//echo $locationOfLogFiles."<br>"; 
	//Extract the list of all the log files as a string
	$locationOfLogFiles =realpath(__DIR__)."/"."logs";
	//echo $locationOfLogFiles."<br>"; 
	$getListOfLogFiles = executeRemoteCommand("cd ".$locationOfLogFiles."; ls");
	//echo "The results: ".$getListOfLogFiles."<br>";
	
	//Store the individual titles of log files into an array
	$logFileTitles = explode('_' ,$getListOfLogFiles);
	
	//echo "Size of log file titles ".sizeof($logFileTitles)."<br>";
	//echo $logFileTitles[1]."<br>";

	//Parse each segment of each file title in the following format:  0=user, 1=book, 2=page, 3=time, 4=diff
	$parsedLogFiles = array();
	for ($i = 0 ; $i < sizeof($logFileTitles); $i++){
		array_push($parsedLogFiles, explode("~", $logFileTitles[$i]));
	}
	
	return $parsedLogFiles;
	*/
}









?>