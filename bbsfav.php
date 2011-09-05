<?php
	$XX = isset($_GET["x"]);
	require("www2-funcs.php");
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_SELECT);
	if ($XX) {
		$page_title = "�·���������";
	} else {
		assert_login();
		$page_title = $currentuser["userid"] . " ���ղؼ�";
	}
	page_header($page_title, "", "<meta name='kbsrc.brd' content='' />");

	if (isset($_GET["select"]))
		$select = $_GET["select"];
	else
		$select = 0;
	settype($select, "integer");

	if ($select < 0)
		html_error_quit("����Ĳ���");
	if (!$XX) {
		if(bbs_load_favboard($select)==-1)
			html_error_quit("����Ĳ���");
		if (isset($_GET["delete"]))
		{
			$delete_s=$_GET["delete"];
			settype($delete_s,"integer");
			bbs_del_favboard($select,$delete_s);
		}
		if (isset($_GET["deldir"]))
		{
			$delete_s=$_GET["deldir"];
			settype($delete_s,"integer");
			bbs_del_favboarddir($select,$delete_s);
		}
		if (isset($_GET["dname"]))
		{
			$add_dname=trim($_GET["dname"]);
			if ($add_dname)
				bbs_add_favboarddir($add_dname);
		}
		if (isset($_GET["bname"]))
		{
			$add_bname=trim($_GET["bname"]);
			if ($add_bname)
				$sssss=bbs_add_favboard($add_bname);
		}
	}
	$boards = bbs_fav_boards($select, $XX ? 2 : 1, 1);
	$list_father = bbs_get_father($select);
	if ($boards === FALSE)
		html_error_quit("��ȡ���б�ʧ��");
	$fix = $XX ? "&x" : "";
?>
<script type="text/javascript"><!--
var o = new brdWriter(<?php echo $list_father; ?>, <?php echo $select; ?>, '<?php echo $fix; ?>');
<?php
	foreach($boards as $board) {
		if( $board["UNREAD"] ==-1 && $board["ARTCNT"] ==-1) continue;
		if ($board["BID"] == -1) continue;
		if ($board["FLAG"] == -1 ) {
?>
o.f(<?php echo $board["BID"];?>,'<?php echo addslashes($board["DESC"]); ?> ',<?php echo $board["NPOS"]; ?>,'<?php echo $XX?$board["BM"]:""; ?>');
<?php
		} else {
			$isGroup = ($board["FLAG"]&BBS_BOARD_GROUP) ? "true" : "false";
?>
o.o(<?php echo $isGroup; ?>,<?php echo ($board["UNREAD"] == 1) ? "1" : "0"; ?>,<?php echo $board["BID"]; ?>,<?php echo $board["LASTPOST"]; 
?>,'<?php echo $board["CLASS"]; ?>','<?php echo addslashes($board["NAME"]); ?>','<?php echo addslashes($board["DESC"]);
?>','<?php echo $board["BM"]; ?>',<?php echo $board["ARTCNT"]; ?>,<?php echo $board["NPOS"];?>,<?php echo $board["CURRENTUSERS"];?>);
<?php
		}
	}
?>
o.t();
//-->
</script>
<?php
	if (!$XX) {
?>
<div class="oper"><form action=bbsfav.php>����Ŀ¼: <input name=dname size=24 maxlength=20 type=text value="">
<input type=submit value=ȷ��><input type=hidden name=select value=<?php echo $select;?>>
</form></div>
<div class="oper"><form action=bbsfav.php>���Ӱ���: <input name=bname size=24 maxlength=20 type=text value="">
<input type=submit value=ȷ��><input type=hidden name=select value=<?php echo $select;?>>
</form></div>
<?php
	}
	page_footer();
?>
