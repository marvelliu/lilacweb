<?php
require("pcadmin_inc.php");
function pc_load_special($link)
{
	$query = "SELECT uid , username , pctype , corpusname , description , visitcount , nodescount FROM users WHERE pctype != 0 ORDER BY username ASC ;";
	$result = mysql_query($query,$link);
	$pcs = array();
	while($rows = mysql_fetch_array($result))
		$pcs[] = $rows;
	return $pcs;
}

pc_admin_check_permission();
$link = pc_db_connect();
pc_html_init("gb2312",$pcconfig["BBSNAME"]."����BLOG����");
pc_admin_navigation_bar();

if($_GET["userid"] && $_GET["conv"])
{
	$pcc = pc_load_infor($link,$_GET["userid"]);
	if(!$pcc)
	{
		html_error_quit($_GET["userid"]."����BLOG");
		exit();
	}
	
	if(!$_GET["statnew"])
		$newtype = 7;
	elseif(!$_GET["statnodes"])
		$newtype = 5;
	elseif(!$_GET["statusers"])
		$newtype = 3;
	else
		$newtype = 1;
		
	if(!$_GET["isgroup"] && !pc_is_groupwork($pcc))
		$newtype -- ;
	
	$query = "UPDATE users SET createtime = createtime , pctype = ".$newtype."  WHERE uid = ".$pcc["UID"]." LIMIT 1;";
	mysql_query($query,$link);
	
	if($_GET["isgroup"] && !pc_is_groupwork($pcc))
		pc_convertto_group($link,$pcc);
	$action = "���� " . $pcc["USER"] . " ��BLOG����(N:".$newtype.";O:".$pcc["TYPE"].")";
	pc_logs($link , $action , "" , $pcc["USER"] );
	
	unset($_GET["userid"]);
}

if($_GET["userid"])
{
	$pcc = pc_load_infor($link,$_GET["userid"]);
	if(!$pcc)
	{
		html_error_quit($_GET["userid"]."����BLOG");
		exit();
	}
?>	
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
<input type="hidden" name="conv" value="1">
������û�����<input type="text" name="userid" value="<?php echo $pcc["USER"]; ?>"><br />
�Ƿ�Ϊ����BLOG:
<input type="checkbox" name="isgroup" value="1" <?php if(pc_is_groupwork($pcc)) echo "checked"; ?> />
(<font color=red>ע��:����BLOG�޷�ת��Ϊ��ͨBLOG</font>)
<br />
�Ƿ�����û�ͳ������
<input type="checkbox" name="statusers" value="1" <?php if($pcc["TYPE"]< 2) echo "checked"; ?> /><br />
�Ƿ������־(������)ͳ������
<input type="checkbox" name="statnodes" value="1" <?php if($pcc["TYPE"]< 4) echo "checked"; ?> /><br />
�Ƿ��������־(������,RSS)ͳ��
<input type="checkbox" name="statnew" value="1" <?php if($pcc["TYPE"]< 6) echo "checked"; ?> /><br />
<input type="submit" value="���" />
<input type="button" value="����" onclick="history.go(-1)" />
</form>
<?php	
}
else
{
	$pcs = pc_load_special($link);
?>
<br /><br />
<p align="center"><b>����BLOG����</b></p><center>
<b>ע�⣺</b>
<font color=red>��</font>��ʾ�Ƿ�Ϊ����BLOG
<font color=red>��</font>��ʾ�Ƿ�����û�ͳ������
<font color=red>��</font>��ʾ�Ƿ������־(������)ͳ������
<font color=red>��</font>��ʾ�Ƿ��������־(������,RSS)ͳ��<br /><br />
<table cellspacing="0" cellpadding="3" class="t1">
<tr>
<td class="t2">�û���</td>
<td class="t2">BLOG��</td>
<td class="t2">������</td>
<td class="t2">��־��</td>
<td class="t2">��</td>
<td class="t2">��</td>
<td class="t2">��</td>
<td class="t2">��</td>
<td class="t2">����</td>
</tr>
<?php
	foreach($pcs as $pc)
	{
		echo "<tr><td class=t4><a href=\"/bbsqry.php?userid=".$pc[username]."\">".$pc[username]."</a></td>".
		     "<td class=t5><a href=\"index.php?id=".$pc[username]."\" title=\"".html_format($pc[description])."\">".html_format($pc[corpusname])."</a></td>".
		     "<td class=t4>".$pc[visitcount]."</td>".
		     "<td class=t3>".$pc[nodescount]."</td><td class=t4>";
		echo (pc_is_groupwork(array("TYPE"=>$pc[pctype])))?"<font color=red><b>Y</b></font>":"n";
		echo "</td><td class=t4>";
		echo ($pc[pctype]<2)?"y":"<font color=red><b>N</b></font>";
		echo "</td><td class=t4>";
		echo ($pc[pctype]<4)?"y":"<font color=red><b>N</b></font>";
		echo "</td><td class=t4>";
		echo ($pc[pctype]<6)?"y":"<font color=red><b>N</b></font>";
		echo "</td><td class=t3><a href=\"".$_SERVER["PHP_SELF"]."?userid=".$pc[username]."\">����</a></td></tr>";
	}
?>
</table>
<br />
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
����BLOG
<input type="text" name="userid">
<input type="submit" value="����">
</form></center>
<?php	
}
pc_db_close($link);
pc_admin_navigation_bar();
html_normal_quit();
?>