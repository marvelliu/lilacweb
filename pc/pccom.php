<?php
	/*
	** some comments actions in personal corp.
	** @id:windinsn  Nov 19,2003
	*/
	require("pcfuncs.php");
	
	$nid = (int)($_GET["nid"]);
	$act = $_GET["act"];
	@$cid = (int)($_GET["cid"]);
		
	$link =	pc_db_connect();
	$query = "SELECT `access`,`uid` FROM nodes WHERE `nid` = '".$nid."' AND `type` != '1' AND `comment` != '0';";
	$result = mysql_query($query,$link);
	$rows = mysql_fetch_array($result);
	mysql_free_result($result);
		
	$pc = pc_load_infor($link,"",$rows["uid"]);
	if(!$pc)
        {
               	html_error_quit("�Բ�����Ҫ�鿴��Blog������");
               	exit();
        }


	if(!$pc["ANONYCOMMENT"]) {
		if ($loginok != 1) {
			html_nologin();
			exit;
		}
		elseif(!strcmp($currentuser["userid"],"guest"))
		{
			html_init("gb2312");
			html_error_quit("guest ���ܷ�������!\n<br>\n<a href=\"/\" target=\"_top\">���ڵ�¼</a>");
			exit();
		}
	}

	pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog","","","",1);		
		
	if(!$rows)
	{
		html_error_quit("�����۵����²�����!");
		exit();
	}
	
	$uid = $rows["uid"];
	
	if(!$pc["ANONYCOMMENT"])
		if(!pc_can_comment($link , $uid))
		{
			html_error_quit("�Բ��������޸�BLOG������Ȩ�ޣ�");
			exit();
		}	
	
               
    $userPermission = pc_get_user_permission($currentuser,$pc);
	$sec = $userPermission["sec"];
	$pur = $userPermission["pur"];
	$tags = $userPermission["tags"];
	if(!$tags[$rows["access"]])
	{
		html_error_quit("�Բ��������ܲ鿴������¼!");
		exit();
	}
		
		
	if($act == "pst")
	{
?>
<br><center>		
<form name="postform" action="pccom.php?act=add&nid=<?php echo $nid; ?>" method="post" onsubmit="return submitwithcopy();">
<table cellspacing="0" cellpadding="5" width="90%" border="0" class="t1">
<tr>
	<td class="t2">��������</td>
</tr>
<tr>
	<td class="t8">
	����
	<input type="text" name="subject" maxlength="200" size="100" class="f1">
	</td>
</tr>
<tr>
	<td class="t13">�������</td>
</tr>
<tr>
	<td class="t5"><?php @require("emote.html"); ?></td>
</tr>
<tr>
	<td class="t11">����
	<input type="checkbox" name="htmltag" value=1 checked>ʹ��HTML���
	</td>
</tr>
<tr>
	<td class="t8"><textarea name="blogbody" class="f1" style="width:100%" rows="20" id="blogbody" wrap="physical">
	<?php echo $pcconfig["EDITORALERT"].@$_POST["blogbody"]; ?>
	</textarea></td>
</tr>
<tr>
	<td class="t2">
	<input type="button" name="ins" value="����HTML" class="b1" onclick="return insertHTML();" />
	<input type="button" name="hil" value="����" class="b1" onclick="return highlight();" />
	<input type="submit" name="postbutton" id="postbutton" value="��������" class="b1">
	<input type="button" value="������ҳ" class="b1" onclick="doCancel();">
</tr>
</table>
</form></center>	
<p align="center">
<?php
    /**
     *    ˮľ��web����bbslib��cgi�������޸���NJUWWWBBS-0.9���˲���
     * ������ѭԭ�е�nju www bbs�İ�Ȩ������GPL����php���ֵĴ��루
     * phplib�Լ�phpҳ�棩������ѭGPL�����ڿ���ʹ����������Դ��İ�
     * Ȩ������BSD����MPL֮�ࣩ��
     *
     *   ϣ��ʹ��ˮľ�����Webվ�����powered by kbs��ͼ��.��ͼ��
     * λ��html/images/poweredby.gifĿ¼,����ָ��http://dev.kcn.cn
     * ʹ��ˮľ�����վ�����ͨ��dev.kcn.cn��ô����������Ϣ.
     *
     */
    powered_by_smth();
?>
<p>
<?php
	}
	else
	{
		if(!$_POST["subject"])
		{
			html_error_quit("���������۱���!");
			exit();
		}
		$ret = pc_add_comment($link,$pc,$nid,intval(($_POST["emote"])),$currentuser["userid"],$_POST["subject"],html_editorstr_format($_POST["blogbody"]),(($_POST["htmltag"]==1)?1:0),false);
        switch($ret) {
            case -6:
                html_error_quit("����ϵͳԭ��������ʧ��");
                break;
            case -9:
                echo "<script language=\"javascript\">alert('�������¿��ܺ��в����ʻ㣬��ȴ�����Ա��ˡ�');</script>";
                break;
            default:    
        }
            
?>
<script language="javascript">
window.location.href="pccon.php?id=<?php echo $uid; ?>&nid=<?php echo $nid; ?>";
</script>
<?php
    }		
	pc_db_close($link);
	html_normal_quit();
?>
