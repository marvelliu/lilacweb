<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gb2312">
<link rel=stylesheet type=text/css href='/bbs.css'>
<nobr><center>用户重置密码服务<hr color=green>

<?php
	require("www2-funcs.php");
	login_init();
	$needlogin = false;
	require("lilac_funcs.php");
	$ac=@$_GET["ac"];
	$uid=@$_GET["uid"];
	if (strlen($ac)!=32)
	{
		echo "无效的激活码！";
		exit(0);
	}
	if (strlen($uid) < 2)
	{
		echo "无效用户名";
		exit(0);
	}
	$acfile = bbs_sethomefile($uid, ".pass_email");
	//echo "file:".$acfile;
	if( ! file_exists($acfile) )
	{
		echo "激活信息无效";
		exit(0);
	}
	load_acinf($acfile);

	if ($acinf["ACCD"]!=$ac)
	{
		echo "错误激活信息, 无法激活!";
		exit(0);
	}else{
		if(!isset($_POST['submit']))
		{
echo <<<EOT
<form action"" method="post">
<table width=750 border=0 cellpadding=0 cellspacing=0>
<tr><td></td><td>

<center>
<b>请输入新密码并再输入一次以确保您没有输入错误：</b>
<table width=500  border="1" cellpadding="2" style="BORDER-COLLAPSE: collapse" b
ordercolor="#000080">
<tr> <td>输入新密码</td><td><input type="password" name="newpassword" size="20"></td> </tr>
<tr> <td>确认新密码</td><td><input type="password" name="renewpassword" size="20"></td> </tr>
<tr> <td colspan="2" align="center"><input type="submit" name="submit" value=" 确定 "></td></tr>
</table>
</form>
</center>
</td></tr>
</table>
EOT;
		}
		else
		{
			if($_POST['newpassword'] != $_POST['renewpassword'])
			{
				echo "两次输入的密码不一致，取回密码失败";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="回上一页">';
				exit;
			}
			elseif(strlen($_POST['newpassword']) >20)
			{
				echo "密码太长，取回密码失败";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="回上一页">';
				exit;
			}
			elseif(strlen($_POST['newpassword']) <2)
			{
				echo "密码太长，取回密码失败";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="回上一页">';
				exit;
			}
			else
			{
				$test_id = bbs_setpassword($uid,$_POST['newpassword']);
				if(empty($test_id))
				{
					echo "系统错误，请联系管理员<br />";
					echo '<input type="button" onclick="window.location.href=\'./index.html\';" value="返回首页">';
				}
				else
				{
					echo "密码修改成功，您的新密码已设定<br />";
					echo '<input type="button" onclick="window.location.href=\'./index.html\';" value="返回首页">';
				}
			}
			bbs_dopasswordemail($uid);
			exit;
		}
	}
?>


