<?php
require("pcadmin_inc.php");
pc_admin_check_permission();
$link = pc_db_connect();

$blogadmin = intval($_COOKIE["BLOGADMIN"]);

if( $_GET["act"] == "login" && !$blogadmin )
{
	$blogadmin = time();
	$action = $currentuser[userid]." ��¼Blog����Ա";
	$comment = $currentuser[userid]." �� ".date("Y-m-d H:i:s",$blogadmin)." �� ".$_SERVER["REMOTE_ADDR"]." ��¼��վBlog����Ա��";
	pc_logs($link , $action , $comment);
	setcookie("BLOGADMIN" , $blogadmin);
}
if( $_GET["act"] == "logout" && $blogadmin)
{
	$action = $currentuser[userid]." �˳�Blog����Ա��¼";
	$comment = $currentuser[userid]." �� ".date("Y-m-d H:i:s")." �� ".$_SERVER["REMOTE_ADDR"]." �˳���վBlog����Ա��¼����ʱ ".intval((time() - $blogadmin) / 60)." ���ӡ�";
	pc_logs($link , $action , $comment);
	unset($blogadmin);
	setcookie("BLOGADMIN");
}

pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog����Ա��¼");
pc_admin_navigation_bar();
?>
<br/><br/><br/><br/>
<?php
if( $blogadmin )
{
?>
<p align="center">
�û�����<font color=red><?php echo $currentuser[userid]; ?></font><br/>
��¼ʱ�䣺<font color=red><?php echo date("Y-m-d H:i:s",$blogadmin); ?></font><br/>
��¼IP��<font color=red><?php echo $currentuser[lasthost]; ?></font><br/>
<br/><br/><a href="pcadmin_log.php?act=logout">�˳�����Ա��¼</a>
</p>
<?php	
}
else
{
?>
<p align="center"><a href="pcadmin_log.php?act=login">����Ա��¼</a></p>
<?php	
}
pc_db_close($link);
pc_admin_navigation_bar();
html_normal_quit();
?>