<?php
/*
** @id:windinsn dec 21, 2003
** System Vote
*/
////////////////////////////////////////////
// System Vote Configure Start
if(function_exists("bbs_sysconf_str"))
{
$sysVoteConfig["DBHOST"]=bbs_sysconf_str("MYSQLHOST");
$sysVoteConfig["DBUSER"]=bbs_sysconf_str("MYSQLUSER");
$sysVoteConfig["DBPASS"]=bbs_sysconf_str("MYSQLPASSWORD");
$sysVoteConfig["DBNAME"]=bbs_sysconf_str("MYSQLSMSDATABASE");
$sysVoteConfig["BOARD"]="SYSOP";
$sysVoteConfig["PAGESIZE"]=20;

// System Vote Configure End
///////////////////////////////////////////
$sysVoteConfig["BRDARR"] = array();
$sysVoteConfig["BRDNUM"] = bbs_getboard($sysVoteConfig["BOARD"], $sysVoteConfig["BRDARR"]);
}

function sysvote_db_connect()
{
	GLOBAL $sysVoteConfig;
	@$link = mysql_connect($sysVoteConfig["DBHOST"],$sysVoteConfig["DBUSER"],$sysVoteConfig["DBPASS"]) or die("�޷����ӵ�������!");
	@mysql_select_db($sysVoteConfig["DBNAME"],$link);
	return $link;
}

function sysvote_db_close($link)
{
	@mysql_close($link);
}

function sysvote_is_admin($currentuser)
{
	global $sysVoteConfig;
	if(bbs_is_bm($sysVoteConfig["BRDNUM"], $currentuser["index"]))
		return TRUE;
	else
		return FALSE;
}

function html_format($str,$wrap=FALSE,$stripSlashes=TRUE)
{
	if($stripSlashes) $str = stripslashes($str);
	$str = str_replace(" ","&nbsp;",htmlspecialchars($str));	
	if($wrap) $str = nl2br($str);
	return $str;
}

function time_format($t)
{
	$t= $t[0].$t[1].$t[2].$t[3]."-".$t[4].$t[5]."-".$t[6].$t[7]." ".$t[8].$t[9].":".$t[10].$t[11].":".$t[12].$t[13];
	return $t;
}

function svGetManagers($managers)
{
	if($managers=="") return NULL;
	$manager = explode(" ",$managers);
	$manArr = array();
	$manStr = "";
	$manHtmlStr = "";
	for($i = 0 ;$i < count($manager);$i++)
	{
		$manager[$i] = trim(ltrim($manager[$i]));
		if(!$manager[$i]) continue;
		$manStr[] = $manager[$i];
		if($manStr!="")
			$manStr .= "|".$manager[$i];
		else
			$manStr = $manager[$i];
		$manHtmlStr .= "<a href=\"/bbsqry.php?userid=".htmlspecialchars($manager[$i])."\">".htmlspecialchars($manager[$i])."</a>";	
	}
	return array("MANARR" => $manArr , "MANSTR" => $manStr , "HTMLSTR" => $manHtmlStr);
}

function svGetProbEtems($etems,$proType)
{
	if($proType!=4)
	{
		$etem = explode("|",$etems);
		$etemArr = array();
		for($i = 0 ;$i < count($etem) ; $i ++)
		{
			$etem[$i] = ltrim(trim($etem[$i]));
			if($etem[$i]=="") continue;
			$etemArr[] = $etem[$i];	
		}
	}
	else
		$etemArr[0] = trim(ltrim($etems));
	return $etemArr;
}
	
function svPostAnnouce($board,$svInfor)
{
		$brdArr=array();
		$boardID= bbs_getboard($board,$brdArr);
		if( $boardID == 0) die("ָ���İ��治����!");
		
		$title = "[֪ͨ] �ٰ�ϵͳͶƱ��".$svInfor[subject];
		$content = "����ƪ���������Զ�����ϵͳ��������\n\n".
			   "�� ͶƱ�����ڣ�".date("D M d H:i:s Y")."  ���ϵͳͶƱ\n".
			   "�� ���⣺".$svInfor[subject]."\n".
			   "�� ͶƱ˵����\n\n".$svInfor[desc];
				
		$ret = bbs_postarticle($board, $title, $content, 0 , 0 , 0, 0 );
		return $ret;
}
		
class sysVoteAdmin
{
	var $user ;
	
	function svDelVote($link,$svid)
	{
		$svid = (int)($svid);
		$query = "DELETE FROM sysvote_users WHERE svid = '".$svid."';";
		mysql_query($query,$link);
		$query = "DELETE FROM sysvote_votes WHERE svid = '".$svid."';";
		mysql_query($query,$link);
	}
	
	function svEndVote($link,$svid,$annType,$annBoard)
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;
		$vote = sysvote_load_vote_infor($link,$svid);
		
		//��ʼ����������
		$ann = "�� ͶƱ�����ڣ�".$vote[created]."  ���ϵͳͶƱ\n".
			"�� ���⣺".$vote[ori][subject]."\n".
			"�� ͶƱ˵����\n\n".$vote[ori][description]."\n\n\n".
		$voteres = array();
		for($i=0 ; $i< count($vote[probs]) ;$i++)
		{
			$etemNum[$i] = count($vote[probs][$i][etems]);
			for($j = 0 ; $j < $etemNum[$i] ;$j++)
				$voteres[$i][$j+1]=0;
			if($vote[probs][$i][type]==4)
				$voteres[$i][0]="����ͨ��ͶƱ������������£�";
			if($vote[probs][$i][type]==2 || $vote[probs][$i][type]==3)
			{
				$voteres[$i][$etemNum[$i]+1]="����ͨ��ͶƱ������������£�";
			}
		}
		//���н��ͳ��
		$query = "SELECT * FROM sysvote_users WHERE svid = '".$vote[svid]."' ORDER BY suid";
		$result = mysql_query($query,$link);
		$voteUserNum = mysql_num_rows($result);
		for($k = 0 ; $k < $voteUserNum ; $k ++)
		{
			$rows = mysql_fetch_array($result);
			$userres = explode("|",$rows[voteinfo]);
			for($i = 0 ; $i < count($vote[probs]) ; $i ++)
			{
				if($vote[probs][$i][type]==4)//�ʴ�
				{
					$voteres[$i][0] .= "\n���������������������������������������������������������������������������\n ".$rows[userid]." �� ".time_format($rows[votetime])." ˵���� \n".base64_decode($userres[$i]);	
				}
				elseif($vote[probs][$i][type]==1 || $vote[probs][$i][type]==3)//��ѡ
				{
					$etemres = explode("&",$userres[$i]);
					for($l = 0 ; $l < $etemNum[$i] ; $l ++)
						$voteres[$i][(int)($etemres[$l])] ++ ;
					if($vote[probs][$i][type]==3 && $etemres[count($etemres)-1])
						$voteres[$i][$etemNum[$i]+1] .= "\n���������������������������������������������������������������������������\n ".$rows[userid]." �� ".time_format($rows[votetime])." ˵���� \n".base64_decode($etemres[count($etemres)-1]);	
				}
				elseif($vote[probs][$i][type]==2) // ��ѡ+�ʴ�
				{
					$etemres = explode("&",$userres[$i]);
					$voteres[$i][(int)($etemres[0])] ++ ;
					if($etemres[1])
						$voteres[$i][$etemNum[$i]+1] .= "\n���������������������������������������������������������������������������\n ".$rows[userid]." �� ".time_format($rows[votetime])." ˵���� \n".base64_decode($etemres[1]);			
				}
				else // ��ѡ
				{
					$voteres[$i][(int)($userres[$i])] ++ ;	
				}
				
			}// i ѭ�� ��Ŀ
		}// k ѭ�� �� �û�
	
		//����ͶƱ���
		$ann = "�� ����ͶƱ���û�����".$voteUserNum."\n".
			"�� ͶƱ�����\n\n\n";
		// $res�Ľṹ �� ����ͶƱ���û�����|��Ŀ1|...|��Ŀn|�ı����
		$res = $voteUserNum;
		if($voteUserNum == 0) $voteUserNum = 1;
		$probType = array("����ѡ��","����ѡ��","����ѡ��(�ɷ������)","����ѡ��(�ɷ������)","�ʴ�");
		for($i = 0 ; $i < count($vote[probs]) ; $i ++)
		{
			$ann .= "\n\n\n�������������������������������������� ".($i+1)."����������������������������������\n".
				"���� ".($i+1)."��".$vote[probs][$i][prob]."\n".
				"���ͣ�".$probType[$vote[probs][$i][type]]."\n";
			if($vote[probs][$i][type]==4)
			{
				$ann .= "\n".$voteres[$i][0]; //�ʴ�
				$res .= "|".base64_encode($voteres[$i][0]);
			}
			else
			{
				
				$res .= "|";
				for($j = 0 ; $j < $etemNum[$i] ;$j++)
				{
					if($j != 0) $res .= ",";
					$res .= $voteres[$i][$j+1];
					$ann .= "\nѡ�� ".($j + 1)."��".$vote[probs][$i][etems][$j].
						"    Ʊ����".$voteres[$i][$j+1]."  Լռ".(((int)($voteres[$i][$j+1]*1000 / $voteUserNum) ) / 10)."%";
				}// j ѭ��  ѡ��
				if($vote[probs][$i][type]==2 || $vote[probs][$i][type]==3)
				{
					$ann .= "\n\n".$voteres[$i][$etemNum[$i]+1];
					$res .= ",".base64_encode($voteres[$i][$etemNum[$i]+1]);
				}
			}
		}// i ѭ��   ��Ŀ
		$res .= "|".base64_encode($ann);
		$voteLog = $vote[logs]."\n����Ա ".$this->user["userid"]." �� ".date("Y-m-d H:i:s")." �� ".$_SERVER["REMOTE_ADDR"]." ��������ͶƱ;";
		$query = "UPDATE sysvote_votes SET active = 0 , results = '".$res."' , logs = '".$voteLog."' WHERE svid = '".$vote[svid]."';";
		mysql_query($query,$link);
		$query = "DELETE FROM sysvote_users WHERE svid = ".$vote[svid];
		mysql_query($query,$link);
		
		if($annType==1)
			$annBoard = "vote";
		elseif($annType==2)
			$annBoard = $sysVoteConfig["BOARD"];
		elseif($annType==3)
		{
			$brdArr=array();
			$boardID= bbs_getboard($annBoard,$brdArr);
			if( $boardID == 0) 
				html_error_quit("ָ���İ��治����!");	
		}
		if($annType!=0)
		{
			$title = "[����] ϵͳͶƱ���";
			$content = "����ƪ���������Զ�����ϵͳ��������\n\n".$ann;
			$ret = bbs_postarticle($annBoard, $title, $content, 0 , 0 , 0, 0 );
		}
	}
	
	function svCreateVoteStepOne()
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;
?>
<center>
<p align=center><strong>����ϵͳͶƱ</strong></p>
<form action="<?php echo $_SERVER["PHP_SELF"]."?act=create&step=2"; ?>" method="post">
<table cellspacing=0 cellpadding=5 border=0 class=t1 width=90%>
	<tr>
		<td class=t2 colspan=2>��һ��:�趨������Ϣ</td>
	<tr>
		<td class=t3>����ԱID</td>
		<td class=t5><?php echo $this->user[userid]; ?></td>
	</tr>
	<tr>
		<td class=t3>��ǰʱ��</td>
		<td class=t5><?php echo date("Y-m-d H:i:s"); ?></td>
	</tr>
	<tr>
		<td class=t3>ͶƱ����</td>
		<td class=t5>
			<input type="text" name="svsubject" size=50 class=b9 maxlength=255 value="<?php echo rawurldecode($_POST["svsubject"]); ?>">(��ʾ����ҳͶƱ��������)
		</td>
	</tr>
	<tr>
		<td class=t3>ͶƱ˵��</td>
		<td class=t5>
			<textarea cols=50 rows=8 class=b9 name="svdesc"><?php echo rawurldecode($_POST["svdesc"]); ?></textarea>
		</td>
	</tr>
	<tr>
		<td class=t3>�Ƿ񷢱�ͶƱ����</td>
		<td class=t5>
			<input type=radio name="svannouce" class=b9 value=1 <?php echo ($_POST["svannouce"]==1)?"checked":""; ?>>��vote�淢������
			<input type=radio name="svannouce" class=b9 value=2 <?php echo ($_POST["svannouce"]==2)?"checked":""; ?>>��<?php echo $sysVoteConfig["BOARD"]; ?>�淢������
			<input type=radio name="svannouce" class=b9 value=0 <?php echo ($_POST["svannouce"]==0)?"checked":""; ?>>�����κΰ��淢������
		</td>
	</tr>
	<tr>
		<td class=t3>ͶƱʱ��</td>
		<td class=t5>
			<input type="text" name="svtimelong" size=2 class=b9 maxlength=2 value="<?php echo $_POST["svtimelong"]?$_POST["svtimelong"]:7; ?>">��
		</td>
	</tr>
	<tr>
		<td class=t3>�Ƿ����������û�ͶƱ</td>
		<td class=t5>
			<input type=radio name="svanonymous" class=b9 value=0  <?php echo ($_POST["svanonymous"]==0)?"checked":""; ?>>������
			<input type=radio name="svanonymous" class=b9 value=1  <?php echo ($_POST["svanonymous"]==1)?"checked":""; ?>>����
		</td>
	</tr>
	<tr>
		<td class=t3>ÿ��IP�����ͶƱ��</td>
		<td class=t5>
			<input type="text" name="svvotesperip" size=2 class=b9 maxlength=2 value="<?php echo $_POST["svvotesperip"]?$_POST["svvotesperip"]:1; ?>">��(���ձ�ʾ������IPͶƱ��)
		</td>
	</tr>
	<tr>
		<td class=t2 colspan=2>
			<input type=submit value="��һ��>>" class=b9>
		</td>
	</tr>
	<input type="hidden" name="svprobnum"" value="<?php echo (int)($_POST["svprobnum"]); ?>">
<?php
	for($i = 0 ; $i< (int)($_POST["svprobnum"]) ; $i ++)
	{
?>	
	<input type="hidden" name="svprob<?php echo $i + 1; ?>" value="<?php echo $_POST["svprob".($i + 1)]; ?>">
	<input type="hidden" name="svprobtype<?php echo $i + 1 ;?>" value="<?php echo (int)($_POST["svprobtype".($i+1)]); ?>">
	<input type="hidden" name="svprobetem<?php echo $i + 1 ;?>" value="<?php echo $_POST["svprobetem".($i+1)]; ?>">
<?php	
	}
?>	
</table></form>
</center>
<?php	
	}
	
	function svCreateVoteStepTwo()
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;
?>
<center>
<p align=center><strong>����ϵͳͶƱ</strong></p>
<form name="svs2" id="svs2" action="<?php echo $_SERVER["PHP_SELF"]."?act=create&step=3"; ?>" method="post">
<table cellspacing=0 cellpadding=5 class=t1 border=0 width=90%>
	<tr><td colspan=2 class=t2>�ڶ���:ȷ�ϻ�����Ϣ</td></tr>
	<input type="hidden" name="svsubject" value="<?php echo $_POST["svsubject"]; ?>">
	<tr>
		<td class=t3>ͶƱ����</td>
		<td class=t5><?php echo html_format($_POST["svsubject"],FALSE,FALSE); ?>&nbsp;</td>
	</tr>
	<input type="hidden" name="svdesc" value="<?php echo $_POST["svdesc"]; ?>">
	<tr>
		<td class=t3>ͶƱ˵��</td>
		<td class=t5><?php echo html_format($_POST["svdesc"],TRUE,FALSE); ?>&nbsp;</td>
	</tr>
	<input type="hidden" name="svannouce" value="<?php echo (int)($_POST["svannouce"]); ?>">
	<tr>
		<td class=t3>ͶƱ����</td>
		<td class=t5>
		<?php
			switch($_POST["svannouce"])
			{
				case 1:
					echo "��Vote�淢������";
					break;
				case 2:
					echo "��".$sysVoteConfig["BOARD"]."�淢������";
					break;
				default:
					echo "������ͶƱ����";
			}
		?>
		</td>
	</tr>
	<input type="hidden" name="svtimelong" value="<?php echo (int)($_POST["svtimelong"]); ?>">
	<tr>
		<td class=t3>ͶƱʱ��</td>
		<td class=t5><?php echo (int)($_POST["svtimelong"]); ?>&nbsp;</td>
	</tr>
	<input type="hidden" name="svanonymous" value="<?php echo (int)($_POST["svanonymous"]); ?>">
	<tr>
		<td class=t3>�Ƿ���������ͶƱ</td>
		<td class=t5><?php echo ((int)($_POST["svanonymous"])==0)?"������":"����"; ?></td>
	</tr>
	<input type="hidden" name="svvotesperip" value="<?php echo (int)($_POST["svvotesperip"]); ?>">
	<tr>
		<td class=t3>ÿ��IP����ͶƱ����</td>
		<td class=t5><?php echo ((int)($_POST["svvotesperip"])==0)?"������":$_POST["svvotesperip"]."��"; ?></td>
	</tr>
	<tr>
		<td class=t2 colspan=2>��˶�����ͶƱ��Ϣ,ȷ������������뱾��ͶƱ�������,����"��һ��";���򷵻�"��һ�������޸�.</td>
	</tr>
	<tr>
		<td class=t3>�������</td>
		<td class=t5><input type="text" class="b9" name="svprobnum" value="<?php echo max(1,(int)($_POST["svprobnum"])); ?>" size="3" maxlength="3">��</td>
	</tr>
	<tr>
		<td class=t2 colspan=2>
		<input type="button" value="<<��һ��" class="b9" onclick="document.svs2.action='<?php echo $_SERVER["PHP_SELF"]; ?>?act=create&step=1';document.svs2.submit();">
		<input type="submit" value="��һ��>>" class="b9">
		</td>
	</tr>
</table>
<?php
	for($i = 0 ; $i< (int)($_POST["svprobnum"]) ; $i ++)
	{
?>	
	<input type="hidden" name="svprob<?php echo $i + 1; ?>" value="<?php echo $_POST["svprob".($i + 1)]; ?>">
	<input type="hidden" name="svprobtype<?php echo $i + 1 ;?>" value="<?php echo (int)($_POST["svprobtype".($i+1)]); ?>">
	<input type="hidden" name="svprobetem<?php echo $i + 1 ;?>" value="<?php echo $_POST["svprobetem".($i+1)]; ?>">
<?php	
	}
?>	
</form>
</center>
<?php		
	}
	
	function svCreateVoteStepThree()
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;

?>
<center>
<p align=center><strong>����ϵͳͶƱ</strong></p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?act=create&step=4" name="svs3" id="svs3" method="post">
<table cellspacing=0 cellpadding=5 class=t1 border=0 width=90%>
	<tr><td colspan=2 class=t2>������:�趨ͶƱѡ��</td></tr>
	<input type="hidden" name="svsubject" value="<?php echo $_POST["svsubject"]; ?>">
	<input type="hidden" name="svdesc" value="<?php echo $_POST["svdesc"]; ?>">
	<input type="hidden" name="svannouce" value="<?php echo (int)($_POST["svannouce"]); ?>">
	<input type="hidden" name="svtimelong" value="<?php echo (int)($_POST["svtimelong"]); ?>">
	<input type="hidden" name="svanonymous" value="<?php echo (int)($_POST["svanonymous"]); ?>">
	<input type="hidden" name="svvotesperip" value="<?php echo (int)($_POST["svvotesperip"]); ?>">
	<input type="hidden" name="svprobnum"" value="<?php echo (int)($_POST["svprobnum"]); ?>">
<?php
	for($i = 0 ; $i< (int)($_POST["svprobnum"]) ; $i ++)
	{
?>	
	<input type="hidden" name="svprob<?php echo $i + 1; ?>" value="<?php echo $_POST["svprob".($i + 1)]; ?>">
	<input type="hidden" name="svprobtype<?php echo $i + 1 ;?>" value="<?php echo (int)($_POST["svprobtype".($i+1)]); ?>">
	<input type="hidden" name="svprobetem<?php echo $i + 1 ;?>" value="<?php echo $_POST["svprobetem".($i+1)]; ?>">
<?php	
	}
	for($i = 0 ; $i< (int)($_POST["svprobnum"]) ; $i ++)
	{
?>	
	<tr>
		<td class=t2></strong>����<font color=#FF0000><?php echo $i + 1; ?></font>:</strong></td>
		<td class=t5><input type="text" name="svprob<?php echo $i+1; ?>" size=100 class=b9 value="<?php echo $_POST["svprob".($i + 1)]; ?>"></td>
	</tr>
	<tr>
		<td class=t3>����</td>
		<td class=t5>
			<input type="radio" class=b9 name="svprobtype<?php echo $i + 1; ?>" value=0 <?php echo ((int)($_POST["svprobtype".($i+1)])==0)?"checked":""; ?>>��ѡ
			<input type="radio" class=b9 name="svprobtype<?php echo $i + 1; ?>" value=1 <?php echo ((int)($_POST["svprobtype".($i+1)])==1)?"checked":""; ?>>��ѡ
			<input type="radio" class=b9 name="svprobtype<?php echo $i + 1; ?>" value=2 <?php echo ((int)($_POST["svprobtype".($i+1)])==2)?"checked":""; ?>>��ѡ���ʴ�
			<input type="radio" class=b9 name="svprobtype<?php echo $i + 1; ?>" value=3 <?php echo ((int)($_POST["svprobtype".($i+1)])==3)?"checked":""; ?>>��ѡ���ʴ�
			<input type="radio" class=b9 name="svprobtype<?php echo $i + 1; ?>" value=4 <?php echo ((int)($_POST["svprobtype".($i+1)])==4)?"checked":""; ?>>�ʴ�
		</td>
	</tr>
	<tr>
		<td class=t3>ѡ��</td>
		<td class=t5>
		<textarea name="svprobetem<?php echo $i + 1; ?>" class=b9 cols=80 rows=3><?php echo $_POST["svprobetem".($i+1)]; ?></textarea>
		<br />(����"|"���Ÿ���ѡ��������Ķ��ѡ��,�ʴ���ֱ��������Ŀ.)
		</td>
	</tr>
<?php
	}
?>
	<tr>
		<td class=t2 colspan=2>
		<input type="button" value="<<��һ��" class="b9" onclick="document.svs3.action='<?php echo $_SERVER["PHP_SELF"]; ?>?act=create&step=2';document.svs3.submit();">
		<input type="submit" value="��һ��>>" class="b9">
		</td>
	</tr>
</table>
</form>
</center>
<?php		
	}

	function svCreateVoteStepFour()
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;

?>
<center>
<p align=center><strong>����ϵͳͶƱ</strong></p>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>?act=create&step=5" name="svs3" id="svs3" method="post">
<table cellspacing=0 cellpadding=5 class=t1 border=0 width=90%>
	<tr><td colspan=2 class=t2>���Ĳ�:ȷ��ͶƱѡ��</td></tr>
	<input type="hidden" name="svsubject" value="<?php echo $_POST["svsubject"]; ?>">
	<input type="hidden" name="svdesc" value="<?php echo $_POST["svdesc"]; ?>">
	<input type="hidden" name="svannouce" value="<?php echo (int)($_POST["svannouce"]); ?>">
	<input type="hidden" name="svtimelong" value="<?php echo (int)($_POST["svtimelong"]); ?>">
	<input type="hidden" name="svanonymous" value="<?php echo (int)($_POST["svanonymous"]); ?>">
	<input type="hidden" name="svvotesperip" value="<?php echo (int)($_POST["svvotesperip"]); ?>">
	<input type="hidden" name="svprobnum"" value="<?php echo (int)($_POST["svprobnum"]); ?>">
<?php
	for($i = 0 ; $i< (int)($_POST["svprobnum"]) ; $i ++)
	{
?>	
	<input type="hidden" name="svprob<?php echo $i + 1; ?>" value="<?php echo $_POST["svprob".($i + 1)]; ?>">
	<input type="hidden" name="svprobtype<?php echo $i + 1 ;?>" value="<?php echo (int)($_POST["svprobtype".($i+1)]); ?>">
	<input type="hidden" name="svprobetem<?php echo $i + 1 ;?>" value="<?php echo $_POST["svprobetem".($i+1)]; ?>">
	<tr>
		<td class=t2 width=120></strong>����<font color=#FF0000><?php echo $i + 1; ?></font>:</strong></td>
		<td class=t5><?php echo html_format($_POST["svprob".($i + 1)],TRUE,FALSE); ?>&nbsp;</td>
	</tr>
	<tr>
		<td class=t3>����</td>
		<td class=t5>
		<?php
			switch((int)($_POST["svprobtype".($i+1)]))
			{
				case 0:
					echo "��ѡ";
					break;
				case 1:
					echo "��ѡ";
					break;
				case 2:
					echo "��ѡ���ʴ�";
					break;
				case 3:
					echo "��ѡ���ʴ�";
					break;
				case 4:
					echo "�ʴ�";
					break;
				default:
			}
		?>
		</td>
	</tr>
	<tr>
		<td class=t3>ѡ��</td>
		<td class=t5>
		<?php
			$etems = svGetProbEtems($_POST["svprobetem".($i+1)],(int)($_POST["svprobtype".($i+1)]));
			for($j = 0 ; $j < count($etems) ; $j ++ )
			{
				echo "<strong>ѡ��<font color=#FF0000>".($j + 1)."</font></strong>\n&nbsp;".html_format($etems[$j],TRUE,FALSE)."<br />";
			}
		?>
		</td>
	</tr>
<?php
	}
?>
	<tr>
		<td class=t2 colspan=2>
		<input type="button" value="<<��һ��" class="b9" onclick="document.svs3.action='<?php echo $_SERVER["PHP_SELF"]; ?>?act=create&step=3';document.svs3.submit();">
		<input type="submit" value="����ϵͳͶƱ" class="b9">
		</td>
	</tr>
</table>
</form>
</center>
<?php		
	}

	function svCreateVoteStepFive($link)
	{
		global $currentuser,$sysVoteConfig;
		$this->user = $currentuser;
		$svInfor = array(
				"subject" => $_POST["svsubject"],
				"desc" => $_POST["svdesc"],
				"annouce" => (int)($_POST["svannouce"]),
				"timelong" => (int)($_POST["svtimelong"]),
				"anonymous" => (int)($_POST["svanonymous"]),
				"votesperip" => (int)($_POST["svvotesperip"]),
				"probnum" => (int)($_POST["svprobnum"]),
				"created" => time()
					);
		$etems = "";
		for($i = 0 ; $i < (int)($_POST["svprobnum"]) ; $i ++ )
		{
			 if($etems!="") $etems .= "|";
			 $etems .= base64_encode($_POST["svprob".($i + 1)])."&".(int)($_POST["svprobtype".($i+1)])."&".base64_encode($_POST["svprobetem".($i+1)]); 
		}
		$svInfor[etems] = $etems;
		
		$svInfor[logs] = addslashes("����Ա ".$this->user["userid"]." �� ".date("Y-m-d H:i:s")." �� ".$_SERVER["REMOTE_ADDR"]." ��������ͶƱ;");
		$query = "INSERT INTO `sysvote_votes` ( `svid` , `active` , `subject` , `description` , `changed` , `created` , `annouce` , `probs` , `timelong` , `anonymousvote` , `votesperip` , `votecount` , `logs` , `results` ) ".
			"VALUES ('', '1', '".addslashes($svInfor[subject])."', '".addslashes($svInfor[desc])."', '".date("YmdHis")."' , '".date("YmdHis")."', '".$svInfor[annouce]."', '".addslashes($svInfor[etems])."', '".$svInfor[timelong]."', '".$svInfor[anonymous]."', '".$svInfor[votesperip]."', '0', '".$svInfor[logs]."', '');";
		mysql_query($query,$link);
		
		if($svInfor[annouce] == 2 )
			svPostAnnouce($sysVoteConfig["BOARD"],$svInfor);
		if($svInfor[annouce] == 1)
			svPostAnnouce("vote",$svInfor);
			
		//����html�ĵ���mainpageʹ��
		$query = "SELECT svid FROM sysvote_votes WHERE subject = '".addslashes($svInfor[subject])."' AND probs = '".addslashes($svInfor[etems])."' ORDER BY svid DESC LIMIT 0,1";
		$result = mysql_query($query,$link);
		$rows = mysql_fetch_array($result);
		$newSvid = $rows[svid];
		$htmlFile = "<form action=\"/bbssysvote.php?svid=".$newSvid."&sv=sv\" method=\"post\"><table width=\"100%\" height=\"18\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"helpert\">".
				"<tr><td width=\"16\" background=\"images/lt.gif\">&nbsp;</td><td width=\"66\" bgcolor=\"#0066CC\">ϵͳͶƱ</td><td width=\"16\" background=\"images/rt.gif\"></td>".
				"<td>&nbsp;</td></tr></table><table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\" class=\"helper\">".
				"<tr><td width=\"100%\" class=\"MainContentText\">".
				sysvote_display_probs(sysvote_get_probs($svInfor[etems])).
				"<p align=center><input type=submit class=button value=\"ͶƱ\">".
				"<input type=button class=button value=\"�鿴\" onclick=\"window.location.href='/bbssysvote.php';\">".
				"</p></td></tr></table></form>";
		$fp = fopen(BBS_HOME . "/vote/sysvote.html" , "w");
		fputs($fp,$htmlFile,strlen($htmlFile));
		fclose($fp);
?>
<br /><br /><br />
<p align=center><strong>ϵͳͶƱ�����ɹ���</strong></p>
<p align=center>[<a href="/bbssysvote.php">�������</a>]</p>
<?php
	}
}

function sysvote_get_etems($etem,$type)
{
	$etems = array();
	if($type==4)
		$etems[0] = $etem;
	else
		$etems = explode("|",$etem);
	return $etems;
}

function sysvote_get_probs($probs)
{
	$prob = array();
	$probarr = explode("|",$probs);
	for($i = 0 ; $i < count($probarr) ; $i ++)
	{
		if($probdept[1]==4)	$probdept[0]=$probs;
		$probdept = explode("&",$probarr[$i]);
		$prob[$i] = array(
				"prob" => base64_decode($probdept[0]),
				"type" => (int)($probdept[1]),
				"etems" => sysvote_get_etems(base64_decode($probdept[2]),base64_decode($probdept[1]))
				);
	}
	return $prob;
}

function sysvote_display_probs($probs)
{
	for($i = 0 ; $i < count($probs) ; $i ++)
	{
		$output = html_format($probs[$i][prob],TRUE,FALSE)."\n<br />";	
		switch($probs[$i][type])
		{
			case 0:
				for($j = 0 ; $j < count($probs[$i][etems]) ; $j ++ )
					$output .=  "<input type=\"radio\" name=\"ans".$i."\" value=\"".($j + 1)."\">\n<font color=#FF0000><strong>".($j+1)."</strong></font>. ".html_format($probs[$i][etems][$j],TRUE,FALSE)."\n<br />";
				break;	
			case 1:
				for($j = 0 ; $j < count($probs[$i][etems]) ; $j ++ )
					$output .=  "<input type=\"checkbox\" name=\"ans".$i."e".$j."\" value=\"1\">\n<font color=#FF0000><strong>".($j+1)."</strong></font>. ".html_format($probs[$i][etems][$j],TRUE,FALSE)."\n<br />";
				break;	
			case 2:
				for($j = 0 ; $j < count($probs[$i][etems]) ; $j ++ )
					$output .=  "<input type=\"radio\" name=\"ans".$i."\" value=\"".($j + 1)."\">\n<font color=#FF0000><strong>".($j+1)."</strong></font>. ".html_format($probs[$i][etems][$j],TRUE,FALSE)."\n<br />";
				$output .= "<textarea class=b9 cols=50 rows=5 name=\"ans".$i."p\"></textarea>\n<br />";
				break;	
			case 3:
				for($j = 0 ; $j < count($probs[$i][etems]) ; $j ++ )
					$output .=  "<input type=\"checkbox\" name=\"ans".$i."e".$j."\" value=\"1\">\n<font color=#FF0000><strong>".($j+1)."</strong></font>. ".html_format($probs[$i][etems][$j],TRUE,FALSE)."\n<br />";
				$output .= "<textarea class=b9 cols=50 rows=5 name=\"ans".$i."p\"></textarea>\n<br />";
				break;	
			case 4:
				$output .= html_format($probs[$i][etems][0],TRUE,FALSE)."<br /><textarea class=b9 cols=50 rows=5 name=\"ans".$i."\"></textarea>\n<br />";
			default:	
		}
		$output .=  "<br />";
		return $output;
	}
}

function sysvote_load_votes($link,$start=1,$all=FALSE)
{
	global $sysVoteConfig;	
	$start = (int)($start);
	if($start < 1) $start = 1;
	$query = "SELECT * FROM sysvote_votes ORDER BY active DESC , svid DESC LIMIT ".(($start - 1)*$sysVoteConfig["PAGESIZE"])." , ".$sysVoteConfig["PAGESIZE"]." ;";
	$result = mysql_query($query,$link);
	$votesNum = mysql_num_rows($result);
	$votes = array();
	for($i = 0 ; $i < $votesNum ; $i ++ )
	{
		$rows = mysql_fetch_array($result);
		$votes[$i] = array(
				"svid" => $rows[svid],
				"active" => $rows[active],
				"subject" => html_format($rows[subject]),
				"desc" => html_format($rows[description],TRUE),
				"changed" => time_format($rows[changed]),
				"created" => time_format($rows[created]),
				"annouce" => $rows[annouce],
				"probs" => sysvote_get_probs(stripslashes($rows[probs])),
				"timelong" => $rows[timelong],
				"anonymous" => $rows[anonymousvote],
				"votesperip" => $rows[votesperip],
				"votecount" => $rows[votecount],
				"logs" => $rows[logs]
				);
	}
	return $votes;
}

function sysvote_load_vote_infor($link,$svid)
{
	global $sysVoteConfig;	
	$query = "SELECT * FROM sysvote_votes WHERE  svid = ".(int)($svid).";";
	$result = mysql_query($query,$link);
	$rows = mysql_fetch_array($result);
	if(!$rows)
		return FALSE;
	$vote = array(
			"svid" => $rows[svid],
			"active" => $rows[active],
			"subject" => html_format($rows[subject]),
			"desc" => html_format($rows[description],TRUE),
			"changed" => time_format($rows[changed]),
			"created" => time_format($rows[created]),
			"annouce" => $rows[annouce],
			"probs" => sysvote_get_probs(stripslashes($rows[probs])),
			"timelong" => $rows[timelong],
			"anonymous" => $rows[anonymousvote],
			"votesperip" => $rows[votesperip],
			"votecount" => $rows[votecount],
			"logs" => $rows[logs],
			"ori" => $rows,
			"res" => $rows[results]
			);
	return $vote;
}

function sysvote_user_can_vote($link,$vote,$currentuser)
{
	if($vote[anonymous]==0 && ($currentuser[userid]=="guest"||!$currentuser))
		return -1;//�����û�
	$query = "SELECT userid FROM sysvote_users WHERE svid = '".$vote[svid]."' AND ( userid = '".$currentuser[userid]."' OR votehost = '".$currentuser[lasthost]."' )";
	$result = mysql_query($query,$link);
	$i = 0 ;
	while($rows=mysql_fetch_array($result))
	{
		if(strtolower($rows[userid])==strtolower($currentuser[userid]) && $currentuser[userid]!="guest")
			return -2;//��ID��ͶƱ	
		$i ++;
	}
	if($vote[votesperip]!=0 && $i >= $vote[votesperip])
		return -3; //��IP�Ѵﵽ���ͶƱ��
	return 0;//����ͶƱ
}

function sysvote_user_vote($link,$vote,$currentuser,$ans)
{
	$query = "INSERT INTO `sysvote_users` ( `suid` , `svid` , `userid` , `votehost` , `votetime` , `voteinfo` ) ".
		"VALUES ('', '".$vote[svid]."', '".$currentuser[userid]."', '".$currentuser[lasthost]."', '".date("YmdHis")."', '".$ans."');";
	mysql_query($query,$link);
	$voteLogs = $vote[logs]."\n�û� ".$currentuser[userid]." �� ".date("Y-m-d H:i:s")." �� ".$_SERVER["REMOTE_ADDR"]." ����ͶƱ;";
	$query = "UPDATE sysvote_votes SET votecount = votecount + 1 , logs = '".$voteLogs."' WHERE svid = '".$vote[svid]."';";
	mysql_query($query,$link);
}

function sysvote_display_result($vote)
{
	$res = explode("|",$vote[res]);
	$voteUserNum = max(1,$res[0]);
	$probType = array("����ѡ��","����ѡ��","����ѡ��(�ɷ������)","����ѡ��(�ɷ������)","�ʴ�");
	for($i = 0 ; $i < count($vote[probs]) ; $i ++)
	{
?>
<table cellspacing=0 cellpadding=5 width=100% border=0 class=t1>
	<tr>
		<td colspan=2 class=t3><strong><?php echo $i+1; ?></strong></td>
	</tr>
	<tr>
		<td class=t3 width=200>����</td>
		<td class=t7><?php echo html_format($vote[probs][$i][prob],TRUE,FALSE); ?></td>
	</tr>
	<tr>
		<td class=t3>����</td>
		<td class=t7><?php echo $probType[$vote[probs][$i][type]]; ?></td>
	</tr>
<?php
	if($vote[probs][$i][type]==4)
		echo "<tr><td class=t3>���ѽ���</td><td class=t7>".html_format(base64_decode($res[$i+1]),TRUE,FALSE)."</td></tr>";
	else
	{
		$etemcount = explode(",",$res[$i+1]);
		for($j = 0 ;$j < count($vote[probs][$i][etems])	; $j ++)
		{
			$etemcount1 = ((int)(($etemcount[$j]*1000 )/ $voteUserNum)) / 10;
			echo "<tr><td class=t3><span title=\"".html_format($vote[probs][$i][etems][$j],FALSE,FALSE)."\">";
			echo html_format(substr($vote[probs][$i][etems][$j],0,24),FALSE,FALSE);
			if(strlen($vote[probs][$i][etems][$j])>24) echo " ... \n";
			echo "</span></td><td class=t7>";
			echo "<img src=\"images/bar".($j%10 + 1).".gif\" width=\"".($etemcount1*0.8)."%\" height=10 alt=\"Ʊ����".$etemcount[$j]."  ������".$etemcount1."\" border=0>";
			echo "&nbsp;".$etemcount1."%";
			echo "</td></tr>";
		}
	}
	if($vote[probs][$i][type]==2 || $vote[probs][$i][type]==3)
		echo "<tr><td class=t3>���ѽ���</td><td class=t7>".html_format(base64_decode($etemcount[count($vote[probs][$i][etems])]),TRUE,FALSE)."</td></tr>";
	
?>	
</table>
<?php
	}// i ѭ��  ��Ŀ
}

?>