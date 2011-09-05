<?php
	require("www2-funcs.php");
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_READING);
	assert_login();

	if( !isset($_GET["board"]) && !isset($_POST["board"]))
		html_error_quit("�����������");
	if( isset($_GET["board"]) )
		$board = $_GET["board"];
	else
		$board = $_POST["board"];

	$brdarr = array();
	$brdnum = bbs_getboard($board, $brdarr);
	if ($brdnum == 0){
		html_error_quit("�����������");
	}
	$usernum = $currentuser["index"];
	if (bbs_checkreadperm($usernum, $brdnum) == 0){
		html_error_quit("�����������");
	}

	if( !isset($_GET["gid"]) && !isset($_POST["gid"]))
		html_error_quit("��������º�");
	if( isset($_GET["gid"]) )
		$gid = $_GET["gid"];
	else
		$gid = $_POST["gid"];

	if( !isset($_GET["start"]) && !isset($_POST["start"]))
		html_error_quit("����Ĳ���");
	if( isset($_GET["start"]) )
		$start = $_GET["start"];
	else
		$start = $_POST["start"];

	$articles = array ();
	$num = bbs_get_records_from_id($brdarr["NAME"], $start, $dir_modes["NORMAL"] , $articles);
	if($num == 0)
		html_error_quit("��������º�");
	$brd_encode = urlencode($board);
	
	bbs_board_nav_header($brdarr, "����ת��");
	if (!isset($_GET["do"])){
?>
<form action="bbstfwd.php?do" method="post" class="medium"/>
<input type="hidden" name="board" value="<?php echo $brdarr["NAME"];?>"/>
<input type="hidden" name="gid" value="<?php echo $gid;?>"/>
<input type="hidden" name="start" value="<?php echo $start;?>"/>
	<fieldset>
		<legend>ͬ����ϼ�ת�����£�<?php echo $articles[1]["OWNER"];?> �� <a href="bbscon.php?bid=<?php echo $brdnum; ?>&id=<?php echo $id; ?>"><?php echo htmlspecialchars($articles[1]["TITLE"]); ?></a></legend>
		<div class="inputs">
			<label>������ת�ĸ� (������Է���id��email��ַ):</label>
			<input type="text" name="target" size="40" maxlength="69" id="sselect" value="<?php echo $currentuser["email"];?>"><br/>
			<input type="checkbox" name="big5" id="big5" value="1"/><label for="big5" class="clickable">ʹ��BIG5��</label>
			<input type="checkbox" name="noansi" id="noansi" value="1" checked /><label for="noansi" class="clickable">����ANSI���Ʒ�</label>
            </br>
			<input type="checkbox" name="noref" id="noref" value="1"/><label for="noattach" class="clickable">����������</label>
			<input type="checkbox" name="noattach" id="noattach" value="1"/><label for="noattach" class="clickable">����������(�������)</label>
		</div>
	</fieldset>
	<div class="oper"><input type="submit" value="ȷ��ת��"></div>
</form>
<?php
	} else {
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

		if( isset($_POST["noref"]) )
			$noref = $_POST["noref"];
		else
			$noref=0;
		settype($noref, "integer");

		if( isset($_POST["noattach"]) )
			$noattach = $_POST["noattach"];
		else
			$noattach=0;
		settype($noattach, "integer");

		$ret = bbs_dotforward($brdarr["NAME"], $gid, $start, $target, $big5, $noansi, $noref, $noattach);
		if($ret < 0)
			html_error_quit("ϵͳ����:".$ret);
		
		html_success_quit("������ת�ĸ�" . $target);
	}
	page_footer();
?>
