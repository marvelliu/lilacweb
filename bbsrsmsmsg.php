<?php
	require("www2-funcs.php");
	login_init();
	assert_login();

	$hostname=bbs_sysconf_str("MYSQLHOST");
	$username=bbs_sysconf_str("MYSQLUSER");
	$password=bbs_sysconf_str("MYSQLPASSWORD");
	$dbname=bbs_sysconf_str("MYSQLSMSDATABASE");

		if( $_GET["start"] ){
			$startnum = $_GET["start"];
		}else if( $_POST["start"] ){
			$startnum = $_POST["start"];
		}else
			$startnum = 0;
		settype($startnum,"integer");

		if( $_GET["count"] ){
			$count = $_GET["count"];
		}else if( $_POST["count"] ){
			$count = $_POST["count"];
		}else
			$count = 20;
		settype($count,"integer");

		if( $_GET["desc"] ){
			$desc = $_GET["desc"];
		}else if( $_POST["desc"] ){
			$desc = $_POST["desc"];
		}else
			$desc = 0;
		settype($desc, "integer");
		if($desc == 1)
			$order="DESC";
		else
			$order="ASC";

		$db = mysql_connect($hostname, $username, $password) or die(mysql_error());
		mysql_select_db($dbname, $db) or die(mysql_error());

		if( $_GET["action"] ){
			$action = $_GET["action"];
		}

		if(isset($action) && $action == "del"){
			if( $_GET["id"] ){
				$id = $_GET["id"];
			}
			else{
				html_error_quit("��������1");
			}
			settype($id, "integer");
			if($id < 0)
				html_error_quit("��������2");

			$sqlstr = "DELETE FROM smsmsg WHERE userid=\"".$currentuser["userid"]."\" AND id=".$id;
			$result = mysql_query($sqlstr) or die(mysql_error());

		}

		$sqlstr = "SELECT * FROM smsmsg WHERE userid=\"".$currentuser["userid"]."\" ORDER BY readed, timestamp ".$order." LIMIT ".$startnum.",".$count;

		$result = mysql_query($sqlstr) or die(mysql_error());

		if( ! $result ){
			html_error_quit("û������");
		}
		page_header("���Ź���");
?>
<a href="javascript:location.reload()">ˢ��</a>
<hr class=default>
<table border="1" width="613" align="center" cellpadding="0" cellspacing="0">
<tr><td>���</td><td>����</td><td>���</td><td>ʱ��</td><td>����</td><td></td></tr>
<?php
			$i=0;
			while($row = mysql_fetch_row($result)){
?>
<tr>
<td><?php echo $startnum+$i+1;?></td>
<td><?php echo $row[2];?></td>
<td><?php if( $row[4]==1 ) echo "��"; else echo "��";?></td>
<td><?php echo $row[3];?></td>
<td><pre><?php echo $row[5];?></pre></td>
<td><a onclick="return confirm('�����Ҫɾ��������¼��?')" href="bbsrsmsmsg.php?start=<?php if($i==0 && $startnum > 0) echo ($startnum-1); else echo $startnum;?>&count=<?php echo $count;?>&action=del&id=<?php echo $row[0];?>&desc=<?php echo $desc;?>">ɾ��</a></td>
</tr>
<?php
				$i++;
			}
			mysql_free_result($result);
?>
</table>
<center>
<hr class=default>
<a href="javascript:location.reload()">ˢ��</a>
<?php
		if( $startnum > 0 ){
?>
<a href="bbsrsmsmsg.php?start=<?php if($startnum - $count > 0) echo ($startnum-$count); else echo "0";?>&count=<?php echo $count;?>&desc=<?php echo $desc;?>">��һҳ</a>
<?php	}else{
?>
��һҳ
<?php
		}

		if( $i >= $count ){
?>
<a href="bbsrsmsmsg.php?start=<?php echo ($startnum+$count-1);?>&count=<?php echo $count;?>&desc=<?php echo $desc;?>">��һҳ</a>
<?php	}else{
?>
��һҳ
<?php
		}
?>

<script type="text/javascript">
<!--//
function doRefresh(){
	var oSelectType=document.getElementById("oType");
	var type=oSelectType.value;

	if(type=="1")
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=<?php echo $count;?>&desc=0";
	else
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=<?php echo $count;?>&desc=1";

	return;
}
//-->
</script>

<select name="type" class="input"  style="WIDTH: 60px" id="oType" onChange="doRefresh();">
<option value="1"<?php if( $desc==0 ) { ?> selected="selected"<?php } ?>>����</option>
<option value="2"<?php if( $desc==1 ) { ?> selected="selected"<?php } ?>>����</option>
</select>

<script type="text/javascript">
<!--//
function doCount(){
	var oSelectType=document.getElementById("oCount");
	var type=oSelectType.value;

	if(type=="10")
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=10&desc=<?php echo $desc;?>";
	else if(type == "50")
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=50&desc=<?php echo $desc;?>";
	else if(type == "30")
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=30&desc=<?php echo $desc;?>";
	else
		window.location="bbsrsmsmsg.php?start=<?php echo ($startnum);?>&count=20&desc=<?php echo $desc;?>";

	return;
}
//-->
</script>

ÿҳ��ʾ<select name="type" class="input"  style="WIDTH: 40px" id="oCount" onChange="doCount();">
<option value="10"<?php if( $count==10 ) { ?> selected="selected"<?php } ?>>10</option>
<option value="20"<?php if( $count==20 ) { ?> selected="selected"<?php } ?>>20</option>
<option value="30"<?php if( $count==30 ) { ?> selected="selected"<?php } ?>>30</option>
<option value="50"<?php if( $count==50 ) { ?> selected="selected"<?php } ?>>50</option>
</select>��

</center>
<?php
	page_footer();
?>
