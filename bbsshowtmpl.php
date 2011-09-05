<?php
	require("www2-funcs.php");
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_READING);
	assert_login();

	if(isset($_GET["board"]))
		$board = $_GET["board"];
	else
		html_error_quit("����������");

	$brdarr = array();
	$brdnum = bbs_getboard($board,$brdarr);
	if($brdnum == 0)
		html_error_quit("�����������");

	if(bbs_checkreadperm($currentuser["index"],$brdnum)==0)
		html_error_quit("��û��Ȩ��");

	$votearr = array();
	$retnum = bbs_get_tmpls($board,$votearr);

	if( $retnum < 0 )
		$retnum = 0;

	if(isset($_GET["id"]))
		$restr = "&reid=" . $_GET["id"];
	else
		$restr = "";
		
	
	bbs_board_nav_header($brdarr, "ģ���б�");
?>
<table class="main adj">
<caption>���� <?php echo $board; ?> ���� <?php echo $retnum;?> ��ģ��</caption>
<tr><th>���</th><th>����</th><th>����</th><th>�������</th><th></th></tr>
<?php
		for($i = 0; $i < $retnum; $i++ ){
?>
<tr><td>
<?php echo $i+1;?>
</td><td>
<a href="bbsatmpl.php?board=<?php echo $board;?>&num=<?php echo $i+1;?>"><?php echo $votearr[$i]["TITLE"];?></a>
</td><td>
<?php echo $votearr[$i]["TITLE"];?>
</td><td>
<?php echo $votearr[$i]["CONT_NUM"];?>
</td><td>
<a href="bbspsttmpl.php?board=<?php echo $board;?>&num=<?php echo $i+1; echo $restr;?>">ʹ�ñ�ģ�巢��</a>
</td></tr>
<?php
	}
?>
</table>
<div class="oper">
<a href="bbsdoc.php?board=<?php echo $board;?>">���ر�������</a>
<a href="javascript:history.go(-1)">���ٷ���</a>
</div>
<?php
	page_footer();
?>
