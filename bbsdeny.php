<?php
$denyreasons = array(
	'��ˮ',
	'���Ű�����������',
	'�������',
	'������',
	'�����޹ػ���',
	'����ǡ������',
	'test(�����öԷ�ͬ��󹩲��Է��ʹ��)'
);
 
require("www2-funcs.php");
require("www2-board.php");
login_init();
bbs_session_modify_user_mode(BBS_MODE_READING);
if (isset($_GET["board"]))
	$board = $_GET["board"];
else
	html_error_quit("�����������");

$brdarr = array();
$bid = bbs_getboard($board, $brdarr);
if ($bid == 0)
	html_error_quit("�����������");
$usernum = $currentuser["index"];
if (!bbs_is_bm($bid, $usernum))
	html_error_quit("�㲻�ǰ���");
$board = $brdarr['NAME'];
$brd_encode = urlencode($board);
$maxreason = bbs_getdenyreason($board, $denyreasons, 1);

bbs_board_nav_header($brdarr, "�������");

if (isset($_GET['act'])) {
	switch ($_GET['act']) {
		case 'del':
			$userid = ltrim(trim($_GET['userid']));
			if (!$userid)
				html_error_quit("���������û���ID");
			switch (bbs_denydel($board,$userid)) {
				case -1:
				case -2:
					html_error_quit("����������");
					break;
				case -3:
					html_error_quit($userid." ���ڷ���б���");
					break;
				default:
			}
			break;
		case 'add':
			$userid = ltrim(trim($_POST['userid']));
			$denyday = intval($_POST['denyday']);
			$exp = (trim($_POST['exp2']))?trim($_POST['exp2']):$denyreasons[intval($_POST['exp'])]['desc'];
			if (!$userid || !$denyday || !$exp)
				break;
			if (!strcasecmp($userid,'guest') || !strcasecmp($userid,'SYSOP'))
				html_error_quit("���ܷ�� ".$userid);
			switch (bbs_denyadd($board,$userid,$exp,$denyday,0)) {
				case -1:
				case -2:
					html_error_quit("����������");
					break;
				case -3:
					html_error_quit("����ȷ��ʹ����ID");
					break;
				case -4:
					html_error_quit("�û� ".$userid." ���ڷ���б���");
					break;
				case -5:
					html_error_quit("���ʱ�����");
					break;
				case -6:
					html_error_quit("������������");
					break;
				case -7:
					html_error_quit($userid."û���ڱ���ķ���Ȩ��, ���ܷ��");
					break;
				default:
			}
			break;
		case 'mod':
			$userid = ltrim(trim($_POST['userid']));
			$denyday = intval($_POST['denyday']);
			$exp = (trim($_POST['exp2']))?trim($_POST['exp2']):$denyreasons[intval($_POST['exp'])]['desc'];
			if (!$userid || !$denyday || !$exp)
				break;
			if (!strcasecmp($userid,'guest') || !strcasecmp($userid,'SYSOP'))
				html_error_quit("���ܷ�� ".$userid);
			switch (bbs_denymod($board,$userid,$exp,$denyday,0)) {
				case -1:
				case -2:
					html_error_quit("����������");
					break;
				case -3:
					html_error_quit("����ȷ��ʹ����ID");
					break;
				case -4:
					html_error_quit("�û� ".$userid." ���ڷ���б���");
					break;
				case -5:
					html_error_quit("���ʱ�����");
					break;
				case -6:
					html_error_quit("������������");
					break;
				case -7:
					html_error_quit($userid."û���ڱ���ķ���Ȩ��, ���ܷ��");
					break;
				default:
			}
			break;
		default:
	}
}

$denyusers = array();
$ret = bbs_denyusers($board,$denyusers);
switch ($ret) {
	case -1:
		html_error_quit("ϵͳ��������ϵ����Ա");
		break;
	case -2:
		html_error_quit("�����������");
		break;
	case -3:
		html_error_quit("������Ȩ��");
		break;
	default:    
}

$maxdenydays = ($currentuser["userlevel"]&BBS_PERM_SYSOP)?70:14;
?>
<table class="main wide adj">
<caption>�������</caption>
<col class="center" width="60"/><col class="center" width="100"/><col width="*"/><col class="center" width="150"/><col class="center" width="60"/>
<tbody><tr><th>���</th><th>�û���</th><th>����</th><th>˵��</th><th>�޸�</th><th>���</th></tr>
<?php
	$i = 1;
	foreach ($denyusers as $user) {
		echo '<tr><td>'.$i.'</td><td><a href="bbsqry.php?userid='.$user['ID'].'">'.$user['ID'].'</a></td>'.
			 '<td>'.htmlspecialchars($user['EXP']).' </td>'.
			 '<td>'.htmlspecialchars($user['COMMENT']).'</td>'.
			 '<td><a href="bbsmodifydeny.php?board='.$brd_encode."&user=".htmlspecialchars($user['ID']).'">�޸�</a></td>'.
			 '<td><a onclick="return confirm(\'ȷʵ�����?\')" href="'.$_SERVER['PHP_SELF'].'?board='.$brd_encode.'&act=del&userid='.$user['ID'].'">���</a></td>'.
			 '</tr>';
		$i ++ ;
	}
?>
</tbody></table>
<form action="<?php $_SERVER['PHP_SELF']; ?>?act=add&board=<?php echo $brd_encode; ?>" method="post" class="medium">
	<fieldset><legend>��ӷ���û�</legend>
		<div class="inputs">
			<label>�û���</label><input type="text" name="userid" size="12" maxlength="12" /><br/>
			<label>���ʱ��</label><select name="denyday">
<?php
	$i = 1;
	while ($i <= $maxdenydays) {
		echo '<option value="'.$i.'">'.$i.'</option>';
		$i += ($i >= 14)?7:1;
	}    
?>    
			</select>��<br/>
			<label>���ԭ��</label><select name="exp">
<?php
	$i = 0;
	foreach ($denyreasons as $reason) {
		echo '<option value="'.$i.'">'.htmlspecialchars($reason['desc']).'</option>';
		$i ++;
	}
?>    
			</select>&nbsp;&nbsp;<a href="bbsdenyreason.php?board=<?php echo $board; ?>">�Զ��������</a><br />
			���ֶ����������ɣ�
			<input type="text" name="exp2" size="20" maxlength="28" />
		</div>
		<div class="oper"><input type="submit" value="��ӷ��" /></div>
	</fieldset>
</form>
<?php
page_footer(FALSE);
?>
