<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gb2312">
<link rel=stylesheet type=text/css href='/bbs.css'>
<nobr><center>用户激活协议<hr color=green>
<?php
	$needlogin = false;
	require("funcs.php");
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
	$acfile = bbs_sethomefile($uid, ".verify_email");
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

		bbs_doverifyemail($uid);
	}
?>
<table width=750 border=0 cellpadding=0 cellspacing=0>

<tr><td></td><td align=center><br><br>
<textarea rows=20 cols=90 class=l15>

    用户邮件激活成功！

　　　　　　　　　　　　　　　　　　　　　　　　　授权人：紫丁香社区办公室
</textarea>
<br><br></td></tr>
<tr><td></td><td>

<center>
<b>用户信息：</b>
<table width=500  border="1" cellpadding="2" style="BORDER-COLLAPSE: collapse" b
ordercolor="#000080">
<tr> <td>用户ID</td><td><?php echo $uid ?></td> </tr>
<tr> <td>真实姓名</td><td><?php echo $acinf["NAME"] ?></td> </tr>
<tr> <td>Verify邮箱</td><td><?php echo $acinf["MAIL"] ?></td> </tr>
<tr> <td colspan="2" align="center"><input type="button" onclick="window.location.href='./index.html';" value="返回首页"></td></tr>
</table>
</center>
</td></tr>
</table>

