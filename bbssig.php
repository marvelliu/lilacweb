<?php
    require("www2-funcs.php");
    login_init();
    bbs_session_modify_user_mode(BBS_MODE_EDITUFILE);
    toolbox_header("ǩ�����޸�");
    assert_login();
    
    $filename=bbs_sethomefile($currentuser["userid"],"signatures");
    if (isset($_POST['text'])) {
        $fp=@fopen($filename,"w+");
        if ($fp!==false) {
            fwrite($fp,str_replace("\r\n", "\n", $_POST["text"]));
            fclose($fp);
            bbs_recalc_sig();
            html_success_quit($currentuser["userid"] . "ǩ�����޸ĳɹ�");
        }
        html_error_quit("ϵͳ����");
    }
?>
<form method="post" action="bbssig.php" class="large">
<fieldset><legend>�޸�ǩ���� (ÿ 6 ��Ϊһ����λ�������ö��ǩ����)</legend>
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
