<?php
    $setboard=0;
    require("www2-funcs.php");
	login_init();
	page_header("״̬", FALSE);

	$unread = false;	
	if (strcmp($currentuser["userid"], "guest")) {
		$tn = bbs_mail_get_num($currentuser["userid"]);
		if ($tn) {
			$unread = $tn["newmail"];
			$total = $tn["total"];
			$full = $tn["full"];
		}
	}
?>
<script type="text/javascript">
<!--
	addBootFn(footerStart);
	var stayTime = <?php echo (time()-$currentuinfo["logintime"]); ?>;
	var serverTime = <?php echo (time() + intval(date("Z"))); ?>;
	var hasMail = <?php echo $unread ? "1" : "0"; ?>;
	var MailFull = <?php echo $full ? "1" : "0"; ?>;
<?php
	if (isset($currentuser["userid"]) && $currentuser["userid"] != "guest" && bbs_checkwebmsg()) {
?>
alertmsg();
<?php
	}
?>
	function newmailnotice() {
		var thespan = document.getElementById('mailnotice');
		if(thespan.style.display == '')
			thespan.style.display = 'none';
		else
			thespan.style.display = '';
		if(hasMail || MailFull)
			setTimeout('newmailnotice();', 800);
		else
			thespan.style.display = 'none';
	}
	function clearjunkmail() {
		if(confirm('���Ƿ�Ҫ���������?')) {
			if(top.window['f3'])
				top.window['f3'].location='bbsmailact.php?act=clear';
			return true;
		}
		return false;
	}
//-->
</script>
<body><div class="footer">ʱ��[<span id="divTime"></span>] ����[<?php echo bbs_getonlinenumber(); ?>]
�ʺ�[<a href="bbsqry.php?userid=<?php echo $currentuser["userid"]; ?>" target="f3"><?php echo $currentuser["userid"]; ?></a>]
<?php
	if (isset($total)) {
echo "����[<a href=\"bbsmailbox.php?path=.DIR&title=�ռ���\" target=\"f3\">";
		if ($full) {
			echo $total . "��</a><a href=\"javascript:void(0);\" onclick=\"return clearjunkmail()\">(���䳬��)</a>] <bgsound src='sound/mailfull.mp3'>";
		} else if ($unread) {
			echo $total . "��(������)</a>] <bgsound src='sound/newmail.mp3'>";
		} else {
			echo $total . "��</a>] ";
		}
	}
?>
ͣ��[<span id="divStay"></span>]
<span id="mailnotice" style="display:none">�����ż�</span></div></body>
</html>
