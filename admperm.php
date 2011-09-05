<?php
    require("www2-admin.php");

    admin_check("perm");

    $ret = 1;
    if(isset($_POST["modifyuserid"])) {
        $userid = $_POST["modifyuserid"];
        $perm = 0;
        for($i=0; $i<30; $i++) {
            if(@$_POST["p{$i}"] == "o")
                $perm |= (1 << $i);
        }
        $ret = bbs_admin_setuserperm($userid, $perm);
        html_success_quit("�޸��û�Ȩ�޳ɹ���", array("<a href=\"admperm.php?userid={$userid}\">����Ȩ�޸���ҳ��</a>"));
    }

    if(isset($_POST["userid"]))
        $userid = $_POST["userid"];
    else if(isset($_GET["userid"]))
        $userid = $_GET["userid"];
    else
        $userid = $currentuser["userid"];

    $perm = bbs_admin_getuserperm($userid);
    if(($perm == -1) || ($ret == -1))
        html_error_quit("�����ڵ��û���");
    else {
        $giveupperm = bbs_admin_getgiveupperm($userid);
        admin_header("�ı���Ȩ��", "����ʹ���ߵ�Ȩ��");
?>
<form method="post" action="admperm.php" class="medium">
<fieldset><legend>Ҫ�޸ĵ��û�ID</legend><div class="inputs">
<label>ID:</label><input type="text" name="userid" value="<?php print($userid); ?>" size="12" maxlength="12">
<input type="submit" value="ȷ��"><br>ע�⣺����Ƿ����⣬��ʹ�÷��ѡ����
</div></fieldset></form>
<form method="post" action="admperm.php" class="large" onsubmit="return confirm('ȷʵҪ�޸��û�Ȩ����');">
<fieldset><legend>�޸��û�Ȩ��</legend><div class="inputs">
<label>�û�ID:</label><input type="text" name="modifyuserid" value="<?php print($userid); ?>" size="12" readonly>
<table align="center" border="0" cellpadding="1" cellspacing="0">
<tr><td valign="top">
<?php
    for($i=0; $i<16; $i++) {
        $chk = ($perm & (1 << $i))?" checked":"";
        if($giveupperm & (1 << $i))
            print("<input type=\"checkbox\" name=\"p{$i}\" value=\"o\"{$chk}> <span style=\"color:#ff0000\">". constant("BBS_PERMSTRING{$i}") ."</span><br />");
        else
            print("<input type=\"checkbox\" name=\"p{$i}\" value=\"o\"{$chk}> ". constant("BBS_PERMSTRING{$i}") ."<br />");
    }
?>
<td width="100">&nbsp;</td>
</td><td valign="top">
<?php
    for($i=16; $i<30; $i++) {
        $chk = ($perm & (1 << $i))?" checked":"";
        if($giveupperm & (1 << $i))
            print("<input type=\"checkbox\" name=\"p{$i}\" value=\"o\"{$chk}> <span style=\"color:#ff0000\">". constant("BBS_PERMSTRING{$i}") ."</span><br />");
        else
            print("<input type=\"checkbox\" name=\"p{$i}\" value=\"o\"{$chk}> ". constant("BBS_PERMSTRING{$i}") ."<br />");
    }
?>
</td></tr>
</table><br />
<div align="center"><input type="submit" value="����">&nbsp;<input type="reset" value="����"></div>
</div></fieldset></form><br />
<?php
    }
    page_footer();
?>
