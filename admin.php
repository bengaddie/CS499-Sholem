
<!--
File description:




 -->


<?php
//The following php file is needed for getters/setters of directories, running remote server commands,  
require_once "utility.php";
require_once "globals.php";


/* Start of Added By Ben */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept']))
{

        $email = $_POST['email'];
        //echo $email;
        //echo '<br>';
        $sFile = 'applicant.xml';
        $dFile = 'user.xml';

        $source = simplexml_load_file($sFile);
        $destination = simplexml_load_file($dFile);

        foreach ($source->xpath("/info/user[Email='".$email."']") as $user2)
        {
            $newUser = $destination->addChild('user');
            $newUser->addChild('Email', $user2->Email);
            $newUser->addChild('First', $user2->First);
            $newUser->addChild('Last', $user2->Last);
            $newUser->addChild('Password', $user2->Password);
            $newUser->addChild('Reason', $user2->Reason);
            $newUser->addChild('Permission', $user2->Permission);
        }

        $destination->saveXML($dFile);

        $doc = new DOMDocument;
        $doc->load("applicant.xml");
        $xpath2 = new DOMXPath($doc);

       foreach($xpath2->query("/info/user[Email='".$email."']") as $user3)
       {

        $user3->parentNode->removeChild($user3);
       }


       $doc->save('applicant.xml');

}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deny']))
{

  $email = $_POST['email'];
  // XML attempt

  //$xml = simplexml_load_string("info.xml");
  $doc = new DOMDocument;
  $doc->load("applicant.xml");

  $xpath = new DOMXPath($doc);

  foreach($xpath->query("/info/user[Email='".$email."']") as $user)
  {

    $user->parentNode->removeChild($user);

  }
  // Makes the outout  look neat so the xml file stays properly formatted.
  $doc->formatOutput = true;
  $doc->save('applicant.xml');

}



/*   handles if there are requests in file.txt, only need to make page look better
        0 - there are request to edit. (info.txt is not empty)
        1 - thare are NOT any request to edit. (info.txt is empty)
*/
$file = "applicant.xml";
// opens file that holds the emails and reasons why the should be allowed to edit
$xml = simplexml_load_file($file);
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




<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
<h1> Admin page </h1>
<br>
<?php
echo "<br><h2>Recent User edits:</h2> <br>";
	//echo $locationOfLogFiles."<br>"; 
    $logs = parseLogFiles();
    //echo "<br> Number of logs files: ".sizeof(($logs))."<br>";

	for ($i = 1 ; $i < sizeof($logs); $i++){
	$userChange = "/mounts/u-zon-d2/ugrad/mtlank2/HTML/499/logs/".$logs[$i][0]."~".$logs[$i][1]."~".$logs[$i][2]."~".$logs[$i][3]."~diff";  
	//echo $userChange."<br>";
	
	if (!($i % 2 == 0)){
    echo $logs[$i][0]." changed book# ".$logs[$i][1]." , page# ".$logs[$i][2]." on ".date("l jS \of F Y h:i:s A", $logs[$i][3])."<br>"; 
	echo 
		"
		<textarea rows='25' cols='75' name='editedText'>
		".
		file_get_contents($userChange)  
		.
		"
		</textarea>

		<form action='validate.php' method='post'>
		  <input type='hidden' name='time' value='".time()."' />
		  <input type='hidden' name='user' value='".$logs[$i][0]."'>
		  <input type='hidden' name='book' value='".$logs[$i][1]."'>
		  <input type='hidden' name='page' value='".$logs[$i][2]."'>
		  <input type='hidden' name='time' value='".$logs[$i][3]."'>
		  Press Accept to allow change.  Press Deny to revert the change.
		  <button name='commitOcrChange' type='submit' value='commitOcrChange'>Accept</button>
		  <button name='denyOcrChange' type='submit' value='denyOcrChange'>Deny</button>
		</form>
		<br><br><br><br><br><br>
		";
	}
}



?>

<?php
//Admin changes path of Sholem OCR Files.
echo "<br><h2>New OCR file path</h2><h4>Set the Multilab absolute path location of the OCR files to be crowdsourced.  NOTE: The path should be set up to parent directory of the books you wish to crowdsource.  
Example: path/to/ocrFilesDirectory/book/page *IS NOT* the path you should set.  Instead, using the same example, the correct path to set would be: path/to/ocrFilesDirectory </h4>";
?>
<form action="validate.php" method="post">
Absolute Path (i.e use pwd command): <input type="text" name="ocrFilesPath"><br>
<input type="submit">
</form>

</body>
</html>
