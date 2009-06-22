<?php

$user = "guest";

# get user_id & user from cookie
# return $user on success, or "guest" if error occurs
function get_user()
{
  global $user;

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
}// end of function get_user();


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
$rowcnt = mysql_num_rows($res);
if($rowcnt == 0) {
  die("<h1>404 Not Found</h1>");
}
$movie = mysql_fetch_array($res, MYSQL_ASSOC);
$id = $movie["id"];
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>test</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
  <script type="text/javascript" src="/hhac/prototype.js"></script>
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
  //border: 2px solid #cccccc;
}
  </style>
</head>

<body>

    <div class="top">
        <h1>Home of Harmless Amination & Comic</h1>
    </div>

    <hr/>

    <div id="user_info">
      <?php get_user(); ?>
    </div>

    <div id="play_and_info">

    <div id="movie_info">
      <span style="font-weight:bold"><?php echo $title ?></span><br/>
      <div id="movie-id" style="display:none"><?php echo $id ?></div>
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
              <source><?php echo "http://$ip/hhac/cgi/get-flv?id=$id" ?></source>
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

<script type="text/javascript">
function load_comment()
{
  var movie_id = $("movie-id").firstChild.data;
  request = new Ajax.Request('/hhac/cgi/comments',
    {
      method: 'GET',
      parameters: { movie: movie_id },
      onSuccess: function(transport) {
        var response = transport.responseText;
        //$("comment-list").firstChild.data = response;
        //$("comment-list").removeChild($("comment-list").firstChild);

        var comments = response.evalJSON();
        var comment_cnt = comments.size();

        if(comment_cnt > 0)
        {
          while(child = $("comment-list").firstChild)
          {
            $("comment-list").removeChild(child);
          }

          for(var i = 0; i < comment_cnt; i++)
          {
            var comment = comments[i];
            var user = comment.user;
            var content = comment.content;
           
            var tn = document.createTextNode(content+" by "+user);
            var li = document.createElement("li");
            li.appendChild(tn);
            $("comment-list").appendChild(li);
          }
        }
        else
        {
          $("comment-list").firstChild.data = "[没有评论]";
        }
      },
      onFailure: function() {
        alert("获取评论失败");
      }
    });
}
function submit_comment()
{
  var comment = document.getElementById('comment').value;
  if(comment == "") {
      alert("评论为空，无法发表!");
      return;
  }

  var movie_id = $("movie-id").firstChild.data;
  var comment = $("comment").value;

  request = new Ajax.Request('/hhac/cgi/submit-comment', {
    parameters: {
      movie: movie_id,
      comment: comment
      },
    onSuccess: function(transport) {
      alert("评论发表成功！");
      load_comment();
    },
    onFailure: function() { alert("评论发表失败！") }
    });
}

// 在页面加载时读取评论
load_comment();
</script>

  <div id="comments">
    <p>Comments</p>
    <p><div id="comment-list">
      读取评论中...
    </div></p>
    <p><div id="newComment" style="">
    <?php
    if($user == "guest")
      echo "登录后发表评论 <a href=\"/hhac/login.php\">Login</a>";
    else {
      echo '<form id=“commitForm” name="commentForm" onsubmit="return false;" method="post">
        <div><textarea id="comment" name="comment" style="width:480px;height:100px">我也来评论</textarea></div>
        <div><button id="commit" name="commit" onclick="submit_comment()">发表</button></div>
      </form>';
    }
    ?>
    </div></p>
  </div>
</body></html>
