<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>HHAC Login</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>
<body>
<?php
require("core.php");
$usr_row = authenticate();
if(!$usr_row) 
{
  echo "authenticate fail.<br />";
  echo "5秒后重定向到登录页面<br >";
  echo "<script type='text/javascript'>setTimeout(\"self.location='/hhac/login.php'\", 5000)</script>";
}
else
{
  $session_id = mk_session_id();
  session_start();
  setcookie("_session_id", $session_id);
  var_dump($usr_row);
  echo "Login Success!<br >";
  echo "session_id: $session_id<br />";
  echo "5秒后重定向到视频列表<br>";
  echo "<script type='text/javascript'>setTimeout(\"self.location='/hhac/'\", 5000)</script>";
}


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

</body>
</html>
