<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <title>HHAC Register New User</title>
  <script type="text/javascript" src="/hhac/prototype.js"></script>
  <script type="text/javascript">
function register()
{
  name = $("name").value;
  pass = $("pass").value;
  pass2 = $("pass2").value;
  email = $("email").value;

  if(name == "")
  {
    alert("用户名不能为空！");
    return false;
  }

  if(pass == "")
  {
    alert("密码不能为空！");
    return false;
  }

  if(pass != pass2)
  {
    alert("两次输入密码必须相同！");
    return false;
  }

  email_re = /[._a-zA-Z0-0]+@[a-z0-9]+.[a-z]{3,4}/;
  if(email_re.test(email) == false)
  {
    alert("请填写正确的邮箱地址！");
    return false;
  }

  request = new Ajax.Request('/hhac/cgi/users', {
    parameters: {
      "Function": "create",
      "Name": name,
      "Pass": pass,
      "Email": email
    },
    onSuccess: function(transport) {  // on200
      var response = transport.responseText;
      var div = $("regMsg");
      div.firstChild.data = "注册成功! "+response;
      div.setAttribute("style", "color:green");
    },
    onFailure: function() {
      var div = $("regMsg");
      div.firstChild.data = "注册失败!";
      div.setAttribute("style", "color:red");
    }
    });
}
  </script>
  <style type="text/css">
  </style>
</head>

<body>
  <h2>HHAC Register New User</h2>

  <p><div id="regMsg" style="display:none">
  </div></p>
  <form id="regForm" name="regForm" onsubmit="return false;" method="post">
    <p>
      <label>Username:<br />
        <input id="name" name="name" type="text" /> </label>
    </p>

    <p>
      <label>Password:<br />
      <input id="pass" name="pass" type="password" /> </label>
    </p>

    <p>
      <label>Confirm Password:<br />
      <input id="pass2" name="pass" type="password" /> </label>
    </p>

    <p>
      <label>Email:<br />
        <input id="email" name="email" type="text" /> </label>
    </p>

    <p>
      <input name="commit" type="submit" value="Register" onclick="register()" />
    </p>
  </form>

</body></html>
