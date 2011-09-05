<?php
	require("www2-funcs.php");
	login_init();
	$usernum = $currentuser["index"];
	if (isset($_GET["board"]))
		$board = $_GET["board"];
	else 
		html_error_quit("�����������");

	$brdarr = array();
	$brdnum = bbs_getboard($board, $brdarr);
	if ($brdnum == 0)
		html_error_quit("�����������");
	if (bbs_checkreadperm($usernum,$brdnum)==0)
		html_error_quit("�����������");
	$top_file= bbs_get_vote_filename($brdarr["NAME"], "notes");
	$isnormalboard = bbs_normalboard($board);
	if ($isnormalboard) {
		$mt = file_exists($top_file) ? @filemtime($top_file) : time();
		if (cache_header("public",$mt,1800))
			return;
	}

	bbs_board_nav_header($brdarr, "����¼");
	$brd_encode = urlencode($brdarr["NAME"]);
?>
<link rel="stylesheet" type="text/css" href="static/www2-ansi.css"/>
<script type="text/javascript" src="static/www2-addons.js"></script>
<div id="divNote" class="AnsiArticleBW"><div id="dn1">
<script type="text/javascript"><!--
<?php
	$s = false;
	if (file_exists($top_file)) {
		$s = bbs2_readfile($top_file);
	}
	if (!is_string($s))
	{
		$s = 'prints("\n\n\n�����������ޡ�����¼����\n\n\n");';
	}
?>
triggerAnsiDiv('divNote','dn1');
<?php
				echo $s;
?>
//-->
</script></div></div>
<div class="oper">
[<a href="bbsdoc.php?board=<?php echo $brd_encode; ?>">��������</a>]
<?php
	if (bbs_is_bm($brdnum,$usernum)) {
?>
[<a href="bbsmnote.php?board=<?php echo $brd_encode; ?>">�༭���滭��</a>]
<?php
	}
?> 
[<?php bbs_add_super_fav ('[����¼] '.$brdarr['DESC'], 'bbsnot.php?board='.$brd_encode); ?>]
</div>
<?php
	page_footer();
?>
