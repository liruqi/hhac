<?php
$user_id = 0;
function authenticate($name, $pass)
{
  global $user_id;

  $db_host = "localhost";
  $db_name = "hhac";
  $db_user = "hhac";
  $db_pass = "iamharmless";
 
  // establish connection
  $db_con = mysql_connect($db_host, $db_user, $db_pass);
  if($db_con == FALSE)
    throw new Exception("Cannot connect to database.");

  // switch to $db_name
  $db_selected = mysql_select_db($db_name, $db_con);
  if($db_selected == FALSE)
    throw new Exception("Cannot switch to $db_name.");
 
  // send a SELECT query
  $_name = mysql_real_escape_string($name);
  $_pass = mysql_real_escape_string($pass);
  $sql = "SELECT id FROM users WHERE name='$_name' and password='$_pass'";
  $res = mysql_query($sql, $db_con);
  if($res == FALSE)
    throw new Exception("SQL query failed.");

  // process the very query result
  $usr_row = mysql_fetch_array($res, MYSQL_ASSOC);
  mysql_close($db_con);

  // now, give a predication
  if($usr_row == FALSE)  // cannot fetch an array
    return FALSE;
  else
  {
    $user_id = $usr_row["id"];
    return TRUE;
  }
}

/*

*/
function mk_session($name, $ip)
{
  global $user_id;

  $db_host = "localhost";
  $db_name = "hhac";
  $db_user = "hhac";
  $db_pass = "iamharmless";
 
  // establish connection
  $db_con = mysql_connect($db_host, $db_user, $db_pass);
  if($db_con == FALSE)
    throw new Exception("Cannot connect to database.");

  // switch to $db_name
  $db_selected = mysql_select_db($db_name, $db_con);
  if($db_selected == FALSE)
    throw new Exception("Cannot switch to $db_name.");
 
  // send a INSERT query
  $_name = mysql_real_escape_string($name);
  $date = date("Y-m-d H:i:s");
  $seed = $_name . $date . $ip;
  $sessid = md5($seed);
  $sql = "INSERT INTO sessions (sessid, user_id, address) VALUES('$sessid', $user_id, '$ip')";
  $res = mysql_query($sql, $db_con);
  if($res == FALSE)
    throw new Exception("SQL query failed.");

  // close datebase connection
  mysql_close($db_con);

  // now, return session id
  return $sessid;
}

// hold the text to be renderred in <body>
$output = "";

function deed()
{
  global $output;
  $method = $_SERVER["REQUEST_METHOD"];
  $client = $_SERVER["REMOTE_ADDR"];
  $user = $_POST["name"];
  $pass = $_POST["pass"];

  try
  {
    $auth = authenticate($user, $pass);
    if($auth)  // success
    {
      $session_id = mk_session($user, $client);
      setcookie("_session_id", $session_id, time()+1800, "/");
      $output = "Login Success! Welcome to HHAC, $user!<br />" .
        //"session_id: $session_id<br />" .
        "5秒后重定向到视频列表<br>" .
        "<script type='text/javascript'>setTimeout(\"self.location='/hhac/index.php'\", 5000)</script>";
    }
    else  // failure
    {
      $output = "Authenticate failed: $user<br />" .
        "5秒后重定向到登录页面<br />" .
        "<script type='text/javascript'>setTimeout(\"self.location='/hhac/login.php'\", 5000)</script>";
    }
  }
  catch(Exception $e)
  {
    header("HTTP/1.1 500 Internal Server Error");
    $msg = $e->getMessage();
    $output = "<p><span style=\"font-size:36px;font-weight:bold\">500 Internal Server Error</span></p><p>";
    $output = $output . "<span style=\"font-weight:bold\">Date</span>: " . date("Y-m-d H:i:s") . "<br />";
    $output = $output . "<span style=\"font-weight:bold\">Reason</span>: $msg<br />";
    $output = $output . "<span style=\"font-weight:bold\">Contact</span>: physacco@gmail.com<br /></p>";
  }
}
deed($user, $auth, $error);

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>HHAC Login</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>
<body>
<?php
echo $output;
?>
</body>
</html>
