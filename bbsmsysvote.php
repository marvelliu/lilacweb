<?php
	/*
	** ID:windinsn dec 29,2003
	**/
	require("funcs.php");
login_init();
	require("libvote.php");
	
	if ($loginok != 1 || $currentuser[userid] == "guest" )
		html_nologin();
	
	html_init("gb2312","����ϵͳͶƱ","",1);
?>
<script language='JavaScript'>
<!--
function bbs_confirm(url,infor){
	if(confirm(infor)){
		window.location.href=url;
		return true;
		}
	return false;
}
-->
</script>
<br /><p align=center><?php echo BBS_FULL_NAME; ?>ϵͳͶƱ����</p>
<?php
	if(!sysvote_is_admin($currentuser))
		html_error_quit("�Բ�������Ȩ���ʸ�ҳ��!");
	else
	{
		$link = sysvote_db_connect();
		if($_GET["act"]=="create")
		{
			$sysVoteAdmin = new sysVoteAdmin;
			switch($_GET["step"])
			{
				case 2:
					$sysVoteAdmin->svCreateVoteStepTwo();
					break;
				case 3:
					$sysVoteAdmin->svCreateVoteStepThree();
					break;
				case 4:
					$sysVoteAdmin->svCreateVoteStepFour();
					break;
				case 5:
					$sysVoteAdmin->svCreateVoteStepFive($link);
					break;
				default:
					$sysVoteAdmin->svCreateVoteStepOne();
			}
		}
		elseif($_GET["act"]=="log" && $_GET["svid"])
		{
			$vote = sysvote_load_vote_infor($link,$_GET["svid"],TRUE);
			echo 	"����: ".$vote[subject]."<hr size=1><br />".html_format($vote[logs],TRUE);
?>
<p align=center>
<a href="bbsmsysvote.php">����</a>
<a href="javascript:history.go(-1)">���ٷ���</a>
</p>
<?php
		}
		elseif($_GET["act"]=="del" && $_GET["svid"])
		{
			$sysVoteAdmin = new sysVoteAdmin;
			$sysVoteAdmin->svDelVote($link,$_GET["svid"]);
			
?>
<p align=center>
<br />ɾ���ɹ���<br /><br />
<a href="bbsmsysvote.php">����</a>
<a href="javascript:history.go(-1)">���ٷ���</a>
</p>
<?php
		}
		elseif($_GET["act"]=="end" && $_GET["svid"] && isset($_GET["ann"]))
		{
			$sysVoteAdmin = new sysVoteAdmin;
			$sysVoteAdmin->svEndVote($link,$_GET["svid"],$_GET["ann"],$_GET["annBoard"]);
			
?>
<p align=center>
<br />ͶƱ�ѹرգ�<br /><br />
<a href="bbsmsysvote.php">����</a>
<a href="javascript:history.go(-1)">���ٷ���</a>
</p>
<?php
		}
		elseif($_GET["act"]=="end" && $_GET["svid"])
		{
?>
<form action="bbsmsysvote.php" method="get">
<input type="hidden" name="act" value="end">
<input type="hidden" name="svid" value="<?php echo $_GET["svid"]; ?>">
<p align=left>
<strong>�Ƿ񷢲����棺</strong>
<ul>
<li><input type="radio" name="ann" value="0" checked>����������
</li><li><input type="radio" name="ann" value="1">��vote�淢������
</li><li><input type="radio" name="ann" value="2">��<?php echo $sysVoteConfig["BOARD"]; ?>�淢������
</li><li><input type="radio" name="ann" value="3">��<input type="text" name="annBoard" class=b9 >�淢������
</li></ul>
<input type="submit" value="����ͶƱ" class=b9>
</p>
</form>
<?php		
		}
		else
		{
			$votes = sysvote_load_votes($link,(int)($_GET["pno"]),TRUE);
?>
<center>
<table cellspacing=0 cellpadding=5 width=99% class=t1>
	<tr>
		<td width=30 class=t2>���</td>
		<td width=45 class=t2>״̬</td>
		<td class=t2>����</td>
		<td width=30 class=t2>����</td>
		<td width=30 class=t2>IP</td>
		<td width=135 class=t2>����ʱ��</td>
		<td width=135 class=t2>���һ��ͶƱ</td>
		<td width=50 class=t2>����</td>
		<td width=50 class=t2>����</td>
		<td width=30 class=t2>����</td>
		<td width=30 class=t2>ɾ��</td>
		<td width=30 class=t2>Log</td>
	</tr>
<?php
			$j = 1;
			foreach($votes as $vote)
			{
				echo "<tr>".
				     "<td class=t3>".$j."</td>".
				     "<td class=t4>";
				if($vote[active]==1)
				{
					echo "ͶƱ��";
					$linkUrl = "bbssysvote.php?svid=".$vote[svid];
				}
				else
				{
					echo "�ѽ���";
					$linkUrl = "bbsssysvote.php?svid=".$vote[svid];
				}	
				echo "</td>".
				     "<td class=t5><span title=\"".$vote[desc]."\"><a href=\"".$linkUrl."\">".$vote[subject]."</a><span></td>".
				     "<td class=t4>";
				echo ($vote[anonymous]==1)?"����":"��ֹ";
				echo "</td><td class=t3>".$vote[votesperip].
				     "</td><td class=t4>".$vote[created]."</td>".
				     "<td class=t3>".$vote[changed]."</td>".
				     "<td class=t4>".$vote[votecount]."</td>".
				     "<td class=t3>".$vote[timelong]."</td>".
				     "<td class=t4>";
				if($vote[active]==1)
					echo "<a href=\"#\" onclick=\"bbs_confirm('bbsmsysvote.php?act=end&svid=".$vote[svid]."','ȷ��Ҫ����ͶƱ?')\">����</a>";
				else
					echo "-";
				echo "</td><td class=t3><a href=\"#\" onclick=\"bbs_confirm('bbsmsysvote.php?act=del&svid=".$vote[svid]."','ȷ��ɾ��ͶƱ?')\">ɾ��</a></td>".
				     "<td class=t4><span title='�鿴����ͶƱ��ϵͳ��¼'><a href=\"bbsmsysvote.php?act=log&svid=".$vote[svid]."\">�鿴</a></span></td></tr>\n";
				$j ++;
			}
?>
</table>
</center>
<?php
			echo "<p align=right>";
			$pno = $_GET["pno"];
			if($pno < 1) $pno = 1;
			if( $pno > 1) echo "<a href=\"".$_SERVER["PHP_SELF"]."?pno=".($pno-1)."\">��һҳ</a>\n";
			if(count($votes) == $sysVoteConfig["PAGESIZE"]) echo "<a href=\"".$_SERVER["PHP_SELF"]."?pno=".($pno+1)."\">��һҳ</a>\n";
			echo "</p>";	
			
	}
?>
<hr size=0>
<p align=center>
[<a href="bbsmsysvote.php">�������</a>]
[<a href="bbsmsysvote.php?act=create">������ͶƱ</a>]

</p>
<?php			
	sysvote_db_close($link);
	html_normal_quit();
	}
?>
