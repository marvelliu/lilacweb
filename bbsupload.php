<?php
	require("www2-funcs.php");
	require("www2-bmp.php");
	$sessionid = login_init(TRUE);
	assert_login();
	
	@$action=$_GET["act"];
	$msg = "";
	if ($action=="delete") {
		@$act_attachname=$_GET["attachname"];
		settype($act_attachname, "string");
		$filename = base64_decode($act_attachname);
		$ret = bbs_upload_del_file($filename);
		if ($ret) $msg = bbs_error_get_desc($ret);
		else $msg = "ɾ�� " . $filename . " �ɹ�";
	} else if ($action=="add") {
		$counter = @intval($_POST["counter"]);
		for($i = 0; $i < $counter; $i++) {
			if (!isset($_FILES['attachfile' . $i])) {
				continue;
			}
			$attpost = $_FILES['attachfile' . $i];
			@$errno = $attpost['error'];
			switch ($errno) {
			case UPLOAD_ERR_OK:
				$ofile = $attpost['tmp_name'];
				if (!file_exists($ofile)) {
					$msg .= "�ļ��������";
					break 2;
				}
				$oname = $attpost['name'];
				$htmlname = htmlspecialchars(my_basename($oname));
				if (!is_uploaded_file($ofile)) {
					die;
				}
				if (compress_bmp($ofile, $oname)) {
					$msg .= "���� BMP ͼƬ " . $htmlname . " ���Զ�ת���� PNG ��ʽ��<br/>";
				}
				$ret = bbs_upload_add_file($ofile, $oname);
				if ($ret) $msg .= bbs_error_get_desc($ret);
				else {
					$msg .= $htmlname . "�ϴ��ɹ���<br/>";
					continue 2;
				}
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$msg .= "�ļ��������� " . sizestring(BBS_MAXATTACHMENTSIZE) . " �ֽ�";
				break;
			case UPLOAD_ERR_PARTIAL:
				$msg .= "�ļ��������";
				break;
			case UPLOAD_ERR_NO_FILE:
				continue 2;
				$msg .= "û���ļ��ϴ���";
				break;
			default:
				$msg .= "δ֪����";
			}
			break;
		}
	}
	$attachments = bbs_upload_read_fileinfo();
	$filecount = count($attachments);
	$allnames = array();$totalsize=0;
	for($i=0;$i<$filecount;$i++) {
		$allnames[] = $attachments[$i]["name"];
		$totalsize += $attachments[$i]["size"];
	}
	$allnames=implode(",",$allnames);
	page_header("�ϴ�����", FALSE);
?>
<body>
<script type="text/javascript">
<!--
function checkAnyFiles() {
	var frm = document.forms[0];
	var i;
	for (i=0; i<fileCounter; i++) {
		var o = frm.elements["attachfile"+i];
		if (o.value) return true;
	}
	return false;
}
function addsubmit() {
	if (!checkAnyFiles()){
		alert('����ûѡ���ϴ��ĸ���');
		return false;
	} else {
		var frm = document.forms[0];
		var e2="bbsupload.php?act=add";
		getObj("winclose").style.display = "none";
		frm.counter.value = fileCounter;
		frm.action=e2;
		frm.paste.value='�����ϴ��У����Ժ�...';
		frm.paste.disabled=true;
		frm.submit();
		return true;
	}
}

function deletesubmit(f) {
	var e2="bbsupload.php?act=delete&attachname="+f;
	document.forms[1].action=e2;
	document.forms[1].submit();
}

function clickclose() {
	if (!checkAnyFiles()) return window.close();
	else if (confirm("����д���ļ�������û���ϴ����Ƿ�ȷ�Ϲرգ�")==true) return window.close();
	return false;
}

var fileCounter = 0, fileRemains = <?php echo (BBS_MAXATTACHMENTCOUNT - $filecount); ?>;
function moreAttach() {
	var ll = getObj("idAddAtt");
	var n = document.createElement("br");
	getObj("uploads").insertBefore(n, ll);
	if (gIE) {
		n = document.createElement("<input name='attachfile" + fileCounter + "'/>");
	} else {
		n = document.createElement("input");
		n.setAttribute('name', 'attachfile' + fileCounter);
	}
	n.setAttribute('type', 'file');
	n.setAttribute('size', 30);
	getObj("uploads").insertBefore(n, ll);
	fileCounter++;
	if (fileCounter >= fileRemains) ll.style.display = "none";
}
function allAttach() {
	while(fileCounter < fileRemains) moreAttach();
}

addBootFn(function() {
	if (fileRemains > 0) {
		getObj("idAddAtt").style.display = "inline";
		getObj("idAllAtt").style.display = "inline";
		moreAttach();
	}
	if (opener) {
		try {
			opener.document.forms["postform"].elements["attachname"].value = "<?php echo $allnames; ?>";
		} catch(e) {}
	} else {
		getObj("winclose").style.display = "none";
	}
});

//-->
</script>
<div style="width: 550px; margin: 1em auto;">
<?php if ($msg) echo "<font color='red'> ��ʾ��".$msg."</font>"; ?>
<form name="addattach" method="post" ENCTYPE="multipart/form-data" class="left" action="">
<input type="hidden" name="counter" vaue="0" />
<?php if ($sessionid) echo "<input type='hidden' name='sid' value='$sessionid' />"; ?>
ѡ����Ҫ�ϴ����ļ�����ϴ���(<a id="idAllAtt" style="display:none;" href="javascript:void(0);" onclick="allAttach();">��Ҫ���ö฽��</a>)
<div id="uploads">
<?php
	if ($filecount<BBS_MAXATTACHMENTCOUNT) {
?>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo(BBS_MAXATTACHMENTSIZE);?>" />
		<a id="idAddAtt" style="margin-left: 1em; display:none;" href="javascript:void(0);" onclick="moreAttach();">����һ������</a>
<?php
	} else {
?>
		��������������
		<input type="hidden" name="attachfile" />
<?php
	}
?>
</div>
<input type="button" value="�ϴ�" name="paste" onclick="addsubmit();" />
&nbsp;&nbsp;&nbsp;<input type="button" id="winclose" value="�ϴ����, �رմ���" onclick="return clickclose()" />
<p>�����ļ�������<?php echo sizestring($totalsize); ?> �ֽ�,
���ޣ�<?php echo sizestring(BBS_MAXATTACHMENTSIZE); ?> �ֽ�,
�����ϴ���<font color="#FF0000"><b><?php $rem = BBS_MAXATTACHMENTSIZE-$totalsize; 
	if ($rem < 0) $rem = 0; echo sizestring($rem); ?> �ֽ�</b></font>.</p>
</form>

<form name="deleteattach" ENCTYPE="multipart/form-data" method="post" class="left" action=""> 
<?php if ($sessionid) echo "<input type='hidden' name='sid' value='$sessionid' />"; ?>
<ol style="padding-left: 2em; margin-left: 0em;">�Ѿ��ϴ��ĸ����б�: (������ϴ� <?php echo BBS_MAXATTACHMENTCOUNT; ?>
 ��, �����ϴ� <font color="#FF0000"><b><?php echo (BBS_MAXATTACHMENTCOUNT-$filecount); ?></b></font> ��)
<?php
	for($i=0;$i<$filecount;$i++) {
		$f = $attachments[$i];
		echo "<li>".$f["name"]." (".sizestring($f["size"])."�ֽ�) <a href=\"javascript:deletesubmit('".base64_encode($f["name"])."');\">ɾ��</a></li>";
	}
?>
</ol>
</form>
</div>
</body>
</html>
