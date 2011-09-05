<?php
	require("www2-admin.php");

    admin_check("info");

    if(isset($_POST["modifyuserid"])) {
        $userid = $_POST["modifyuserid"];
        $username = $_POST["username"];
        $realname = $_POST["realname"];
        $address = $_POST["address"];
        $email = $_POST["email"];
        if($_POST["gender"] == "M")
            $gender = 77;
        else
            $gender = 70;
        $birthyear = $_POST["birthyear"];
        $birthmonth = $_POST["birthmonth"];
        $birthday = $_POST["birthday"];
        $title = $_POST["title"];
        $realemail = $_POST["realemail"];
        $numlogins = $_POST["numlogins"];
        $numposts = $_POST["numposts"];
        if(@$_POST["firstlogin"] == "yes")
            $firstlogin = 1;
        else
            $firstlogin = 0;
        if(@$_POST["lastlogin"] == "yes")
            $lastlogin = 1;
        else
            $lastlogin = 0;
        $ret = bbs_admin_setuserinfo($userid, $username, $realname, $address, $email, $gender, $birthyear, $birthmonth, $birthday, $title, $realemail, $numlogins, $numposts, $firstlogin, $lastlogin);
        switch($ret) {
        case 0:
            html_success_quit("�����޸ĳɹ���", array("<a href=\"adminfo.php?userid={$userid}\">���������޸�ҳ��</a>"));
            break;
        case 1:
        case 2:
        case 3:
            html_error_quit("���ղ���ȷ��");
            break;
        case 4:
            html_error_quit("�����ڵ��û�ְ��");
            break;
        default:
        }
    }


    if(isset($_POST["userid"]))
        $userid = $_POST["userid"];
    else if(isset($_GET["userid"]))
        $userid = $_GET["userid"];
    else
        $userid = $currentuser["userid"];
    
    $userinfo = array();
    $uid = bbs_admin_getuserinfo($userid, $userinfo);
    if($uid == -1)
        html_error_quit("�����ڵ��û���");
    if($uid > 0) {
        admin_header("�ı�������", "�޸��û�����");
        
    for($i=1; $i<256; $i++) {
        $usertitles[$i-1] = bbs_admin_getusertitle($i);
    }
        
?>
<form method="post" action="adminfo.php" class="medium">
<fieldset><legend>Ҫ�޸ĵ��û�ID</legend><div class="inputs">
<label>ID:</label><input type="text" name="userid" value="<?php print($userid); ?>" size="12" maxlength="12">
<input type="submit" value="ȷ��"><br>
Ϊ�˱����ظ��ύ��ҳ��������Ҫ�����ƻ����û�ID����������telnet��ʽ��¼�޸ġ�
</div></fieldset></form>
<form method="post" action="adminfo.php" class="medium" onsubmit="return confirm('ȷʵҪ�޸��û�������');">
<fieldset><legend>��������</legend><div class="inputs">
<label>�ʺ�:</label><input type="text" name="modifyuserid" value="<?php echo $userinfo["userid"];?>" size="12" readonly><br/>
<label>�ǳ�:</label><input type="text" name="username" value="<?php echo htmlspecialchars($userinfo["username"],ENT_QUOTES);?>" size="24" maxlength="39"><br/>
<label>��ʵ����:</label><input type="text" name="realname" value="<?php echo $userinfo["realname"];?>" size="16" maxlength="39"><br/>
<label>��ס��ַ:</label><input type="text" name="address" value="<?php echo $userinfo["address"];?>" size="40" maxlength="79"><br/>
<label>��������:</label><input type="text" name="email" value="<?php echo $userinfo["email"];?>" size="40" maxlength="79"><br/>
<label>�Ա�:</label><input type="radio" name="gender" value='M'<?php echo ($userinfo["gender"]==77)?" checked":""; ?>>�� <input type="radio" name="gender" value="F"<?php echo ($userinfo["gender"]==77)?"":" checked"; ?>>Ů<br />
<label>����:</label><input type="text" name="birthyear" value="<?php echo $userinfo["birthyear"]+1900; ?>" size="4" maxlength="4"> �� <input type="text" name="birthmonth" value="<?php echo $userinfo["birthmonth"]; ?>" size="2" maxlength="2"> �� <input type="text" name="birthday" value="<?php echo $userinfo["birthday"]; ?>" size="2" maxlength="2"> ��<br/>
<label>��ǰְ��:</label>
<select name="title">
<option value="0">[û��ְ��]</option>
<?php
        for($i=1; $i<256; $i++) {
            if($usertitles[$i-1] != "")
                print("<option value=\"{$i}\"" . (($userinfo["title"]==$i)?" selected":"") . ">{$usertitles[$i-1]}</option>");
        }
?>
</select>
<br/>
<label>��ʵEmail:</label><input type="text" name="realemail" value="<?php echo $userinfo["realemail"];?>" size="40" maxlength="79"><br/>
<label>��վ����:</label><input type="text" name="numlogins" value="<?php echo $userinfo["numlogins"];?>" size="6" maxlength="7"><br/>
<label>�������:</label><input type="text" name="numposts" value="<?php echo $userinfo["numposts"];?>" size="6" maxlength="7"><br/>
<label>ע��ʱ��:</label><?php echo date("D M j H:i:s Y",$userinfo["firstlogin"]);?> <input type="checkbox" name="firstlogin" value="yes">��ǰ1����<br/>
<label>�������:</label><?php echo date("D M j H:i:s Y",$userinfo["lastlogin"]);?> <input type="checkbox" name="lastlogin" value="yes">��Ϊ����<br/>
</div></fieldset>
<div class="oper">
<input type="submit" name="submit" value="ȷ��" /> <input type="reset" value="����" />
</div>
</form>
<?php
    }
	page_footer();
?>
