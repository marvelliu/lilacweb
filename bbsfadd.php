<?php
	require("www2-funcs.php");
	login_init();
	toolbox_header("���ѱ༭");
	assert_login();
	if (isset($_POST["userid"]))
	{
		$duserid = $_POST["userid"];
		@$expp = $_POST["exp"];
		$ret = bbs_add_friend( $duserid ,$expp );
		if($ret == -1){
			html_error_quit("��û��Ȩ���趨���ѻ��ߺ��Ѹ�����������");
		}else if($ret == -2){
			html_error_quit("���˱���������ĺ���������");
		}else if($ret == -3){
			html_error_quit("ϵͳ����");
		}else if($ret == -4){
			html_error_quit("�û�������");
		}else{
			html_success_quit($duserid . "�����ӵ����ĺ���������.");
		}
	}else{
?>
<form action="bbsfadd.php" method="post" class="medium">
	<fieldset><legend>���Ӻ���</legend>
		<div class="inputs">
			<label>�����������ӵĺ����ʺ�:</label>
				<input maxlength=12 name="userid" type="text" id="sfocus"/><br/>
			<label>�����������ӵĺ��ѱ�ע:</label>
				<input maxlength=14 name="exp" type="text"/>
		</div>
	</fieldset>
	<div class="oper"><input type="submit" value="��Ӻ���"></div>
</form>
<a href="bbsfall.php">���غ�������</a>
<?php
		page_footer();
	}
?>
