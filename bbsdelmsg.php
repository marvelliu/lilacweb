<?php
    require("www2-funcs.php");
    login_init();
    page_header("ѶϢ�ؼ�");
	assert_login();

	$filename = bbs_sethomefile($currentuser["userid"],"msgindex");
	if (file_exists($filename))
		unlink($filename);
	$filename = bbs_sethomefile($currentuser["userid"],"msgindex2");
	if (file_exists($filename))
		unlink($filename);
	$filename = bbs_sethomefile($currentuser["userid"], "msgcount");
	if (file_exists($filename))
		unlink($filename);
		
	html_success_quit("�Ѿ�ɾ������ѶϢ����");
?>
