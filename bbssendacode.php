<?php
require("funcs.php");
login_init();

if ($loginok != 1)
{
	html_nologin();
	exit();
}
cache_header("nocache");
if(!strcmp($currentuser["userid"],"guest"))
{
	header("Location: ".MAINPAGE_FILE);
	exit();
}
if($currentuser["userlevel"]&BBS_PERM_LOGINOK )
{
	header("Location: ".MAINPAGE_FILE);
	exit();
}
html_init("gb2312","","",1);

if(!isset($_GET["submit"]))
	return;
$email = $_GET["txtemail"];
$name = $currentuser["userid"];

#if(!bbs_verify_email_file_exist($name))
#{
#	echo "ȱ����֤�ļ�";
#}
#else 
#if(bbs_verified_email_file_exist($name) && $currentuser["reg_email"]!="" && strpos($currentuser["reg_email"],"@"))
#if(bbs_verified_email_file_exist($name) && $currentuser["reg_email"]!="")
#echo $currentuser["reg_email"]."---".($currentuser["reg_email"]!="")."===";
if(bbs_verified_email_file_exist($name) && !$currentuser["reg_email"]=="")
{
	echo "�Ѿ���֤";
}
else
#else if(bbs_verify_email_file_exist($name) && !bbs_verified_email_file_exist($name))
{
	bbs_send_verify_email($name, $email);
	echo "�����ż����ͳɹ���";
	echo $email;
}
?>
