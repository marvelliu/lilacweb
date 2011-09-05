<?php
	require("www2-funcs.php");
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_GMENU);
	toolbox_header("��ʱ�ǳ��޸�");
	assert_login();

	if (isset($_POST["username"])) {
		$username = $_POST["username"];
		if( strlen($username) < 2 )
			html_error_quit("�ǳ�̫��");
		$ret = bbs_modify_nick( $username );
		if($ret < 0){
			html_error_quit("ϵͳ����");
		} else {
			html_success_quit("��ʱ�ǳ��޸ĳɹ�");
		}
		exit;
	}
?>
<form action="bbsnick.php" method="post" class="medium">
	<fieldset><legend>��ʱ�ı��ǳ� (�����ķ���Ч)</legend>
		<div class="inputs">
			<label>���ǳ�:</label>
			<input id="sselect" type="text" name="username" value="<?php echo htmlspecialchars($currentuser["username"],ENT_QUOTES);?>" size="24" maxlength="39"/>
		</div>
	</fieldset>
	<div class="oper"><input type="submit" value="ȷ���޸�"></div>
</form>
<?php
	page_footer();
?>
