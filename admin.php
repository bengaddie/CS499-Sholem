<!--
File description:




 -->


<?php

//The following php file is needed for getters/setters of directories, running remote server commands,  
require "utility.php";










//Set the new path of the OCR files you want crowdsourced
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ocrFilesPath'])){

	//$adminInfo = gatherAdminInfo();
	//adminInfo[ocrFilesPath] = $_POST['ocrFilesPath']
	setOcrFilesPath($_POST['ocrFilesPath']);
	echo "<br>OCR Files path set<br>";
} 


//Commit a crowdsourced change for all changes.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commitOcrChange'])){
	//executeRemoteCommand("svn commit"." ".file_get_contents("constants/ocrFilesPath.txt")."/0016");

	//while loop to keep generate the list of changes whith a check box 
	

	$command = (string)"cd ".file_get_contents("constants/ocrFilesPath.txt").";svn commit -m 'myVictoryMsg'";
	echo $command;
	echo executeRemoteCommand($command);
}


//Deny a crowdsourced change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['denyOcrChange'])){

	file_put_contents(file_get_contents('constants/ocrFilesPath.txt')."/0016", file_get_contents("logs/userEdits0016AtTimeStamp"));
	
	//executeRemoteCommand("svn commit"." ".file_get_contents("constants/ocrFilesPath.txt")."/0016");
} 



?>




<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">




















</head>
<body>
<h1> Admin page </h1>
<br>







<?php
echo "<br>Set the location of the OCR files to be crowdsourced:<br>";
?>
<form action="admin.php" method="post">
OCR change1: <input type="text" name="ocrFilesPath"><br>
<input type="submit">
</form>








<?php
echo "<br>Recent Changes: <br>";



echo file_get_contents("/mounts/u-zon-d2/ugrad/mtlank2/HTML/499_1_sandbox/499_1/logs/userEdits0016AtTimeStamp.diff");

//Get, parse, and present all diff files
	//The diff files for the same file should be present in chronological order
	//If there are multiple diff files w.r.t the same OCR file being modified, then only present the most recent diff

//Each diff-change being presented should have the ability to either commit or deny that change, it should also show who made that change
	
?>

<form action="admin.php" method="post">
OCR commit change1: <input type="text" name="commitOcrChange"><br>
<input type="submit">
</form>
<form action="admin.php" method="post">
OCR deny change1: <input type="text" name="denyOcrChange"><br>
<input type="submit">
</form>






</body>
</html>
