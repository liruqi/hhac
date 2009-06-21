<?php
global $db_user;
global $db_pass;
global $usr_row;
function authenticate()
{
    $db_user =  "hhac";
    $db_pass =  "iamharmless";
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $name = addslashes($_POST["name"]);
        $pass = addslashes($_POST["pass"]);
        $db = mysql_connect("localhost", $db_user, $db_pass);
        mysql_select_db("hhac", $db);

        $sql = "SELECT * FROM users WHERE name='$name' and password='$pass';";
        //echo "$sql<br>";
        $res = mysql_query($sql, $db);
        if($res && $usr_row = mysql_fetch_array($res, MYSQL_ASSOC))
        {
            //var_dump ($usr_row);
            return $usr_row;
        }
        header("HTTP/1.1 403 Forbidden");
        return false;
    }
    else 
    {
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
        return false;
    }
}

function mk_session_id()
{
    $user = $_POST["name"];
    $date = date("Y-m-d H:I:s");
    $remote_ip = $_SERVER["REMOTE_ADDR"];
    $seed = $user . $date . $remote_ip;
    //echo ($seed);
    return md5($seed);
}

?>
