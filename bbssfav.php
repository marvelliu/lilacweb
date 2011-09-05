<?php
require_once ('www2-funcs.php');
require_once ('favorite.inc.php');
login_init();
bbs_session_modify_user_mode(BBS_MODE_MMENU);
page_header($currentuser["userid"] . " �İٱ���");
assert_login();

if (!($fav = new Favorite ($currentuser['userid']))) {
	html_error_quit ($fav);
}

if (isset ($_GET['pid']))
	$pid = intval ($_GET['pid']);
else
	$pid = 0;

if (!$fav->load ($pid)) {
	html_error_quit ($fav->err);
}

$form = 0;
if (isset ($_GET['act'])) {
	switch ($_GET['act']) {
		case 'add':
			if (!$fav->add ($fav->currentPid, @$_GET['type'], @$_GET['order'], @$_GET['title'], @$_GET['url']))
				html_error_quit ($fav->err);
			break;
		case 'del':
			if (!$fav->del (@$_GET['id']))
				html_error_quit ($fav->err);
			break;
		case 'edit':
			if (!$fav->id_exist (@$_GET['id']))
				html_error_quit ('��������');
			$editArr = $fav->allFav[$_GET['id']];
			$form = 1;
			break;
		case 'edit2':
			if (!$fav->move (@$_GET['id'], @$_GET['order']))
				html_error_quit ($fav->err);
			break;
		case 'choose':
			$fav->load_all_dirs ();
			$form = 2;
			break;
		default:
	}
	$fav->load ($pid);
}

display_super_fav ($fav);
switch ($form) {
	case 1:
		display_edit_form ($fav, $editArr);
		break;
	case 2:
		display_add_form ($fav, true);
		break;    
	default:
		display_add_form ($fav, false);
}
unset ($fav);

page_footer();

function display_super_fav ($fav) {
	
?>
<table class="main adj">
<caption>�ҵİٱ���</caption>
<col class="center"/><col class="center"/><col/><col class="center"/>
<tbody>
<tr><th>λ��</th><th>����</th><th style="width: 20em;">����</th><th>����</th></tr>
<?php
	if ($fav->currentPid != 0) {
?>
<tr>
<td> </td>
<td> <script type="text/javascript">putImage('groupgroup.gif','alt="up" title="�ص���Ŀ¼"');</script></td>
<td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId; ?>">�ص���Ŀ¼</a></td>
</tr>
<tr>
<td> </td>
<td> <script type="text/javascript">putImage('groupgroup.gif','alt="up" title="�ص���һ��"');</script></td>
<td colspan="2"><a href="<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId.'&pid='.$fav->info['PID']; ?>">�ص���һ��</a></td>
</tr>
<?php        
	}
	for ($i=0; $i < $fav->favCnt; $i ++) {
?>   
<tr>
<td><?php echo $fav->fav[$i]['ORDER']; ?></td>
<td> 
<?php
	if ($fav->is_dir ($fav->fav[$i]['ID'])) {
?>
<script type="text/javascript">putImage('groupgroup.gif','alt="Ŀ¼" title="Ŀ¼"');</script>
<?php
	} else {
?>
<script type="text/javascript">putImage('newgroup.gif','alt="��Ŀ" title="��Ŀ"');</script>
<?php
	}
?>
</td>
<?php
	if ($fav->is_dir ($fav->fav[$i]['ID'])) {
?>
<td><a href="<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId.'&pid='.($fav->fav[$i]['ID']); ?>"><?php echo htmlspecialchars ($fav->fav[$i]['TITLE']); ?></a></td>
<?php
	} else {
?>
<td><a href="<?php echo $fav->fav[$i]['URL']; ?>"><?php echo htmlspecialchars ($fav->fav[$i]['TITLE']); ?></a></td>
<?php
	}
?>
<td>
	<a href="<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId.'&pid='.$fav->currentPid.'&act=edit&id='.$fav->fav[$i]['ID']; ?>">�޸�</a>
	<a href="<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId.'&pid='.$fav->currentPid.'&act=del&id='.$fav->fav[$i]['ID']; ?>">ɾ��</a>
</td>
</tr>
<?php        
	}
?>
</tbody></table>
<?php
	
}

function display_edit_form ($fav, $editArr) {
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="small">
<input type="hidden" name="act" value="edit2" />
<input type="hidden" name="pid" value="<?php echo $fav->currentPid; ?>" />
<input type="hidden" name="id" value="<?php echo $editArr['ID']; ?>" />
<fieldset><legend>�༭��Ŀ</legend>
	<div class="inputs">
		<label>����:</label><?php echo htmlspecialchars ($editArr['TITLE']); ?><br/>
		<label>��λ��: </label><select name="order">
<?php
	for ($i=1; $i <= $fav->maxOrder; $i ++) {
		if ($editArr['ORDER']==$i)
			echo '<option value="'.$i.'" selected>'.$i.'</option>';
		else
			echo '<option value="'.$i.'">'.$i.'</option>';
	}
?>
</select></div></fieldset>
<div class="oper"><input type="submit" value="�޸�" />&nbsp;<input type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'].'?userid='.$fav->userId.'&pid='.$fav->currentPid; ?>';" value="���" /></div>
</form>
<?php
}

function display_add_form ($fav, $isChoose) { /* TODO: fold add form */
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="medium">
<input type="hidden" name="act" value="add" />
<input type="hidden" name="pid" value="<?php echo $fav->currentPid; ?>" />
<fieldset><legend>�����Ŀ</legend>
	<div class="inputs">
		<label>����:</label><input type="text" size="30" maxlength="100" name="title"  value="<?php echo @$_GET['title']; ?> " <?php if ($isChoose) echo 'id="sfocus"'; ?>/><br/>
		<label>����:</label><input type="radio" name="type" value="0" <?php if (@$_GET['type']!=1) echo 'checked'; ?> onclick="this.form.url.disabled=false;"/>��Ŀ&nbsp;&nbsp;<input type="radio" name="type" value="1" onclick="this.form.url.disabled=true;" <?php if (@$_GET['type']==1) echo 'checked'; ?>/>Ŀ¼<br/>
<?php
	if ($isChoose) {
?>
		<label>Ŀ¼:</label><select name="pid">
<option value="0" selected >��Ŀ¼</option>
<?php
		reset ($fav->allDirs);
		while (list ($id, $val)=each ($fav->allDirs))
			echo '<option value="'.$id.'">'.htmlspecialchars($val['TITLE']).'</option>';
	} else {
?>
		<label>λ��:</label><select name="order">
<?php
		for ($i=1; $i <= $fav->maxOrder; $i ++) {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
		echo '<option value="'.($fav->maxOrder + 1).'" selected>'.($fav->maxOrder + 1).'</option>';
	}
?>
</select><br/>
		<label>��ַ:</label><input type="text" size="30" maxlength="200" name="url" value="<?php echo @$_GET['url']; ?>"/>
	</div>
</fieldset>
<div class="oper"><input type="submit" value="���"></div>
</form>
<?php    
}
?>
