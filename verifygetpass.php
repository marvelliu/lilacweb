<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gb2312">
<link rel=stylesheet type=text/css href='/bbs.css'>
<nobr><center>�û������������<hr color=green>

<?php
	require("www2-funcs.php");
	login_init();
	$needlogin = false;
	require("lilac_funcs.php");
	$ac=@$_GET["ac"];
	$uid=@$_GET["uid"];
	if (strlen($ac)!=32)
	{
		echo "��Ч�ļ����룡";
		exit(0);
	}
	if (strlen($uid) < 2)
	{
		echo "��Ч�û���";
		exit(0);
	}
	$acfile = bbs_sethomefile($uid, ".pass_email");
	//echo "file:".$acfile;
	if( ! file_exists($acfile) )
	{
		echo "������Ϣ��Ч";
		exit(0);
	}
	load_acinf($acfile);

	if ($acinf["ACCD"]!=$ac)
	{
		echo "���󼤻���Ϣ, �޷�����!";
		exit(0);
	}else{
		if(!isset($_POST['submit']))
		{
echo <<<EOT
<form action"" method="post">
<table width=750 border=0 cellpadding=0 cellspacing=0>
<tr><td></td><td>

<center>
<b>�����������벢������һ����ȷ����û���������</b>
<table width=500  border="1" cellpadding="2" style="BORDER-COLLAPSE: collapse" b
ordercolor="#000080">
<tr> <td>����������</td><td><input type="password" name="newpassword" size="20"></td> </tr>
<tr> <td>ȷ��������</td><td><input type="password" name="renewpassword" size="20"></td> </tr>
<tr> <td colspan="2" align="center"><input type="submit" name="submit" value=" ȷ�� "></td></tr>
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
				echo "������������벻һ�£�ȡ������ʧ��";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="����һҳ">';
				exit;
			}
			elseif(strlen($_POST['newpassword']) >20)
			{
				echo "����̫����ȡ������ʧ��";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="����һҳ">';
				exit;
			}
			elseif(strlen($_POST['newpassword']) <2)
			{
				echo "����̫����ȡ������ʧ��";
				echo '<br /><input type="button" onclick="window.history.go(-1);" value="����һҳ">';
				exit;
			}
			else
			{
				$test_id = bbs_setpassword($uid,$_POST['newpassword']);
				if(empty($test_id))
				{
					echo "ϵͳ��������ϵ����Ա<br />";
					echo '<input type="button" onclick="window.location.href=\'./index.html\';" value="������ҳ">';
				}
				else
				{
					echo "�����޸ĳɹ����������������趨<br />";
					echo '<input type="button" onclick="window.location.href=\'./index.html\';" value="������ҳ">';
				}
			}
			bbs_dopasswordemail($uid);
			exit;
		}
	}
?>


