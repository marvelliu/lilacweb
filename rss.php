<?php
require("www2-funcs.php");
require("www2-rss.php");
login_init(FALSE, TRUE);

$query = $_SERVER["QUERY_STRING"];
settype($query, "string");
if (strlen($query) < 2) die;
$type = substr($query, 0, 1);
$board = substr($query, 1);

// ����û��ܷ��Ķ��ð�
$brdarr = array();
$isnormalboard = bbs_safe_getboard(0, $board, $brdarr);
if (is_null($isnormalboard)) {
	die;
}
if (strcmp($board, $brdarr["NAME"])) die; //cache consideration
$brdnum = $brdarr["BID"];
if ($brdarr["FLAG"]&BBS_BOARD_GROUP) {
	die;
}

/*
 * �ڲ����治�ṩ rss �����ɣ�
 * 1. rss ����� rss ��վ�޷���ȷʵ�� session-based ��¼������ʵ����Ҳ������
 * 2. �ڲ������������� rss ����� rss ��վ����������������Ϣй¶
 * 3. �ڲ����� rss �޷��� squid ǰ�˻��棬�������Ǳ�ڸ�������
 * 4. �ڲ�������ɶ�� rss �ģ�ֱ����վ���������ˣ�
 */
if (!$isnormalboard) die;


if ($type == "g") {
	$ftype = $dir_modes["DIGEST"];
	$title = $desc = "��ժ��";
} else if ($type == "m") {
	$ftype = $dir_modes["MARK"];
	$title = $desc = "������";
	bbs_checkmark($board);
} else if ($type == "o") {
	$ftype = $dir_modes["ORIGIN"];
	$title = "";
	$desc = "����";
} else {
	die; //TODO?
}
$dotdirname = bbs_get_board_index($board, $ftype);
$modifytime = @filemtime($dotdirname);

if (cache_header("public",$modifytime,1800))
	return;

$channel = array();
$htmlboardDesc = BBS_FULL_NAME . " " . $board . "/" . htmlspecialchars($brdarr["DESC"], ENT_QUOTES) . " ";
$channel["title"] = $htmlboardDesc . $title;
$channel["description"] = $htmlboardDesc . " ����" . $desc . "����";

$channel["link"] = SiteURL."frames.html?mainurl=".urlencode("bbsdoc.php?board=".$board); /* TODO: ftype? */
$channel["language"] = "zh-cn";
$channel["generator"] = "KBS RSS Generator";
$channel["lastBuildDate"] = gmt_date_format($modifytime);

$total = bbs_countarticles($brdnum, $ftype);
$items = array();
if ($total > 0) {
	$artcnt = ARTICLE_PER_RSS;
	if ($total <= $artcnt) {
		$start = 1;
		$artcnt = $total;
	} else {
		$start = ($total - $artcnt + 1);
	}
	($articles = bbs_getarticles($board, $start, $artcnt, $ftype)) or die;
	$cc = count($articles);
	for ($i = $cc - 1; $i >= 0; $i--) {
		$origin = $articles[$i];
		$item = array();
		$item["title"] = htmlspecialchars($origin['TITLE'], ENT_QUOTES) . " ";
		$conurl = "bbscon.php?bid=".$brdnum."&amp;id=".$origin['ID']; 
		//you want the link to always work, so you don't want to add num
		//if ($ftype == $dir_modes["DIGEST"]) $conurl .= "&amp;ftype=".$ftype."&amp;num=".($start+$i);
		$item["link"] = SiteURL.$conurl;
		$item["author"] = $origin['OWNER'];
		$item["pubDate"] = gmt_date_format($origin['POSTTIME']);
		$item["guid"] = $item["link"]; //TODO
		//$item["comments"] = ?? //TODO
		
		$filename = bbs_get_board_filename($board, $origin["FILENAME"]);
		$contents = bbs2_readfile_text($filename, DESC_CHAR_PER_RSS_ITEM, 0);
		if (is_string($contents)) {
			$item["description"] = "<![CDATA[" . $contents . "]]>";
		}
		$items[] = $item;
	}
}

output_rss($channel, $items);
?>
