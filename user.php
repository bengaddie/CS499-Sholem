<!--
File description:




 -->



<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
<div class="top container col-md-12 text-center bg-primary">
    <h1>אַלע װערק פֿון שלום עליכם</h1>
</div>
<div class="current-edit container text-center">
    <h2>דערווייַל עדיטינג: Volume 1, Book 16</h2>
</div>


<?php
/*
My Dev Repo: 
https://499sholem.googlecode.com/svn/trunk/

PATH to my mulitlab sandbox: 
/mounts/u-zon-d2/ugrad/mtlank2/HTML/499sholem

*/



//The following php file is needed for getters/setters of directories, running remote server commands,  
require_once "utility.php";
require_once "globals.php";








//VARIABLES:
//This variable holds the directory (the book) of the containing OCR file
$book = "book";//$_POST['book'];

//This variable holds the exact OCR file, ie the page
$page = "0016";//$_POST["page"];

//This variable holds the name of the user that edited the file
$user = "someGuy";

//This variable holds the absolute Multilab file path of the where the currently edited user file is, in other words this is where the file being edited is stored on the server. (this will have to be passed via cgi search function)
$locationOfOcrFile = $locationOfAllOcrFiles."/".$book."/".$page;
//echo "Location of ocr file: ".$locationOfOcrFile."<br>";





//NORMAL EXECUTION:
//Display the contents of the file that the user wants to make changes to
echo "
<div class='row'>

    <div class='col-md-offset-1 col-md-5 text-center'>
        <form>
            <div class='form-group'>
                <textarea class='form-control text-right' rows='35' readonly>
                " .
	            file_get_contents($locationOfOcrFile)
	            . "
                </textarea>
            </div>
        </form>
	<button type='button' class='btn btn-primary btn-lg'>סוויטש מיינונג</button>
    </div>

    <div class='col-md-5 text-center'>
        <form action='validate.php' method='POST'>
            <div class='form-group text-right'>
                <textarea class='form-control text-right' rows='35' name='editedText'>
	            " .
	            file_get_contents($locationOfOcrFile)
	            . "
	        </textarea>
			<input type='hidden' name='user' value='".$user."'>
		    <input type='hidden' name='book' value='".$book."'>
		    <input type='hidden' name='page' value='".$page."'>
            </div>
			<button type='submit' name='ocrMakeChange'>Submit OCR Changes</button>
        </form>
        <button type='button'
                class='btn btn-primary btn-lg' 
                data-toggle='modal'
                data-target='.bs-example-modal-lg'>פאָרלייגן</button>
        <div class='modal fade bs-example-modal-lg' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
    	        <div class='modal-content'>
		    ...
		</div>
	    </div>
	</div>
    </div>
</div>

<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'></script>
";


?>


</body>
</html>


