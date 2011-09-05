<?php
    require("www2-funcs.php");
    login_init();
    bbs_session_modify_user_mode(BBS_MODE_EDITUFILE);
    toolbox_header("������");
    assert_login();
    
    $fname = bbs_sethomefile($currentuser["userid"], "ignores");
    if(isset($_POST["badlist"])) {
        $empty = "";
        for($i=0; $i<BBS_IDLEN; $i++)
            $empty .= "\0";
        $badlist = explode("\n", $_POST["badlist"]);
        $count = count($badlist);
        $badstr = "";
        $user = array();
        $reallist = array();
		$rid = 0;
        for($i=0; $i<$count; $i++) {
            $userid = trim($badlist[$i]);
            if(bbs_getuser($userid, $user)) {
                if($currentuser["userid"] == $user["userid"])
                    break;
                $repeat = 0;
                for($j=0; $j<$rid; $j++) {
                    if($reallist[$j] == $user["userid"]) {
                        $repeat = 1;
                        break;
                    }
                }
                if(!$repeat) {
                    $badstr .= substr($user["userid"] . $empty, 0, BBS_IDLEN + 1);
                    $reallist[$rid] = $user["userid"];
                    $rid++;
                }
            }
        }
        $fp = @fopen($fname, "w");
        if($fp) {
            fwrite($fp, $badstr);
            fclose($fp);
            html_success_quit($currentuser["userid"] . "���������޸�");
        }
        else
            html_error_quit("�޷�д���ļ�������ϵ����Ա��");
    }
?>
<form method="post" action="bbsbadlist.php" class="large">
<fieldset><legend>�޸ĺ�����</legend>
<textarea name="badlist" onkeydown='return textarea_okd(this, event);' wrap="physical" id="sfocus">
<?php
    $fp = @fopen($fname, "r");
    if($fp) {
        while(!feof($fp)) {
            $userid = fread($fp, BBS_IDLEN + 1);
            $userid = substr($userid, 0, strpos($userid, 0));
            print($userid . "\n");
        }
        fclose($fp);
    }
?>
</textarea>
</fieldset>
<div class="oper">
<input type="submit" value="ȷ��" /> <input type="reset" value="ȡ��" />
</div>
</form>
<?php
	page_footer();
?>
