<!--
File description:




 -->
<!DOCTYPE HTML>
<html>
  <head>
   <meta charset="utf-8">
  </head>
  <body>
    <h1> Admin page </h1>
     <br>
     <h2> Request to Edit </h2>

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



/*****************************
    Start Handling new contributers request */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept']))
{

	echo $_POST['email'] . 'Is accepted <br>';
	/* 
	  open new file called emails.txt
	$file = fopen('email.txt',a+);
	  write new email to the end of the file
	  close the file
	  open info.txt 
	  remove line that starts with the email
	  read in entire file.
	  then recopy the file back so that there are no empty lines in file. 
	  close file
	*/
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deny']))
{

  $email = $_POST['email'];
  // XML attempt

  //$xml = simplexml_load_string("info.xml");
  $doc = new DOMDocument;;
  $doc->load("info.xml");

  $xpath = new DOMXPath($doc);

  foreach($xpath->query("/info/user[Email='".$email."']") as $user)
  {

    $user->parentNode->removeChild($user);

  }
  // Makes the outout  look neat so the xml file stays properly formatted.
  $doc->formatOutput = true;
  $doc->save('info.xml');

}



/*   handles if there are requests in file.txt, only need to make page look better.
	0 - there are request to edit. (info.txt is not empty)
	1 - thare are NOT any request to edit. (info.txt is empty) 
*/
$noRequest = 0;
$count = 0;

// opens file that holds the emails and reasons why the should be allowed to edit
$xml = simplexml_load_file("info.xml");
// if the file opens then you procede there just to keep the page from crashing in the off chance the file is misisng. 


        foreach($xml->children() as $user)
        {
            echo '<form action="" method="POST">';
        	echo $user->Email . ": ";
		echo $user->Reason; 
                echo '<br>';
        	echo '<input type="submit" name="accept" value="Accept">    ';
	        echo '<input type="submit" name="deny" value="Deny"><br>';
		echo '<input type="hidden" name="email" value="'.$user->Email.'">';
		echo '<input type="hidden" name="line" value="'.$user->Reason.'">';
		echo '</form>';
            }


/*   End of Handling new contributer request     */



?>
<!-- Moved farther up because html starts earlier now. 
<!DOCTYPE HTML>
<html>
  <head>
   <meta charset="utf-8">
  </head>
  <body>
    <h1> Admin page </h1>
     <br>
-->     
     <?php
       echo "<br/><br/>Set the location of the OCR files to be crowdsourced:<br>";
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

