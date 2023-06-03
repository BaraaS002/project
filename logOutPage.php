<?php
session_start();
// delete values from session and destroy is then go to log in page
unset($_SESSION['userID']);
unset($_SESSION['timeSession']);
session_destroy();
header("location:logInPage.php");
