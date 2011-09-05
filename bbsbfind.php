<?php
	require_once("www2-funcs.php");
	require_once('www2-board.php');
	login_init();
	bbs_session_modify_user_mode(BBS_MODE_READING);
	if( !isset($_GET["board"]) && !isset($_POST["board"]))
		html_error_quit("�����������");
	if( isset($_GET["board"]) )
		$board = $_GET["board"];
	else
		$board = $_POST["board"];

	$brdarr = array();
	$brdnum = bbs_getboard($board, $brdarr);
	if ($brdnum == 0){
		html_error_quit("�����������");
	}
	$usernum = $currentuser["index"];
	if (bbs_checkreadperm($usernum, $brdnum) == 0){
		html_error_quit("�����������");
	}
	$board = $brdarr["NAME"];
	$isnormalboard = bbs_normalboard($board);

	if ($isnormalboard) {
		$dotdirname = bbs_get_board_index($board, $dir_modes["NORMAL"]);
		if (cache_header("public",@filemtime($dotdirname),300))
			return;
	}
	bbs_board_header($brdarr,-2,0);
	$ann_path = bbs_getannpath($board);
	if ($ann_path != FALSE)	{
		if (!strncmp($ann_path,"0Announce/",10))
			$ann_path = substr($ann_path,9);
	}
?>
<script type="text/javascript">
<!--
var c = new docWriter('<?php echo addslashes($board); ?>',<?php echo $brdarr["BID"]; ?>,0,0,-2,0,0,'<?php echo addslashes($ann_path); ?>',0);
//-->
</script>
<?php
	if(!isset($_GET["q"])){
?>
<script type="text/javascript">
<!--
var str = '<table class="main wide"><tr height="5"><th></th></tr></table></div>';<?php /* </div>: dirty way ... for closing <div class="doc"> */ ?>
document.write(str + getFindBox('<?php echo $brdarr["NAME"]; ?>'));
//-->
</script>
<?php
		page_footer();
		exit;
	}

	if( isset( $_GET["title"] ) ){
		$title = $_GET["title"];
	}else
		$title="";

	if( isset( $_GET["title2"] ) ){
		$title2 = $_GET["title2"];
	}else
		$title2="";

	if( isset( $_GET["title3"] ) ){
		$title3 = $_GET["title3"];
	}else
		$title3="";

	if( isset( $_GET["userid"] ) ){
		$userid = $_GET["userid"];
	}else
		$userid="";

	if( isset( $_GET["dt"] ) ){
		$dt = $_GET["dt"];
	}else
		$dt=0;
	settype($dt, "integer");
	if($dt <= 0)
		$dt = 7;
	else if($dt > 9999)
		$dt = 9999;

	if( isset( $_GET["mg"] ) ){
		$mg = $_GET["mg"];
	}else
		$mg = "";
	if($mg != "")
		$mgon=1;
	else
		$mgon=0;

	if( isset( $_GET["og"] ) ){
		$og = $_GET["og"];
	}else
		$og = "";
	if($og != "")
		$ogon=1;
	else
		$ogon=0;

	if( isset( $_GET["ag"] ) ){
		$ag = $_GET["ag"];
	}else
		$ag = "";
	if($ag != "")
		$agon=1;
	else
		$agon=0;


function log_it($id, $ip, $board, $title, $title2, $title3, $userid, $dt, $mgon, $agon, $ogon, $num) {
	$action = $board . " | " . $title . " | " . $title2 . " | " . $title3 . " | " . $userid . " | " . $dt . " | " . $mgon . " | " . $agon . " | " . $ogon . " | " . $num;
    $action = "[".date("Y-m-d H:i:s")."] $id($ip) | ".$action."\n";
    $logs = BBS_HOME . "/bbsbfind.log";
    if(!($fn = fopen($logs,"a")))
        return FALSE;
    if (!flock ($fn, LOCK_EX))
    {
        fclose ($fh);
        return FALSE;
    }

    fputs($fn,$action);
    flock ($fn, LOCK_UN);
    fclose($fn);
    return TRUE;
}

	$articles = bbs_search_articles($board, $title, $title2, $title3, $userid, $dt, $mgon, $agon, $ogon);

	if( $articles <= 0 ){
		html_error_quit("ϵͳ����:".$articles);
	}
//	log_it($currentuser["userid"], $_SERVER["REMOTE_ADDR"], $board, $title, $title2, $title3, $userid, $dt, $mgon, $agon, $ogon, count($articles));
?>
<script>
var ta = new tabWriter(0,'main wide',0,[['���','6%','center'],['���','6%','center'],['����','12%','center'],['����','8%','center'],['����',0,0]]);
<?php
	foreach ($articles as $article)
	{
		$flags = $article["FLAGS"];
		$col1 = $article["NUM"]+1;
		$col2 = $flags[0] . $flags[3];
		$col3 = '<a href="bbsqry.php?userid=' . $article["OWNER"] . '">' . $article["OWNER"] . '</a>';
		$col4 = strftime("%b&nbsp;%e", $article["POSTTIME"]);
		$col5 = '<a href="bbscon.php?bid=' . $brdnum . '&id=' . $article["ID"] . '">' . htmlspecialchars($article["TITLE"], ENT_QUOTES) . ' </a>';
		echo "ta.r('$col1','$col2','$col3','$col4','$col5');\n";
	}
	$i = count($articles);
?>
ta.t();
</script>
</div><?php /* </div>: dirty way ... for closing <div class="doc"> */ ?>
<div class="oper">
����������"<?php echo $brdarr["NAME"];?>"��, ���⺬: "<?php echo htmlspecialchars($title,ENT_QUOTES);?>"<?php if($title2!="") echo ' ��"'.htmlspecialchars($title2,ENT_QUOTES).'"';?><?php if($title3!="") echo ',����"'.htmlspecialchars($title3,ENT_QUOTES).'"';?> ����Ϊ: "<?php if($userid!="") echo $userid; else echo "������";?>", "<?php echo $dt;?>"�����ڵ� <?php if($mgon) echo "����"; if($agon) echo "����"; if($ogon) echo "����";?>����<br/>
���ҵ� <?php echo $i;?> ƪ���·������� <?php if($i>=999) echo "(ƥ��������, ʡ�Ե�1000�Ժ�Ĳ�ѯ���)";?>
[<a href="bbsdoc.php?board=<?php echo $brdarr["NAME"];?>">���ر�������</a>] [<a href="javascript:history.go(-1)">������һҳ</a>]
</div>
<?php
	page_footer();
?>
