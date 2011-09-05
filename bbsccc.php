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
bbs_board_nav_header($brdarr, "����ת��");

if (isset($_GET['do'])) {
	$target = trim(ltrim($_POST['target']));
	if (!$target)
		html_error_quit("������ת���������");
	$outgo = isset($_POST['outgo'])?1:0;
	switch (bbs_docross($board,$id,$target,$outgo)) {
		case 0:
			html_success_quit("ת���ɹ���",
			array("<a href='bbsdoc.php?board=" . $target . "'>���� " . $target . " ������</a>",
			"<a href='bbsdoc.php?board=" . $brd_encode . "'>���� " . $brdarr['DESC'] . "</a>",
			"<a href='bbscon.php?bid=" . $bid . "&id=" . $id . "'>���ء�" . htmlspecialchars($articles[1]["TITLE"]) . "��</a>"));
			break;
		case -1:
			html_error_quit("����������");
			break;
		case -2:
			html_error_quit("������ ".$target. " ������");
			break;
		case -3:
			html_error_quit("����ת��ֻ��������");
			break;
		case -4:
			html_error_quit("������ ".$target." �������ķ���Ȩ��");
			break;
		case -5:
			html_error_quit("��������� ".$target." �������ķ���Ȩ��");
			break;
		case -6:
			html_error_quit("ת�����´���");
			break;
		case -7:
			html_error_quit("�����ѱ�ת�ع�һ��");
			break;
		case -8:
			html_error_quit("���ܽ�����ת�ص�����");
			break;
		case -9:
			html_error_quit($target." �����������ϴ�����");
			break;
		case -11:
			$prompt = "ת���ɹ���<br/><br/>���Ǻܱ�Ǹ�����Ŀ��ܺ��в������ݣ��辭��˷��ɷ���<br/><br/>" .
            	      "���ݡ��ʺŹ���취������ϵͳ���˵�������ͬ�������������ĵȴ�<br/>" .
                	  "վ����Ա����ˣ���Ҫ��γ��Է�������¡�<br/><br/>" .
            		  "�������ʣ������� SYSOP ��ѯ��";
			html_success_quit($prompt,
				array("<a href='bbsdoc.php?board=" . $target . "'>���� " . $target . " ������</a>",
				"<a href='bbsdoc.php?board=" . $brd_encode . "'>���� " . $brdarr['DESC'] . "</a>"));
			break;
		case -21:
			html_error_quit("���Ļ��ֲ����� ".$target." ���������趨, ��ʱ�޷���������...");
			break;
		default:
	}
	html_error_quit("ϵͳ��������ϵ����Ա");
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?do&board=<?php echo $brd_encode; ?>&id=<?php echo $id; ?>" method="post" class="medium"/>
	<fieldset>
		<legend>ת�����£�<a href="bbscon.php?bid=<?php echo $bid; ?>&id=<?php echo $id; ?>"><?php echo htmlspecialchars($articles[1]["TITLE"]); ?></a></legend>
		<div class="inputs">
			<label>������Ҫת���������:</label>
			<input type="text" name="target" size="18" maxlength="20" id="sfocus"/>
			<input type="checkbox" name="outgo" checked />ת��
		</div>
	</fieldset>
	<div class="oper"><input type="submit" value="ת��" /></div>
</form>
<?php
page_footer();
?>
