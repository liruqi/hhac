<?php
# get user_id & user from cookie
# return $user on success, or "guest" if error occurs
function get_user()
{
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
      mysql_close($db_con);
      $user = "guest";
      $message = "You are visiting HHAC as a guest.<br/>" .
        "<a href='/hhac/login.php'>Login</a><br>";
      echo $message;
      return $user;
    }
    else
    {
      $user_id = $row["user_id"];
      $address = $row["address"];
    }
 
    //echo "Got user_id: $user_id<br/>";
 
    // get user name, another SELECT query
    $sql = "SELECT name FROM users WHERE id='$user_id'";
    $res = mysql_query($sql, $db_con);
    if($res == FALSE)
      throw new Exception("SQL query failed.");
 
    // process the very query result
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
 
    // now, give a predication
    if($row == FALSE)  // cannot fetch an array
      $user = "guest";
    else
      $user = $row["name"];
 
    $message = "Welcome to HHAC, $user!<br/>" .
      "<a href='/hhac/logout.php'>Logout</a><br>";
    echo $message;

    mysql_close($db_con);
    return $user;
  }
  catch(Exception $e)
  {
    $message = "Cannot get user information.<br/>" .
      "Reason: " . $e->getMessage();
    echo $message;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Movie Directory</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
  <script type="text/javascript" src="/hhac/prototype.js"></script>
</head>
<body>
<div id="user_info">
<?php get_user(); ?>
</div>
<p><h2>Listing Movies (PHP)</h2></p>
<div id="div_mvlist">
<?php  // SQL query
$db = mysql_connect('localhost', 'hhac', 'iamharmless');
if(!$db) {
    die('Could not connect: ' . mysql_error());
}
$dbname = 'hhac';
mysql_select_db($dbname, $db) or die("Cannot switch to $dbname");
$query = 'select * from videos';
$res = mysql_query($query, $db) or die('SELECT error: ' . mysql_error());
?>

<table id="mvlist" border='1' width="600">
<?php  // renderring table `mvlist`
$line = 0;
while($row = mysql_fetch_array($res, MYSQL_ASSOC))
{
    $id = $row['id'];
    $title = $row['title'];
    $tags = $row['tags'];
    $description = $row['description'];
    $owner = $row['owner'];

  $query_user = "select * from users where id=$owner";
  $res_user = mysql_query($query_user, $db);
  $row_user = mysql_fetch_array($res_user, MYSQL_ASSOC);
  $user_name = $row_user['name'];

  $background = "#cceecc";
  if($line % 2 ) {
    $background = "#ccddcc";
  }
  print "<tr><td style=\"background:$background\">";
  print "<a target=\"viewer\" href=\"/hhac/play.php?id=$id\" style=\"font-weight:bold\">$title</a> by <a>$user_name</a><br>";
  print "Tags: $tags<br>";
  print "Description: $description";
  print "</td></tr>";
  $line = $line + 1;
}
mysql_close($db);
?>
</table>

</div>
</body>
</html>
