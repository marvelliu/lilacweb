<html>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gb2312">
<link rel=stylesheet type=text/css href='/bbs.css'>
<nobr><center>�û�����Э��<hr color=green>
<?php
	$needlogin = false;
	require("funcs.php");
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
	$acfile = bbs_sethomefile($uid, ".verify_email");
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

		bbs_doverifyemail($uid);
	}
?>
<table width=750 border=0 cellpadding=0 cellspacing=0>

<tr><td></td><td align=center><br><br>
<textarea rows=20 cols=90 class=l15>

    �û��ʼ�����ɹ���

����������������������������������������������������Ȩ�ˣ��϶��������칫��
</textarea>
<br><br></td></tr>
<tr><td></td><td>

<center>
<b>�û���Ϣ��</b>
<table width=500  border="1" cellpadding="2" style="BORDER-COLLAPSE: collapse" b
ordercolor="#000080">
<tr> <td>�û�ID</td><td><?php echo $uid ?></td> </tr>
<tr> <td>��ʵ����</td><td><?php echo $acinf["NAME"] ?></td> </tr>
<tr> <td>Verify����</td><td><?php echo $acinf["MAIL"] ?></td> </tr>
<tr> <td colspan="2" align="center"><input type="button" onclick="window.location.href='./index.html';" value="������ҳ"></td></tr>
</table>
</center>
</td></tr>
</table>

