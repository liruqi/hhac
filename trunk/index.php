<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Movie Directory</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>
<body>
<p><h2>Listing Movies</h2></p>
<p>
<?php
$db = mysql_connect('localhost', 'hhac', 'iamharmless');
if(!$db) {
    die('Could not connect: ' . mysql_error());
}
$dbname = 'hhac';
mysql_select_db($dbname, $db) or die("Cannot switch to $dbname");
$query = 'select id, title, owner from videos';
$res = mysql_query($query, $db) or die('SELECT error: ' . mysql_error());
mysql_close($db);
?>
<table border='1'>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Owner</th>
  </tr>
<?php
while($row = mysql_fetch_array($res, MYSQL_ASSOC))
{
    $id = $row['id'];
    $title = $row['title'];
    $owner = $row['owner'];

    print("<tr>\n");
    print("<td>$id</td>\n");
<<<<<<< .mine
    print("<td><a href=/cgi-bin/get-flv?id=$id>$title</a></td>\n");
=======
#    print("<td><a href=/cgi-bin/get-flv?id=$id&amp;session=42783497293>$name</a></td>\n");
    print("<td><a target=\"viewer\" href=/vod/play.php?id=$id>$name</a></td>\n");
>>>>>>> .r11
    print("<td>$owner</td>\n");
    print("</tr>\n");
}
?>
</table>
</p>
</body>
</html>
