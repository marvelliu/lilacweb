<?php
	require("www2-funcs.php");
	login_init();
	$hostname=bbs_sysconf_str("MYSQLHOST");
	$username=bbs_sysconf_str("MYSQLUSER");
	$password=bbs_sysconf_str("MYSQLPASSWORD");
	$dbname=bbs_sysconf_str("MYSQLDATABASE");
	//���Ȩ��
	if($currentuser["userlevel"]&BBS_PERM_BMAMANGER)
		$hasperm=true;
	else
		$hasperm=false;

	//ת����������
	function meaningofWord($arg){
		for($i=0;$i<strlen($arg);$i++)
		{
				switch(ord($arg{$i}))
				{
			case 1: $meaning.="n.";break;
			case 2: $meaning.="v.";break;
			case 3: $meaning.="a.";break;
			case 4: $meaning.="ad.";break;
			case 5: $meaning.="vi.";break;
			case 6: $meaning.="vt.";break;
			case 8: $meaning.="vbl.";break;
			case 9: $meaning.="int.";break;
			case 10: $meaning.="art.";break;
			case 11: $meaning.="aux.";break;
			case 12: $meaning.="num.";break;
			case 13: $meaning.="conj.";break;
			case 14: $meaning.="prep.";break;
			case 15: $meaning.="pron.";break;
			case 16: $meaning.="st.";break;
			case 17: $meaning.="adj.";break;
			case 18: $meaning.="adv.";break;
			case 19: $meaning.="pr.";break;
			case 20: $meaning.="abbr.";break;
			case 21: break;
			case 22: break;
			case 25: break;//insound
			case 26: break;
			case 30: break;
			case 31: $i++; $i++; break;
			case 59: $meaning.="<br>"; break;
			default: $meaning.=$arg{$i}; 
				}
		}
		return $meaning;
	}
		$db=mysql_connect($hostname,$username,$password) or die(mysql_error());
		mysql_select_db($dbname,$db) or die(mysql_error());
		
		if(isset($_GET["add"]) && $hasperm)
		{
				$word=addslashes(trim($_GET["word"]));
				if(strlen($word)>0)
				{
				if(ord($word{0})>128){
						$tablename="cedict";
						$dictname=addslashes($_GET["cedictname"]);
				}else{
						$tablename="ecdict";    
						$dictname=addslashes($_GET["ecdictname"]);
				}
				//���ӵ���
				if($_GET["cixing"]>0)
				{
						$meaning=chr(addslashes($_GET["cixing"])).addslashes($_GET["meaning"]);
				}else
						$meaning=addslashes($_GET["meaning"]);
				
				//����ǲ����Ѿ������������
				$result=mysql_query("select * from ".$tablename." where word='".$word."' and dictid=$dictname") or die(mysql_error());
				if($result_rows=mysql_fetch_array($result))
				{
						$meaning=$result_rows["meaning"].chr(59).$meaning;
						$id=$result_rows["id"];
						$result=mysql_query("update ".$tablename." set meaning='$meaning' where id=$id");
				}else
				{
						$result=mysql_query("insert into ".$tablename." (word,meaning,dictid) values('$word','$meaning','$dictname')") or die(mysql_error());
				}
				//add a new word;
				}
		}
		if($_GET["delete"]){
			$id=addslashes($_GET["id"]);
			$mysql="delete from ".addslashes($_GET["tablename"])." where id=$id";
			$result=mysql_query($mysql) or die(mysql_error());
		}
		page_header("���ֵ�");
?>
<form action="<?php echo $_SERVER['PHP_SELF']?>" class="medium">
	<fieldset><legend>���ֵ�</legend>
		������:<input type="text" name="inputword" value="" size="24" maxlength="20" id="sfocus"/>
		<input type="submit" value="��ѯ" />
	</fieldset>
</form>
<div class="medium left">
<?php
//��ʾ��ѯ�Ľ��
//��ʾ�鵽�ĵ���
if(isset($_GET["inputword"]))
		{
				$inputword=addslashes(trim($_GET["inputword"]));
				
				if(ord($inputword{0})>128)
						$tablename="cedict";
				else
						$tablename="ecdict";
				$result=mysql_query("select * from ".$tablename." where word='".$inputword."'") or die(mysql_error());
						
				echo "�ǵ�:".$tablename."<br>";
				echo "��ѯ���<br>";
				echo trim($_GET["inputword"])."����˼<br>";
				if(mysql_num_rows($result)==0)
						echo "û�������<br>";
				while($result_rows=mysql_fetch_array($result))
				{                       
						echo MeaningofWord($result_rows["meaning"]);
						$dictname=$tablename."name";
						echo "<br />>>>>";
						if($result_rows[3]>0)
						{
						$dictid=$result_rows[3];
						$dict_result2=mysql_query("select * from ".$dictname." where id='$dictid'") or die(mysql_error());
						$myrows=mysql_fetch_row($dict_result2);
						echo "(�ʵ�:".$myrows[1].")";
						}
						else
								echo "Ĭ�ϴʵ�";
						
						if($hasperm)
								printf("<a href=\"%s?id=%d&tablename=%s&delete=yes\">(delete)</a><br>",$_SERVER['PHP_SELF'],$result_rows["id"],$tablename);
				
				}               
}
?>
</div>
<p>
<?php
if($hasperm)
{
?>
<form action="<?php echo $_SERVER['PHP_SELF']?>">
<input type=hidden name=add value="yes">
�����´ʻ��޸�:<input type=text name=word value="<?php echo $inputword;?>" size=24 maxlength=20>
����
<select name="cixing">
<option value=1>n.</option>
<option value=2>v.</option>
<option value=3>a.</option>
<option value=4>ad.</option>
<option value=5>vi.</option>
<option value=6>vt.</option>
<option value=8>vbl.</option>
<option value=9>int.</option>
<option value=10>art.</option>
<option value=11>aux.</option>
<option value=12>num.</option>
<option value=13>conj.</option>
<option value=14>prep.</option>
<option value=15>pron.</option>
<option value=16>st.</option>
<option value=17>adj.</option>
<option value=18>adv.</option>
<option value=19>pr.</option>
<option value=20>abbr.</option> 
<option value=0>����</option>
</select><br>
����<input type name=meaning value="" size=24>
<br>��Ӣ�ʵ�<select name="cedictname">
<option value=0>Ĭ��</option>
<?php
$result=mysql_query("select * from cedictname") or die(mysql_error());
while($myrows=mysql_fetch_array($result)){
		echo "<option value=$myrows[0]>$myrows[1]</option>";
}       
?>
</select>
Ӣ�дʵ�<select name="ecdictname">
<option value=0>default</option>
<?php
$result=mysql_query("select * from ecdictname") or die(mysql_error());
while($myrows=mysql_fetch_array($result)){
		echo "<option value=$myrows[0]>$myrows[1]</option>";
}       
?>
</select>
<input type="submit" value="����">
</form>
<?php
}//end of hasperm
page_footer();
?>
