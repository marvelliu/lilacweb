<?php
	require("www2-funcs.php");
	login_init();
	assert_login();
	
	$act = $_GET["act"];
	$dirname = isset($_GET["dir"])?$_GET["dir"]:false;
	$title = isset($_GET["title"])?$_GET["title"]:false;
	$act2 = isset($_POST["act2"])?$_POST["act2"]:false;
	
	if($act == "clear" )
	{
		$dirname = ".DELETED";
		$title = "������";
	}
	
	if(!isset($_POST["mailnum"]) || $_POST["mailnum"] == 0)
		$mail_num = 19;
	else
		$mail_num = $_POST["mailnum"];
	
	if (strstr($dirname,'..'))
	{
		html_error_quit("��ȡ�ʼ�����ʧ��!");
	}
	
	if($act == "del")
	{
		$filename = $_GET["file"];
		$ret = bbs_delmail($dirname,$filename);
		if($ret != 0)
		{
			html_error_quit("�ż������ڻ��������, �޷�ɾ��");
		}
	}
	elseif($act == "move")
	{
		for($i=0;$i < $mail_num;$i++)
		{
			if(!isset($_POST["file".$i])||$_POST["file".$i]=="")
				continue;
			
			$filename = $_POST["file".$i];
			if( $act2 == "delarea" )
				$ret = bbs_delmail($dirname,$filename);
		}
	}
	elseif($act == "clear")
	{
		
		$mail_fullpath = bbs_setmailfile($currentuser["userid"],".DELETED");
		$mail_num = bbs_getmailnum2($mail_fullpath);
		$maildata = bbs_getmails($mail_fullpath,0,$mail_num);
		for($i=0; $i < $mail_num; $i++ )
		{
			bbs_delmail(".DELETED",$maildata[$i]["FILENAME"]);
		}
?>
<script type="text/javascript"><!--
alert('��ɾ��<?php echo $mail_num; ?>�������ʼ�');
if(top.window['f4'])
	top.window['f4'].location.reload();
window.location='bbsmail.php';
//-->
</script>
<?php
		die();
	}
	/*
	elseif($act == "delarea" )
	{
		$mail_fullpath = bbs_setmailfile($currentuser["userid"],$dirname);
		$mail_num = bbs_getmailnum2($mail_fullpath);
		
		$dstart = $_POST["dstart"];
		$dend = $_POST["dend"];
		$dtype = $_POST["dtype"];
		
		if( $dstart < 1 || $dstart > $mail_num  || $dend < 1 || $dend > $mail_num  || $dstart > $dend  )
		{
			html_init("gb2312");
			html_error_quit("����ɾ����ʼ����������������������룡");
			die();
		}
		
		$dnum = $dend - $dstart + 1;
		$dstart-- ;
		
		$maildata = bbs_getmails($mail_fullpath,$dstart,$dnum);
		if($dtype == 1)
		{
			foreach( $maildata as $mail )
			{
				bbs_delmail($dirname,$mail["FILENAME"]);
			}
		}
		else
		{
			foreach( $maildata as $mail )
			{
				if(stristr($mail["FLAGS"],"m"))
					continue;
				else
				{
					bbs_delmail($dirname,$mail["FILENAME"]);
				}
			}
		}
	}
	*/
	else
	{
		
	}
	
	header("Location:bbsmailbox.php?path=".urlencode($dirname)."&title=".urlencode($title));
?>
