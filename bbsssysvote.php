<?php
	/*
	** ID:windinsn dec 29,2003
	**/
	require("funcs.php");
login_init();
	require("libvote.php");
	
	if ($loginok != 1 || !isset($currentuser) )
		html_nologin();
	
	html_init("gb2312","ϵͳͶƱ","",1);
?>
<br /><p align=center><strong><?php echo BBS_FULL_NAME; ?>ϵͳͶƱ</strong></p>
<?php
	
	$link = sysvote_db_connect();
	$vote = sysvote_load_vote_infor($link,$_GET["svid"]);
	if(!$vote || $vote[active]!=0)
		html_error_quit("�Բ�����ѡ���ͶƱ��������ڣ�");
	
?>
<center>
<table cellspacing=0 cellpadding=5 width=100% class=t1 border=0>
	<tr>
		<td class=t2 colspan=2>�鿴ͶƱ���</td>
	</tr>
	<tr>
		<td class=t3 width=100>ͶƱ����</td>
		<td class=t7><?php echo $vote[subject]; ?></td>
	</tr>
	<tr>
		<td class=t3>ͶƱ˵��</td>
		<td class=t7><?php echo $vote[desc]; ?></td>
	</tr>
	<tr>
		<td class=t3>����ʱ��</td>
		<td class=t7><?php echo $vote[created]; ?></td>
	</tr>
	<tr>
		<td class=t3>����ʱ��</td>
		<td class=t7><?php echo $vote[changed]; ?></td>
	</tr>
	<tr>
		<td class=t3>��������</td>
		<td class=t7><?php echo $vote[votecount]; ?></td>
	</tr>
	<tr>
		<td class=t3>ͶƱ���</td>
		<td class=t7><?php echo sysvote_display_result($vote); ?>&nbsp;</td>
	</tr>
	
</table>
</center>
<p align=center>
[<a href="/<?php echo MAINPAGE_FILE; ?>">��ҳ����</a>]
[<a href="bbssysvote.php">ϵͳͶƱ</a>]
[<a href="javascript:history.go(-1)">���ٷ���</a>]
</p>
<?php	
	sysvote_db_close($link);
	html_normal_quit();
?>
