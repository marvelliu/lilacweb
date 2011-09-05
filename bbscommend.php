<?php
require('www2-funcs.php');
login_init();
bbs_session_modify_user_mode(BBS_MODE_READING);
assert_login();

if (isset($_GET['board']))
	$board = $_GET['board'];
else
	html_error_quit('�����������');

$brdarr = array();
$bid = bbs_getboard($board,$brdarr);
if (!$bid)
	html_error_quit('�����������');
$board = $brdarr['NAME'];
$brd_encode = urlencode($board);

if (isset($_GET['id']))
	$id = intval($_GET['id']);
else
	html_error_quit('���������ID');


if (!bbs_normalboard($board))
	if (bbs_checkreadperm($currentuser["index"], $bid) == 0)
		html_error_quit("�����������");

$ftype = $dir_modes["NORMAL"];
$articles = array ();
$num = bbs_get_records_from_id($board, $id, $ftype, $articles);
if ($num == 0)
	html_error_quit("��������º�,ԭ�Ŀ����Ѿ���ɾ��");
$id = $articles[1]["ID"];
bbs_board_nav_header($brdarr, "�����Ƽ�");
$confirmed = isset($_GET['do']) ? 1 : 0;
	switch (bbs_docommend($board,$id,$confirmed)) {
		case 0:
			if ($confirmed) {
				html_success_quit("�Ƽ��ɹ���",
				array("<a href='bbsdoc.php?board=" . $brd_encode . "'>���� " . $brdarr['DESC'] . "</a>",
				"<a href='bbscon.php?bid=" . $bid . "&id=" . $id . "'>���ء�" . htmlspecialchars($articles[1]["TITLE"]) . "��</a>"));
			} else {
				// show recommend form below
			}
			break;
		case -1:
			html_error_quit("�Բ�����û����ƪ���µ��Ƽ�Ȩ��");
			break;
		case -2:
			html_error_quit("����������");
			break;
		case -3:
			html_error_quit("��������º�,ԭ�Ŀ����Ѿ���ɾ��");
			break;
		case -4:
			html_error_quit("�������Ѿ��Ƽ�������л���������Ƽ�");
			break;
		case -5:
			html_error_quit("�Բ��������Ƽ��ڲ���������");
			break;
		case -6:
			html_error_quit("�Բ�������ֹͣ���Ƽ���Ȩ��");
			break;
		case -7:
			html_error_quit("�Ƽ�ϵͳ����");
			break;
		default:
			html_error_quit("ϵͳ��������ϵ����Ա");
	}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?do&board=<?php echo $brd_encode; ?>&id=<?php echo $id; ?>" method="post" class="medium"/>
	<fieldset>
		<legend>�Ƽ����£�<?php echo $articles[1]["OWNER"]; ?> �� <a href="bbscon.php?bid=<?php echo $bid; ?>&id=<?php echo $id; ?>"><?php echo htmlspecialchars($articles[1]["TITLE"]); ?></a></legend>
		<div class="oper">
			<input type="submit" value="ȷ��" />&nbsp;&nbsp;<input type="button" value="ȡ��" onclick="history.go(-1)" />
		</div>
	</fieldset>
</form>
<?php
page_footer();
?>
