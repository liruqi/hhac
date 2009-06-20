<?php
require("core.php");
$usr_row = authenticate();
if(!$usr_row) 
{
   //echo "authenticate fail.";
    exit();
}
$session_id = mk_session_id();
session_start();
setcookie("_session_id", $session_id);
var_dump($usr_row);
echo "Login Success!<br>";
echo $session_id;


/*
 * Read Cookie:
 * echo $_COOKIE["TestCookie"];
 * echo $HTTP_COOKIE_VARS["TestCookie"];
 *
 * Write Cookie:
 * setcookie("TestCookie", $value);
 * setcookie("TestCookie", $value, time()+3600);  // expire in 1 hour
 * setcookie("TestCookie", $value, time()+3600, "/~rasmus/", ".example.com", 1);
*/

?>
