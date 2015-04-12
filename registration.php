<!DOCTYPE html> 
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Registration</title>
    </head>
    <body>
<?php

 if(isset($_POST['submit_btn']))
 {
  // Store the passed values into there own variables. (Easier to work with) 
  $first = $_POST['first'];
  $last = $_POST['last'];
  $username = $_POST['email'];
  $reason = $_POST['reason'];
  $password = $_POST['password'];
  
  // Store the name of the XML file you want to store the information into.
  //  -- So you can just change this variable if you want to store info in diffrent file.
  $file = 'applicant.xml';

  // Create a new DOM document
  $dom = new DOMDocument();
  
  // Load the XML file into the DOM document that was just created. 
  $dom->load($file) or die("Error loading XML file"); // rename xml file
  
   // This just makes the output format correctly, not explicitly used 
  // -- but nice to have if you need to output any of the information. 
  $dom->formatOutput = true;
  
  // Get the document element, the root tag in the XML file. 
  // -- Which is currently <info> for the XML file being used.  
  $root = $dom->documentElement;

  /* Adding a new email */
  // Creates the <Email> tag.
  $newEmail = $dom->createElement("Email");
  // Adds values between the tags <Email>$username</Email>. 
  $emailText = $dom->createTextNode($username);
  // Appends the text to the <Email> tag. 
  $newEmail->appendChild($emailText);
  
  /* Adding a new first name */
  // Create the <First> tag.
  $newFirst = $dom->createElement('First');
  // Adds the actual first name between the tags <First>$first</First>.  
  $firstText = $dom->createTextNode($first);
  // Appends the text to the <First> tag. 
  $newFirst->appendChild($firstText);
  
  // Create the <Last> tag
  $newLast = $dom->createElement('Last');
  // Adds the actual first name between the tags <Last>$last</Last>.
  $lastText = $dom->createTextNode($last);
  // Appends the text to the <Last> tag. 
  $newLast->appendChild($lastText);
  
  // Create the <Password> tag
  $newPass = $dom->createElement('Password');
  // Adds the actual first name between the tags <Password>$pass</Password>.
  $passText = $dom->createTextNode($password);
  // Appends the text to the <Password> tag. 
  $newPass->appendChild($passText);
  
  // Create the <Reason> tag
  $newReason = $dom->createElement('Reason');
  // Adds the actual first name between the tags <Reason>$reason</Reason>.
  $reasonText = $dom->createTextNode($reason);
  // Appends the text to the <Reason> tag.
  $newReason->appendChild($reasonText);
 
  $newPermisson = $dom->createElement('Permission');
  $permissionText = $dom->createTextNode('0');
  $newPermisson->appendChild($permissionText);
// Appends all the previos to user so that it can be stored. 
  $user = $dom->createElement("user");
  $user->appendChild($newEmail);
  $user->appendChild($newFirst);
  $user->appendChild($newLast);
  $user->appendChild($newPass);
  $user->appendChild($newReason);
  $user->appendChild($newPermisson);
// now appeneds user to root which is <info>
  $root->appendChild($user);

// save changes to the DOM file to the original.
  $dom->save($file);

// send a success mesage.
  echo '<h1> Thank You </h1> <br> <p>Your request has been sent. Dr. Finkle will contact you as soon as possible </p>';

  }

// used to control what is displayed to the screen. 
  else
  {
?>
 
<!-- form to collect all the applicants information -->
 <form action = "registration.php" method="POST">
      <h1> Please enter your information and a (short) reason why you want to work on this project</h1>
        <p>
         <!-- labels used to hold the users information and pass it using the submit button -->
          <label>First:</label><input type = "text"  name = "first" /><br>
          <label>Last:</label><input type = "text"  name = "last" /><br>
          <label>Email:</label><input type = "text"  name = "email" /><br>
          <label>Password:</label><input type = "password" name = "password" /> 
          <br/>
	<br/>
<!-- Text area for the applicant to enter why they should be allowed to edit the works of Sholem. -->
    <textarea name="reason" cols="50" rows="5" placeholder="Enter reason here..."></textarea>
    <br />



          <br/>
        </p>
<!-- Submit button to send the information collected back to this page. It is caught in the above if statement.-->
      <input type = "submit" name="submit_btn" id = "submit" value = "submit"/>
    </form>
<?php
}
?>
    </body>
</html>
