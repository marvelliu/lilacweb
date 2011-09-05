<?php
	require("www2-funcs.php");
	login_init();
	assert_login();

	if( !isset($_GET["dir"]) && !isset($_POST["dir"]))
		html_error_quit("���������");
	if( isset($_GET["dir"]) )
		$dirname = $_GET["dir"];
	else
		$dirname = $_POST["dir"];

	if( !isset($_GET["id"]) && !isset($_POST["id"]))
		html_error_quit("��������º�");
	if( isset($_GET["id"]) )
		$num = $_GET["id"];
	else
		$num = $_POST["id"];
	settype($num, "integer");

	if (strstr($dirname, "..") || strstr($dirname, "/")){
		html_error_quit("����Ĳ���");
	}
	$dir = "mail/".strtoupper($currentuser["userid"]{0})."/".$currentuser["userid"]."/".$dirname ;
	$total = filesize($dir) / 140;  /* TODO: bug */
	if( $total <= 0 ){
		html_error_quit("�ż㲻����");
	}
	if( $num >= $total ){
		html_error_quit("�ż㲻����");
	}

	$articles = array ();
	if( bbs_get_records_from_num($dir, $num, $articles) ) {
		$file = $articles[0]["FILENAME"];
	}else{
		html_error_quit("����Ĳ���");
	}

	$filename = "mail/".strtoupper($currentuser["userid"]{0})."/".$currentuser["userid"]."/".$file ;
	if(! file_exists($filename)){
		html_error_quit("�ż�������...");
	}
	mailbox_header("ת���ż�");
	
	if( isset($_POST["target"]) )
		$target =  $_POST["target"];
	else
		$target = "";
	if($target == "")
		html_error_quit("��ָ������");

	if( isset($_POST["big5"]) )
		$big5 = $_POST["big5"];
	else
		$big5=0;
	settype($big5, "integer");

	if( isset($_POST["noansi"]) )
		$noansi = $_POST["noansi"];
	else
		$noansi=0;
	settype($noansi, "integer");

	$ret = bbs_domailforward($filename, $articles[0]["TITLE"], $target, $big5, $noansi);
	if($ret < 0)
		html_error_quit("ϵͳ����: ".$ret);
	
	html_success_quit("�ż���ת�ĸ� " . htmlspecialchars($target));
?>
