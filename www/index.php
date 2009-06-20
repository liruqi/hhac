<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Movie Directory</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
  <script type="text/javascript" src="/hhac/prototype.js"></script>
</head>
<body>
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
