<?php
    require("www2-funcs.php");
    login_init();
	toolbox_header("���ѱ༭");
	assert_login();
	if (isset($_GET["userid"])) {
		$duserid = $_GET["userid"];
		$ret = bbs_delete_friend( $duserid );
		if($ret == 1){
			html_error_quit("��û���趨�κκ���");
		}else if($ret == 2){
			html_error_quit("���˱����Ͳ�����ĺ���������");
		}else if($ret == 3){
			html_error_quit("ɾ��ʧ��");
		}else{
			html_success_quit($duserid . "�Ѵ����ĺ���������ɾ��.");
		}
	}
?>