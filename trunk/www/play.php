<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>test</title>
  <style type="text/css">
#movie_info {
  position: absolute;
  margin-left: 500px;
  width: 50%;
}
#player {
  position: absolute;
  border: 1px solid;
}
#comments {
  position: absolute;
  margin-top: 380px;
  width: 98%;
  border: 2px solid #cccccc;
}
  </style>
</head>

<?php
$ip = $_SERVER["SERVER_NAME"];
$id = $_GET["id"];

$db = mysql_connect("localhost", "hhac", "iamharmless");
if(!$db) {
  die("Could not connect: " . mysql_error());
}
$dbname = "hhac";
mysql_select_db($dbname, $db) or die("Cannot switch to $dbname");

$query_flv = "select * from videos where id=$id";
$res = mysql_query($query_flv, $db) or die("Query error" . mysql_error());
$movie = mysql_fetch_array($res, MYSQL_ASSOC);
$title = $movie["title"];
$tags = $movie["tags"];
$description = $movie["description"];
$owner = $movie["owner"];

$query_user = "select name from users where id='$owner'";
$res_user = mysql_query($query_user, $db) or die("Query error" . mysql_error());
$user = mysql_fetch_array($res_user, MYSQL_ASSOC);
$user_name = $user["name"];

mysql_close($db);
?>


<body>
    <div id="play_and_info">

    <div id="movie_info">
      <span style="font-weight:bold"><?php echo $title ?></span><br/>
      Owner: <?php echo $user_name ?><br/>
      Tags: <?php echo $tags ?><br/>
      Description: <?php echo $description ?><br/>
    </div>


    <div id="player">
    <object type="application/x-shockwave-flash" data="player.swf" width="480" height="360" id="vcastr3">
      <param name="movie" value="player.swf"/> 
      <param name="allowFullScreen" value="true" />
      <param name="FlashVars" value="xml=
        <vcastr>
          <channel>
            <item>
              <source><?php echo "http://$ip/cgi-bin/get-flv?id=$id" ?></source>
              <duration></duration>
              <title></title>
            </item>
          </channel>
          <config>
          </config>
          <plugIns>
          </plugIns>
        </vcastr>"/>
    </object>
    </div>
  </div>

  <div id="comments">
    <p>Comments</p>
  </div>
</body></html>
