<?php
	require("www2-funcs.php");
	login_init();
	assert_login();

	if(isset($_GET["board"]))
		$board = $_GET["board"];
	else
		html_error_quit("����Ĳ���");
	$brdarr = array();
	$bid = bbs_getboard($board, $brdarr);
	if($bid == 0)
		html_error_quit("�����������");
	$board = $brdarr["NAME"];
	$brd_encode = urlencode( $board );
	if(isset($_GET["gid"]))
		$gid = $_GET["gid"];
	else
		html_error_quit("����Ĳ���");
	if(isset($_GET["start"]))
		$start = $_GET["start"];
	else
		html_error_quit("����Ĳ���");
	
	if(isset($_POST["operate"]))
	{
		$operate = $_POST["operate"];
		$ret = bbs_threads_bmfunc($bid, $gid, $start, $operate);
		if($ret >= 0)
		{
			if($operate == 5)
			{
				$ann_path = bbs_getannpath($board);
				if ($ann_path != FALSE)	{
					if (!strncmp($ann_path,"0Announce/",10))
						$ann_path = substr($ann_path,9);
					// ˿·���������� �Ȳ�Ҫ��
		    		//header("Location: bbsipath.php?annpath={$ann_path}");
		    		header("Location: bbs0anbm.php?path={$ann_path}");
				}
			}
			else
				html_success_quit("�����ɹ��� {$ret} ����¼���޸ġ�");
		}
		else if($ret == -1)
			html_error_quit("�������������");
		else if($ret == -2)
			html_error_quit("û��Ȩ��");
		else
			html_error_quit("ϵͳ�ڲ�����");
	}
	
	bbs_board_nav_header($brdarr, "ͬ�������");
?>
<form action="bbsthreads.php?board=<?php echo $board; ?>&gid=<?php echo $gid; ?>&start=<?php echo $start; ?>" method="post" class="medium">
	<fieldset>
		<legend>ͬ�������</legend>
		<div class="inputs">��ѡ��Ҫִ�еĲ�����<br><blockquote>
			<input type="radio" name="operate" value="1">ɾ��<br>
			<input type="radio" name="operate" value="2">M���<br>
			<input type="radio" name="operate" value="3">ȡ��M���<br>
			<input type="radio" name="operate" value="4">ɾ����ɾ����<br>
			<input type="radio" name="operate" value="5">��¼������<br>
			<input type="radio" name="operate" value="12">�����ݴ浵<br>
			<input type="radio" name="operate" value="13">�����ݴ浵��ȥ�����ģ�<br>
			<input type="radio" name="operate" value="6">�趨��ɾ���<br>
			<input type="radio" name="operate" value="7">ȡ����ɾ���<br>
			<input type="radio" name="operate" value="8">�趨���ɻظ�<br>
			<input type="radio" name="operate" value="9">ȡ�����ɻظ�<br>
			<input type="radio" name="operate" value="10">���ϼ����������ģ�<br>
			<input type="radio" name="operate" value="11">���ϼ���ȥ�����ģ�<br>
		</blockquote>
			<div align="center"><input type="submit" value="ȷ��"><br></div>
		</div>
	</fieldset>
</form><br>
<?php
	page_footer();
?>
