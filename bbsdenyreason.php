<?php
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

bbs_board_nav_header($brdarr, "�Զ�����������");

if (isset($_GET['act'])) {
	switch ($_GET['act']) {
		case 'set':
			if (!isset($_POST['setreason']))
				html_error_quit("����Ĳ���");
			$setreason = ($_POST['setreason']);
			switch (bbs_setdenyreason($board, $setreason)) {
				case -1:
					html_error_quit("����������");
					break;
				default:
					html_success_quit("�Զ�����������ɱ���ɹ�<br/><br/>",
						array("<a href=bbsdoc.php?board=".$board.">���ذ���</a>",
							"<a href=\"javascript:history.go(-1)\">�����޸�</a>"));
			}
			break;
		default:
	}
}

$denyreasons = array();
$maxreason = bbs_getdenyreason($board, $denyreasons, 0);
?>
<script type="text/javascript">
function remove(r) {
	var table = document.getElementById("tableId");
	var tbody = table.tBodies[0];
	var len = tbody.rows.length;
	var item = r.parentNode.parentNode;
	var found = 0;
	if (!confirm("ȷ��ɾ����"))
		return;
	for (var i=1;i<len-1;i++) {
		if (found==0 && item==tbody.rows[i])
			found = 1;
		if (found==1) {
			tbody.rows[i].getElementsByTagName("span")[0].innerHTML = tbody.rows[i+1].getElementsByTagName("span")[0].innerHTML;
			tbody.rows[i].getElementsByTagName("span")[1].innerHTML = tbody.rows[i+1].getElementsByTagName("span")[1].innerHTML;
		}
	}
	tbody.removeChild(tbody.rows[len-1]);
}

function move(r, dir) {
	var table = document.getElementById("tableId");
	var tbody = table.tBodies[0];
	var len = tbody.rows.length;
	var item = r.parentNode.parentNode;
	for (var i=1;i<len;i++) {
		if (item==tbody.rows[i]) {
			break;
		}
	}
	var next;
	if (dir=='up')
		next = i-1;
	else if (dir=='down')
		next = i+1;
	else {		
		alert("��������");
		return;
	}
	if (next<=1) {
		alert("��ͷ��");
		return;
	}
	if (next>=len) {
		alert("������");
		return;
	}
	var tmp = tbody.rows[i].getElementsByTagName("span")[0].innerHTML;
	tbody.rows[i].getElementsByTagName("span")[0].innerHTML = tbody.rows[next].getElementsByTagName("span")[0].innerHTML;
	tbody.rows[next].getElementsByTagName("span")[0].innerHTML = tmp;
	tmp = tbody.rows[i].getElementsByTagName("span")[1].innerHTML;
	tbody.rows[i].getElementsByTagName("span")[1].innerHTML = tbody.rows[next].getElementsByTagName("span")[1].innerHTML;
	tbody.rows[next].getElementsByTagName("span")[1].innerHTML = tmp;
}

function edit(r) {
	var item = r.parentNode.parentNode;
	if (item.getElementsByTagName('input').length) {
		item.getElementsByTagName('span')[0].innerHTML = item.getElementsByTagName('span')[1].innerHTML;
		return;
	}
	var oldValue = item.getElementsByTagName('span')[0].innerHTML;
	var str = '';
	str += '<input type="text" maxLength=13 size=40 value="' + oldValue + '">';
	str += '<input type="button" value="ȷ��" onclick="edit_do(this, 1);">';
	str += '<input type="button" value="ȡ��" onclick="edit_do(this, 0);">';
	item.getElementsByTagName('span')[0].innerHTML = str;
}

function edit_do(r, t) {
	var item = r.parentNode.parentNode;
	var table = document.getElementById("tableId");
	var tbody = table.tBodies[0];
	var len = tbody.rows.length;
	
	if (t) {
		var str = item.getElementsByTagName('input')[0].value;
		if (str.length<=0) {
			alert("̫����");
			return;
		}
		for (var i=1;i<len;i++) {
			if (str==tbody.rows[i].getElementsByTagName('span')[1].innerHTML
				&& item.parentNode.getElementsByTagName('td')[0].innerHTML!=tbody.rows[i].getElementsByTagName("td")[0].innerHTML) {
				alert("�ظ���");
				return;
			}
		}	  
	} else
		var str = item.getElementsByTagName('span')[1].innerHTML;
	item.getElementsByTagName('span')[0].innerHTML = str;
	item.getElementsByTagName('span')[1].innerHTML = str;
}

function add() {
	var table = document.getElementById("tableId");
	var tbody = table.tBodies[0];
	var len = tbody.rows.length;
	var newValue = document.getElementById("newValue").value;
	if (newValue.length<=0) {
		alert("̫����");
		return;
	}
	if (len><?php echo BBS_BOARD_MAXCUSTOMREASON; ?>+1) {
		alert("�Ѿ��ﵽ������ˣ�");
		return;
	}
	for (var i=1;i<len;i++) {
		if (newValue==tbody.rows[i].getElementsByTagName("span")[0].innerHTML) {
			alert("�ظ���");
			return;
		}
	}
	var newRow = tbody.rows[1].cloneNode(true);
	newRow.getElementsByTagName("td")[0].innerHTML = len-1;
	newRow.getElementsByTagName("span")[0].innerHTML = newValue;
	newRow.getElementsByTagName("span")[1].innerHTML = newValue;

	tbody.appendChild(newRow);
	tbody.rows[len].style.display = "";
	document.getElementById("newValue").value = "";
}

function confirm_set() {
	var table = document.getElementById("tableId");
	var tbody = table.tBodies[0];
	var len = tbody.rows.length;
	var str = "";

	for (var i=1;i<len;i++) {
		var curstr = tbody.rows[i].getElementsByTagName("span")[1].innerHTML;
		if (curstr == '')
			continue;
		str += curstr + "\n";
	}
	document.getElementById("setreason").value = str;
}
</script>
<table class="main wide adj" id="tableId" width="400">
<caption>����������</caption>
<col class="center" width="40"/><col width="*"/><col class="center" width="50"/><col class="center" width="50"/><col class="center" width="50"/><col class="center" width="50"/>
<tbody><tr><th>���</th><th>����</th><th>�޸�</th><th>����</th><th>����</th><th>ɾ��</th></tr>
<tr style="height:30;display:none;"><td></td>
	<td><span></span>
	<span style="display:none;"></span></td>
	<td><img onclick="edit(this)" style="cursor:pointer;" src="images/edit.png" alt="�޸�" height="16"/></td>
	<td><img onclick="move(this, 'up')" style="cursor:pointer;" src="images/up.png" alt="����" height="16"/></td>
	<td><img onclick="move(this, 'down')" style="cursor:pointer;" src="images/down.png" alt="����" height="16"/></td>
	<td><img onclick="remove(this)" style="cursor:pointer;" src="images/del.png" alt="ɾ��" height="16"/></td>
</tr>
<?php
	$i = 1;
	foreach ($denyreasons as $reason) {
		echo '<tr style="height:30;display:true;"><td>'.$i.'</td>'.
			 '<td><span>'.htmlspecialchars($reason['desc']).'</span>'.
			 '<span style="display:none;">'.htmlspecialchars($reason['desc']).'</span></td>'.
			 '<td><img onclick="edit(this)" style="cursor:pointer;" src="images/edit.png" alt="�޸�" height="16"/></td>'.
			 '<td><img onclick="move(this, \'up\')" style="cursor:pointer;" src="images/up.png" alt="����" height="16"/></td>'.
			 '<td><img onclick="move(this, \'down\')" style="cursor:pointer;" src="images/down.png" alt="����" height="16"/></td>'.
			 '<td><img onclick="remove(this)" style="cursor:pointer;" src="images/del.png" alt="ɾ��" height="16"/></td>'.
			 '</tr>';
		$i ++ ;
	}
?>
</tbody></table>
	<br/>
	<legend>��ӷ������</legend>
		<div class="inputs">
			<label>����: </label><input type="text" id="newValue" size="40" maxlength="13" /><input type="button" value="���" onclick="add()"><br/>
		</div>
	<br/>
<form action="<?php $_SERVER['PHP_SELF']; ?>?act=set&board=<?php echo $brd_encode; ?>" method="post" class="medium">
	<input type="hidden" name="setreason" id="setreason" value="" />
	<div align="center"><input type="button" value="ȡ���޸�" onclick="location.reload();"><input type="submit" value="ȷ���޸�" onclick="confirm_set();"></div>
	<br/>
</form>
<?php
page_footer(FALSE);
?>
