<?php
    require("www2-funcs.php");
    login_init();
    bbs_session_modify_user_mode(BBS_MODE_EDITUFILE);
    toolbox_header("˵�����޸�");
    assert_login();
    
    $filename=bbs_sethomefile($currentuser["userid"],"plans");
    if (isset($_POST['text'])) {
        $fp=@fopen($filename,"w+");
        if ($fp!==false) {
            fwrite($fp,str_replace("\r\n", "\n", $_POST["text"]));
            fclose($fp);
            html_success_quit($currentuser["userid"] . "˵�����޸ĳɹ�");
        }
        html_error_quit("ϵͳ����");
    }
?>
<form method="post" action="bbsplan.php" class="large">
<fieldset><legend>�޸�˵����</legend>
<textarea name="text" onkeydown='return textarea_okd(this, event);' wrap="physical" id="sfocus">
<?php
    echo @htmlspecialchars(file_get_contents($filename));
?>
</textarea>
</fieldset>
<div class="oper">
<input type="submit" value="����" /> <input type="reset" value="��ԭ" />
</div>
</form>
<?php
	page_footer();
?>
