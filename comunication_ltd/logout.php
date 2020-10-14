<?php
require_once("include/functions.php");
require_once("include/sessions.php");
// Close the session and reset all session variables , redirect to login page.
$_SESSION["userId"]=null;
$_SESSION["userName"]=null;
$_SESSION["email"]=null;
session_destroy();
redirect("login.php");
?>
