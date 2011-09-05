<?php

require("bbs0anbm_pre.php");

if($has_perm_boards < 1)
	html_error_quit("��û��Ȩ�޲�����Ŀ¼��");

$p = strrpos($filename, "/");
$realpath = substr($filename, 0, $p);
$oldfname = substr($filename, $p + 1, strlen($filename) - $p - 1);
$redirectpath = rawurlencode(substr($realpath, 9, strlen($realpath) - 9));
$text = "";
if(isset($_POST["filename"]))
{
	$newfname = $_POST["filename"];
	$newtitle = $_POST["title"];
	$newcontent = $_POST["content"];
	$ret = bbs_ann_editfile($realpath, $oldfname, $newfname, $newtitle, $newcontent);
	switch($ret)
	{
		case 0:
			header("Location: bbs0anbm.php?path=" . $redirectpath);
			exit;
		case -1:
			html_error_quit("������Ŀ¼�����ڡ�");
			break;
		case -2:
			$text = "�����ļ��������Ƿ��ַ���";
			break;
		case -3:
			$text = "����ͬ��Ŀ¼���ļ��Ѿ����ڡ�";
			break;
		case -4:
			html_error_quit("ϵͳ���󣬴򲻿��ļ���");
			break;
		case -5:
			html_error_quit("ϵͳ���󣬾��������������⡣");
			break;
		case -6:
			html_error_quit("����ʧ�ܣ������������������ڴ���ͬһĿ¼��");
			break;
	}
}
else
{	
	if(isset($_GET["title"]))
		$newtitle = $_GET["title"];
	else
		html_error_quit("��������");
	$newcontent = bbs_ann_originfile($filename);
	if($newcontent == -1)
		html_error_quit("�������ļ������ڡ�");
	$newfname = $oldfname;
	
}
	
page_header("�༭�ļ�", "����������");

?>
<form action="bbs0anbm_editfile.php?path=<?php echo rawurlencode($path); ?>" method="post" class="large">
	<fieldset><legend>�༭�������ļ�</legend>
		<div class="inputs">
			<div style="color:#FF0000"><?php echo $text; ?></div>
			<label>�ļ�����</label><input type="text" maxlength="38" size="15" name="filename" value="<?php echo htmlspecialchars($newfname); ?>"><br>
			<label>�ꡡ�⣺</label><input type="text" maxlength="38" size="38" name="title" value="<?php echo trim(htmlspecialchars($newtitle)); ?> "><br>
			<textarea name="content"><?php echo htmlspecialchars($newcontent); ?></textarea>
		</div>
	</fieldset>
	<div class="oper"><input type="submit" value="����"> [<a href="bbs0anbm.php?path=<?php echo $redirectpath; ?>">���ؾ�����Ŀ¼</a>]</div>
</form>

<?php

page_footer();
	
?>
