<!DOCTYPE html>
<?php
if(isset($_POST['submit']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    session_start();
    $file = simplexml_load_file("user.xml");
    for($i = 0; $i < count($file); $i++)
    {
      $userEmail = $file->user[$i]->Email;
      $userPassword = $file->user[$i]->Password;
      $userName = $file->user[$i]->First;
      $userPermission = $file->user[$i]->Permission;
    }

    if(($email == $userEmail) &&  ($password == $userPassword))
    {
      $_SESSION['loged_in'] = true;

      $_SESSION['email'] = $userEmail;

      $_SESSION['name'] = $userName;

      $_SESSION['permission'] = $userPermission;


      //send to the correct page first. 
      exit(header("Locationaa:searchSholem.cgi"));
    }
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>login page</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <link href="login.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <form method="POST" action="sholem_login.php">
        <input type="text" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Sign in">
        </form>
    </body>
</html>
