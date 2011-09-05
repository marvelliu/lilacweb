<?php
	require("www2-funcs.php");
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_SMAIL);
	assert_login();
	
	mailbox_header("д�ʼ�");

	if ( !bbs_can_send_mail(isset($_GET["file"]) ? 1 : 0) )
		html_error_quit("�����ܷ����ż�");
	if (isset($_GET["board"]))
		$board = $_GET["board"];

	if (isset( $board )){
		$brdarr = array();
		$brdnum = bbs_getboard($board, $brdarr);
		if ($brdnum == 0)
			html_error_quit("�����������");
		$usernum = $currentuser["index"];
		if (bbs_checkreadperm($usernum, $brdnum) == 0)
			html_error_quit("�����������");
		$id = intval(@$_GET["id"]);
		if ($id <= 0)
			html_error_quit("���������");
		$articles = array ();
		$num = bbs_get_records_from_id($brdarr["NAME"], $id,$dir_modes["NORMAL"],$articles);
		if ($num <= 0) html_error_quit("���������");
		$article = $articles[1];
		if(!strncmp($article["TITLE"],"Re: ",4)) $title = $article["TITLE"] . ' ';
		else $title = "Re: " . $article["TITLE"] . ' ';
		$destuserid = $article["OWNER"];
		$file = $article["FILENAME"];
	}else{
		if (isset($_GET["file"]))
			$file = $_GET["file"];
		if (isset($file) && ( $file[0]!='M' || strstr($file,"..") ) )
			html_error_quit("���������..");
		$title = isset($_GET["title"])?$_GET["title"].' ':'';
		$destuserid = isset($_GET["userid"])?$_GET["userid"]:'';
	}
	
	if(isset($_GET["userid"]))
	{
		$lookupuser = array();
		if (!bbs_getuser($destuserid, $lookupuser))
			html_error_quit("������ռ���ID");
	}

	//system mailboxs
	$mail_box = array(".DIR",".SENT",".DELETED");
	$mail_boxtitle = array("�ռ���","������","������");

	//custom mailboxs
	$mail_cusbox = bbs_loadmaillist($currentuser["userid"]);
	$i = 2;
	if ($mail_cusbox != -1){
		foreach ($mail_cusbox as $mailbox){
			$i++;
			$mail_box[$i] = $mailbox["pathname"];
			$mail_boxtitle[$i] = $mailbox["boxname"];
			//$mail_boxnums[$i] = bbs_getmailnum2(bbs_setmailfile($currentuser["userid"],$mailbox["pathname"]));
			//$totle_mails+= $mail_boxnums[$i];
			}
		}
	$mailboxnum = $i + 1;
?>
<div class="mail">
<div class="mailH">
<b>д�ʼ�</b>
<?php
	for($i=0;$i<$mailboxnum;$i++){
?>
<a href="bbsmailbox.php?path=<?php echo $mail_box[$i];?>&title=<?php echo urlencode($mail_boxtitle[$i]);?>"><?php echo htmlspecialchars($mail_boxtitle[$i]); ?></a>
<?php		
	}
?>
</div>
<form name="postform" method="post" action="bbssendmail.php" class="mailM">
<table>
<tr>
<td class="b9">
������: <?php echo $currentuser["userid"]; ?><br />
��&nbsp;&nbsp;��: <input class="sb1" type="text" name="title" size="40" maxlength="100" value="<?php echo htmlspecialchars($title,ENT_QUOTES); ?>"><br />
������: <input class="sb1" type="text" name="userid" value="<?php echo $destuserid; ?>"<?php if(!isset($board) && isset($file) && $file!=""){echo " readonly";} ?>>
<input type="hidden" name="num" value="<?php echo @$_GET["num"] ?>">
<input type="hidden" name="dir" value="<?php echo @$_GET["dir"] ?>">
<input type="hidden" name="file" value="<?php if (!isset($board) && isset($file)) echo $file; ?>"><br />

ʹ��ǩ���� <select name="signature">
<?php
		if ($currentuser["signum"] == 0)
		{
?>
<option value="0" selected="selected">��ʹ��ǩ����</option>
<?php
		}
		else
		{
?>
<option value="0">��ʹ��ǩ����</option>
<?php
			for ($i = 1; $i <= $currentuser["signum"]; $i++)
			{
				if ($currentuser["signature"] == $i)
				{
?>
<option value="<?php echo $i; ?>" selected="selected">�� <?php echo $i; ?> ��</option>
<?php
				}
				else
				{
?>
<option value="<?php echo $i; ?>">�� <?php echo $i; ?> ��</option>
<?php
				}
			}
?>
<option value="-1" <?php if ($currentuser["signature"] < 0) echo "selected "; ?>>���ǩ����</option>
<?php
		}
?>
</select>
 [<a target="_blank" href="bbssig.php">�鿴ǩ����</a>]
<?php
	$bBackup = (bbs_is_save2sent() != 0);
?>
<input type="checkbox" name="backup" value="1"<?php if ($bBackup) echo " checked=\"checked\""; ?>>���浽������<br />
<textarea class="sb1" name="text" onkeydown='return textarea_okd(document.postform.submit, event);' rows="20" cols="80" wrap="physical">
<?php
	if(isset($file)){
		if(isset($board)){
			$filename = bbs_get_board_filename($board, $file);
			echo "\n�� �� " . $destuserid . " �Ĵ������ᵽ: ��\n";
		}else{
			$filename = bbs_setmailfile($currentuser["userid"], $file);
			echo "\n�� �� " . $destuserid . " ���������ᵽ: ��\n";
		}
		if(file_exists($filename))
		{
			$fp = fopen($filename, "r");
			if ($fp) {
				$lines = 0;
				$buf = fgets($fp,256);       /* ȡ����һ���� ���������µ� ������Ϣ */
				$end = strrpos($buf,")");
				$start = strpos($buf,":");
				if($start != FALSE && $end != FALSE)
					$quser=substr($buf,$start+2,$end-$start-1);

				for ($i = 0; $i < 3; $i++) {
					if (($buf = fgets($fp,500)) == FALSE)
						break;
				}
				while (1) {
					if (($buf = fgets($fp,500)) == FALSE)
						break;
					if (strncmp($buf, ": ��", 4) == 0)
						continue;
					if (strncmp($buf, ": : ", 4) == 0)
						continue;
					if (strncmp($buf, "--\n", 3) == 0)
						break;
					if (strncmp($buf,'\n',1) == 0)
						continue;
					if (++$lines > 10) {
						echo ": ...................\n";
						break;
					}
					/* */
					//if (stristr($buf, "</textarea>") == FALSE)  //filter </textarea> tag in the text
						echo ": ". htmlspecialchars($buf);
				}
				fclose($fp);
			}
		}
	}
?>
</textarea><br><div align="center">
<input class="bt1" type="submit" value="����" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input class="bt1" type="button" value="����" onclick="window.location.href='bbsmail.php'" />
</div></table></form>
</div>
<?php
	page_footer();
?>
