<?php
function logout()
{
  $session_id; $user_id; $user; $message;

  try
  {
    $session_id = $_COOKIE["_session_id"];
    //echo "_session_id: $session_id<br/>";
 
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
    $sessid = mysql_real_escape_string($session_id);
    $sql = "SELECT user_id, address FROM sessions WHERE sessid='$sessid'";
    $res = mysql_query($sql, $db_con);
    if($res == FALSE)
      throw new Exception("SQL query failed.");
 
    // process the very query result
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
 
    // now, give a predication
    if($row == FALSE)  // cannot fetch an array
    {
      $message = "您尚未登录<br/>" .
        "5秒后重定向到视频列表<br>" .
        "<script type='text/javascript'>setTimeout(\"self.location='/hhac/index.php'\", 5000)</script>";
      echo $message;
      mysql_close($db_con);
      return;
    }
    else
    {
      $user_id = $row["user_id"];
      $address = $row["address"];
    }
 
    //echo "Got user_id: $user_id<br/>";
 
    // delete session item
    $sql = "DELETE FROM sessions WHERE sessid='$sessid'";
    $res = mysql_query($sql, $db_con);
    if($res == FALSE)
      throw new Exception("SQL query failed.");
    else
    {
      setcookie("_session_id", $sessid, time()-3600, "/");
      //$message = "Session deleted.<br/>";
    }
 
    // get user name, another SELECT query
    $sql = "SELECT name FROM users WHERE id='$user_id'";
    $res = mysql_query($sql, $db_con);
    if($res == FALSE)
      throw new Exception("SQL query failed.");
 
    // process the very query result
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
    $user = $row["name"];
    $message = $message . "$user: 已成功退出<br/>" .
        "5秒后重定向到视频列表<br>" .
        "<script type='text/javascript'>setTimeout(\"self.location='/hhac/index.php'\", 5000)</script>";
    echo $message;
 
    mysql_close($db_con);
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
}// end of function logout()
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Movie Directory</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
  <script type="text/javascript" src="/hhac/prototype.js"></script>
</head>
<body>
<?php logout() ?>
</body>
</html>
