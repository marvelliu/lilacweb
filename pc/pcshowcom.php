<?php
	/*
	** this file display single comment
	** @id:windinsn nov 27,2003
	*/
	require("pcfuncs.php");
	
	$cid = (int)($_GET["cid"]);
	
	$link = pc_db_connect();
	$query = "SELECT * FROM comments WHERE `cid` = '".$cid."' LIMIT 0 , 1 ;";
	$result = mysql_query($query,$link);
	$comment = mysql_fetch_array($result);
	if(!$comment)
	{
		@mysql_free_result($result);
		pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog");
		html_error_quit("�Բ�����Ҫ�鿴�����۲�����");
		exit();
	}
	$query = "SELECT `access`,`uid`,`subject`,`emote`,`tid`,`pid`,`nodetype` FROM nodes WHERE `nid` = '".$comment["nid"]."' LIMIT 0 , 1 ;";
	$result = mysql_query($query,$link);
	$node = mysql_fetch_array($result);
	if(!$node)
	{
		@mysql_free_result($result);
		pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog");
		html_error_quit("�Բ�����Ҫ�鿴�����۲�����");
		exit();
	}
	
	$pc = pc_load_infor($link,"",$node["uid"]);
	if(!$pc)   
        {   
               	pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog");
		html_error_quit("�Բ�����Ҫ�鿴��Blog������");   
               	exit();   
        }
	
	function pc_add_new_comment($nid,$alert)
	{
		global $pc, $currentuser;
?>
<center>
<table cellspacing="0" cellpadding="5" width="500" border="0" class="t1">
<tr>
	<td class="t5"><strong>�������� </strong>
	<?php if($alert && !$pc["ANONYCOMMENT"]){ ?>
	<font class=f4>
	ע�⣺���б�վ��¼�û����ܷ������ۡ�<br />
	<?php bbs_login_form(); ?>
	</font>
	<?php } ?>
	</td>
</tr>
<?php
		if($alert && !$pc["ANONYCOMMENT"]) {
			echo '</table><br/><br/>';
			return;
		}
?>
<form name="postform" action="pccom.php?act=add&nid=<?php echo $nid; ?>" method="post" onsubmit="if(this.subject.value==''){alert('��������������!');return false;}">
<tr>
	<td class="t8">
	����
	<input type="text" name="subject" maxlength="200" size="60" class="f1">
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
<?php
		if(strtolower($currentuser["userid"]) == "guest")
			print("<br><span style=\"color:#FF0000\">��û�е�¼���������������ۣ�һ�������޷��޸ġ�</span>");
?>
	<input type="hidden" name="htmltag" value=0>
	</td>
</tr>
<tr>
	<td class="t8"><textarea name="blogbody" class="f1" cols="60" rows="10" id="blogbody"  onkeydown='if(event.keyCode==87 && event.ctrlKey) {document.postform.submit(); return false;}'  onkeypress='if(event.keyCode==10) return document.postform.submit()' wrap="physical"></textarea></td>
</tr>
<tr>
	<td class="t5">
	<input type="submit" value="��������" class="f1">
	<input type="button" value="������ҳ" class="f1" onclick="history.go(-1)">
	<input type="button" value="ʹ��HTML�༭��" class="f1" onclick="window.location.href='pccom.php?act=pst&nid=<?php echo $nid; ?>';">
</tr>
</table>
</form></center>
<?php		
	}
               
        $userPermission = pc_get_user_permission($currentuser,$pc);
	$sec = $userPermission["sec"];
	$pur = $userPermission["pur"];
	$tags = $userPermission["tags"];
	if(!$tags[$node["access"]])
	{
		pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog");
		html_error_quit("�Բ��������ܲ鿴������¼!");
		exit();
	}
	
	if(!($pur == 3 && !pc_is_groupwork($pc)) &&  $node["nodetype"]==0)
	{
		pc_counter($link);
		pc_ncounter($link,$comment["nid"]);
	}
	
	pc_html_init("gb2312",$pcconfig["BBSNAME"]."Blog");
?>
<br>
<center>
<table cellspacing="0" cellpadding="5" border="0" width="90%" class="t1">
<tr>
	<td class="t2">
	<img src="icon/<?php echo $node["emote"]; ?>.gif" border="0" alt="�������" align="absmiddle">
	��
	<a href="pccon.php?<?php echo "id=".$node["uid"]."&nid=".$comment["nid"]."&pid=".$node["pid"]."&tid=".$node["tid"]."&tag=".$node["access"]; ?>" class="t2">
	<?php echo html_format($node["subject"]); ?>
	</a>
	��
	������
	</td>
</tr>
<tr>
	<td class="t8">
	<?php
		echo "<a href=\"/bbsqry.php?userid=".$comment["username"]."\">".$comment["username"]."</a>\n".
			"�� ".time_format($comment["created"])." �ᵽ:\n";
	?>
	</td>
</tr>
<tr>
	<td class="t13">
	<img src="icon/<?php echo $comment["emote"]; ?>.gif" border="0" alt="�������" align="absmiddle">
	<strong>
	<?php echo html_format($comment["subject"]); ?>
	</strong>
	</td>
</tr>
<tr>
	<td class="t13" height="200" align="left" valign="top">
	<font class="<?php echo ($comment["htmltag"])?"contentwithhtml":"content"; ?>">
	<?php echo html_format($comment["body"],TRUE,$comment["htmltag"]); ?>
	</font>
	</td>
</tr>
<tr>
	<td class="t5" align="right">
	[FROM:
	<?php echo pc_hide_ip($comment["hostname"]); ?>
	]
	&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
</tr>
<tr>
	<td class="t3">
<?php
	$query = "SELECT `cid` FROM comments WHERE `nid` = '".$comment["nid"]."' AND `cid` < '".$cid."' ORDER BY `cid` DESC LIMIT 0 , 1 ;";
	$result = mysql_query($query,$link);
	if($rows = mysql_fetch_array($result))
		echo "<a href=\"pcshowcom.php?cid=".$rows["cid"]."\">��һƪ</a> \n";
	else
		echo "��һƪ \n";
	$query = "SELECT `cid` FROM comments WHERE `nid` = '".$comment["nid"]."' AND `cid` > '".$cid."' ORDER BY `cid` ASC LIMIT 0 , 1 ;";
	$result = mysql_query($query,$link);
	if($rows = mysql_fetch_array($result))
		echo "<a href=\"pcshowcom.php?cid=".$rows["cid"]."\">��һƪ</a> \n";
	else
		echo "��һƪ \n";
	mysql_free_result($result);
?>	
	<a href="pccon.php?<?php echo "id=".$node["uid"]."&nid=".$comment["nid"]."&pid=".$node["pid"]."&tid=".$node["tid"]."&tag=".$node["access"]; ?>">����ԭ��</a>
	<a href="pccom.php?act=pst&nid=<?php echo $comment["nid"]; ?>">��������</a>
	<a href="/bbspstmail.php?userid=<?php echo $comment["username"]; ?>&title=�ʺ�">���Ÿ�<?php echo $comment["username"]; ?></a>
	<?php bbs_add_super_fav ($node["subject"], '/pc/pcshowcom.php?cid='.$cid); ?>
	</td>
</tr>
</table>
<hr size=1>
<?php 
	$alert = ($loginok != 1 || !strcmp($currentuser["userid"],"guest"))?TRUE:FALSE;
	pc_add_new_comment($comment["nid"],$alert); 
?>
</center>
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
</p>
<?php	
	pc_db_close($link);
	html_normal_quit();	
?>
