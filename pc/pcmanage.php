<?php
	/*
	**manage personal corp.
	**@id: windinsn Nov 19,2003	
	*/
	/*
	**	���ղؼеļ��С����Ʋ�����Ҫ session ֧�� windinsn nov 25,2003
	*/
	require("pcfuncs.php");
	function pc_save_posts($subject,$body,$htmltag) {
?>
<center>
<table cellspacing="0" cellpadding="5" border="0" class="t1" width="90%">
<tr><td class="t2">�����µ�¼���ٷ���</td></tr>
<tr><td class="t3">����</td></tr>
<tr><td class="t5"><?php echo html_format($subject); ?></td></tr>
<tr><td class="t3">����</td></tr>
<tr><td class="t5"><?php echo html_format($body,1,$htmltag); ?></td></tr>
</table>
</center>	    
<?php	    
	}
	
						
	if ($loginok != 1) {
		pc_html_init("gb2312","�����ĵ�");
		if ($_GET['act'] == 'post' && ($_POST['subject'] || $_POST['blogbody']))
		   pc_save_posts($_POST['subject'],$_POST['blogbody'],$_POST['htmltag']);
		html_error_quit("���ȵ�¼!");	
	}
	elseif(!strcmp($currentuser["userid"],"guest"))
	{
		pc_html_init("gb2312","�����ĵ�");
		if ($_GET['act'] == 'post' && ($_POST['subject'] || $_POST['blogbody']))
		   pc_save_posts($_POST['subject'],$_POST['blogbody'],$_POST['htmltag']);
		html_error_quit("���ȵ�¼!");		
	}
	else
	{
		$link = pc_db_connect();
		$pc = pc_load_infor($link,$_GET["userid"]);
		
		if(!$pc)
		{
			pc_db_close($link);
			html_error_quit("�Բ�����Ҫ�鿴��Blog������");
		}
		
		if(!pc_is_admin($currentuser,$pc))
		{
			pc_db_close($link);
			html_error_quit("�Բ�����Ҫ�鿴��Blog������");
		}
		
		if($pc["EDITOR"] != 1 && $pc["EDITOR"] != 3)
			$pcconfig["EDITORALERT"] = NULL;
			
		$act = $_GET["act"]?$_GET["act"]:$_POST["act"];
		$subject = isset($_POST["subject"])?$_POST["subject"]:"";
		
		if($act == "post" && !$subject && $pc["EDITOR"] != 0)
			pc_html_init("gb2312",stripslashes($pc["NAME"]),"","","",$pc["EDITOR"]);
		elseif($act == "edit" && !$subject && $pc["EDITOR"] != 0)
			pc_html_init("gb2312",stripslashes($pc["NAME"]),"","","",$pc["EDITOR"]);
		elseif($act != "favcut" && $act != "favcopy" && $act != "favpaste")
			pc_html_init("gb2312",stripslashes($pc["NAME"]));
		else
			;//nth :p
		
		if($act == "cut" || $act == "copy")
		{
			$access = intval($_POST["access"]);
			if(stristr($_POST["target"],'T'))
			{
				$target = intval(substr($_POST["target"],1,strlen($_POST["target"])-1));
				$in_section = 1;
				if(!pc_load_topic($link,$pc["UID"],$target,$topicname))
					$target = 0; //����������������δ����
			}
			else
			{
				$target = intval($_POST["target"]);
				$in_section = 0;
				if($target < 0 || $target > 4 )
					$target = 2;//�����������������˽����
			}
			
			
			if(!$in_section && 3 == $target ) //����  �����ղ���
			{
				$rootpid = pc_fav_rootpid($link,$pc["UID"]);
				if(!$rootpid)
				{
					html_error_quit("�ղؼи�Ŀ¼����!");
					exit();
				}
			}
			else
				$rootpid = 0;
			
			if($in_section)
			{
				if($act == "cut")
					$query = "UPDATE nodes SET created = created , `tid` = '".$target."' , `changed` = NOW( ) , `pid` = '0' WHERE `uid` = '".$pc["UID"]."' AND `type` = 0 AND ( `nid` = '0' ";
				else
					$query = "SELECT * FROM nodes WHERE `uid` = '".$pc["UID"]."' AND `type` = 0 AND ( `nid` = '0' ";
			}
			else
			{
				if($act == "cut" && $target == 3)
					$query = "UPDATE nodes SET created = created , `access` = '".$target."' , `changed` = '".date("YmdHis")."' , `pid` = '".$rootpid."', `tid` = 0 WHERE `uid` = '".$pc["UID"]."' AND ( `nid` = '0' ";
				elseif($act == "cut")
					$query = "UPDATE nodes SET created = created , `access` = '".$target."' , `changed` = '".date("YmdHis")."' , `pid` = '0' , `tid` = 0 WHERE `uid` = '".$pc["UID"]."' AND `type` = 0  AND ( `nid` = '0' ";
				else
					$query = "SELECT * FROM nodes WHERE `uid` = '".$pc["UID"]."' AND `type` = 0 AND ( `nid` = '0' ";
			}
				
			$j = 0;
			for($i = 1 ;$i < $pc["NLIM"]+1 ; $i ++)
			{
				if($_POST["art".$i])
				{
					$query .= " OR `nid` = '".(int)($_POST["art".$i])."' ";
					$j ++;
				}
			}
			$query .= " ) ";
			
			if($in_section)
			{
				if("cut" == $act)
				{
					mysql_query($query,$link);
				}
				else
				{
					$result = mysql_query($query,$link);
					$num_rows = mysql_num_rows($result);
					$j = $num_rows;
					if(pc_used_space($link,$pc["UID"],$access)+$num_rows > $pc["NLIM"])
					{
						html_error_quit("Ŀ�������������������� (".$pc["NLIM"]." ƪ)!");
						exit();
					}
					for($i = 0;$i < $num_rows ; $i ++)
					{
						/*	Ŀǰ�������µ�ʱ�����۲�ͬ������	*/
						$rows = mysql_fetch_array($result);
						$query = "INSERT INTO `nodes` ( `pid` , `tid` , `type` , `source` , `hostname` , `changed` , `created` , `uid` , `comment` , `commentcount` , `subject` , `body` , `access` , `visitcount` ,`htmltag`)  ".
							" VALUES ('0','".$target."' , '0', '".addslashes($rows["source"])."', '".addslashes($rows["hostname"])."','NOW( )' , '".$rows["created"]."', '".$pc["UID"]."', '".$rows["comment"]."', '0', '".addslashes($rows["subject"])."', '".addslashes($rows["body"])."', '".$access."', '0','".$rows["htmltag"]."');";
						mysql_query($query,$link);
					}
					if($access == 0)
						pc_update_record($link,$pc["UID"]," + ".$j);
				}
			}
			else
			{
				if($act == "cut")
				{
					if(pc_used_space($link,$pc["UID"],$target)+$j > $pc["NLIM"])
					{
						html_error_quit("Ŀ�������������������� (".$pc["NLIM"]." ƪ)!");
						exit();
					}
					else
					{
						mysql_query($query,$link);
					}
				}
				else
				{
					$result = mysql_query($query,$link);
					$num_rows = mysql_num_rows($result);
					$j = $num_rows;
					
					if(pc_used_space($link,$pc["UID"],$target)+$num_rows > $pc["NLIM"])
					{
						html_error_quit("Ŀ�������������������� (".$pc["NLIM"]." ƪ)!");
						exit();
					}
					for($i = 0;$i < $num_rows ; $i ++)
					{
						/*	Ŀǰ�������µ�ʱ�����۲�ͬ������	*/
						$rows = mysql_fetch_array($result);
						$query = "INSERT INTO `nodes` ( `pid` , `tid` , `type` , `source` , `hostname` , `changed` , `created` , `uid` , `comment` , `commentcount` , `subject` , `body` , `access` , `visitcount` ,`htmltag`)  ".
							" VALUES ('".$rootpid."','0' , '0', '".addslashes($rows["source"])."', '".addslashes($rows["hostname"])."',NOW( ) , '".$rows["created"]."', '".$pc["UID"]."', '".$rows["comment"]."', '0', '".addslashes($rows["subject"])."', '".addslashes($rows["body"])."', '".$target."', '0','".$rows["htmltag"]."');";
						mysql_query($query,$link);
					}
				}	
				if($access == 0 && $act == "cut")
					pc_update_record($link,$pc["UID"]," - ".$j);
				if($target == 0)
					pc_update_record($link,$pc["UID"]," + ".$j);
			}
			$log_action = "CUT/COPY NODE";
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=".$access."&tid=".intval($_GET["tid"]));
		}
		elseif($act == "post")
		{
			if($subject)
			{
				if($pc["EDITOR"]==2)//use ubb
					$blogbody = pc_ubb_parse($_POST["blogbody"]);
				else
					$blogbody = $_POST["blogbody"];
				
				if ($pcconfig["ENCODINGTBP"]) 
				    $convert_encoding = @$_POST['tbpencoding'];
				else
				    $convert_encoding = '';
				    
				$ret = @pc_add_node($link,$pc,$_GET["pid"],$_POST["tid"],$_POST["emote"],$_POST["comment"],$_GET["tag"],$_POST["htmltag"],$_POST["trackback"],$_POST["theme"],$_POST["subject"],$blogbody,0,$_POST["autodetecttbps"],$_POST["trackbackurl"],$_POST["trackbackname"],$convert_encoding,0,0,$currentuser["userid"]);
				$error_alert = "";
				switch($ret)
				{
					case -1:
						html_error_quit("ȱ����־����");
						exit();
						break;
					case -2:
						html_error_quit("Ŀ¼������");
						exit();
						break;
					case -3:
						html_error_quit("��Ŀ¼����־���Ѵ�����");
						exit();
						break;
					case -4:
						html_error_quit("���಻����");
						exit();
						break;
					case -5:
						html_error_quit("����ϵͳԭ����־���ʧ��,����ϵ����Ա");
						exit();
						break;
					case -6:
						$error_alert = "����ϵͳ����,����ͨ�淢��ʧ��!";
						break;
					case -7:
						$error_alert = "TrackBack Ping URL ����,����ͨ�淢��ʧ��!";
						break;
					case -8:
						$error_alert = "�Է�����������Ӧ,����ͨ�淢��ʧ��!";
						break;
				    case -9:
				        $error_alert = "�������¿��ܺ��в����ʻ㣬��ȴ�����Ա��ˡ�";
				        break;
					case -10: // Ⱥ��blog�ķ�����δ�ܴ���... 
					    html_error_quit("����ϵͳԭ����־���ʧ��,����ϵ����Ա");
						exit();
						break;
					default:
				}
				
				if($error_alert)
					echo "<script language=\"javascript\">alert('".$error_alert."');</script>";
				$log_action = "ADD NODE: ".$_POST["subject"];
				pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=".intval($_GET["tag"])."&tid=".intval($_POST["tid"])."&pid=".intval($_GET["pid"]));
			}
			else
			{
				@$tid = intval($_GET["tid"]);
				@$pid = intval($_GET["pid"]);
				$tag = intval($_GET["tag"]);
				if($tag < 0 || $tag > 4)
					$tag =2 ;
				
				if($tid)
				{
					if(!pc_load_topic($link,$pc["UID"],$tid,$topicname,$tag))
					{
						html_error_quit("��ָ���ķ��಻���ڣ�������!");
						exit();
					}
				}
				if($pid)
				{
					if(!pc_load_directory($link,$pc["UID"],$pid))
					{
						html_error_quit("��ָ���ķ��಻���ڣ�������!");
						exit();
					}
				}
				
?>
<br><center>
<form name="postform" id="postform" target="_self" action="pcmanage.php?userid=<?php echo $pc["USER"]; ?>&act=post&<?php echo "tag=".$tag."&pid=".$pid; ?>" method="post" onsubmit="return submitwithcopy();">
<input type="hidden" name="tmpsave" id="tmpsave" value="0">
<table cellspacing="0" cellpadding="5" border="0" width="90%" class="t1">
<tr>
	<td class="t2">��������</td>
</tr>
<tr>
	<td class="t8">����
	<input type="text" size="100" maxlength="200" name="subject" class="f1" value="<?php echo $subject; ?>">
	</td>
</tr>
<tr>
	<td class="t5">
	����
	<input type="radio" name="comment" value="1" checked class="f1">����
	<input type="radio" name="comment" value="0" class="f1">������
	</td>
</tr>
<tr>
	<td class="t8">
	Blog
	<select name="tid" class="f1">
<?php
		$blogs = pc_blog_menu($link,$pc,$tag);
		for($i = 0 ; $i < count($blogs) ; $i ++)
		{
			if($blogs[$i]["TID"] == $tid )
				echo "<option value=\"".$blogs[$i]["TID"]."\" selected>".html_format($blogs[$i]["NAME"])."</option>";
			else
				echo "<option value=\"".$blogs[$i]["TID"]."\">".html_format($blogs[$i]["NAME"])."</option>";
		}
?>
	</select>
	&nbsp;&nbsp;&nbsp;
	����
	<select name="theme" class="f1">
<?php
    while (list ($key,$val) = each ($pcconfig["SECTION"])) {
        if ($key == $pc["THEM"][0])
            echo "<option value=\"".$key."\" selected>".html_format($val)."</option>";
        else
            echo "<option value=\"".$key."\">".html_format($val)."</option>";
    }
?>	
	</select> 
	</td>
</tr>
<tr>
	<td class="t13">�������</td>
</tr>
<tr>
	<td class="t5"><?php @require("emote.html"); ?></td>
</tr>
<tr>
	<td class="t11">����
	<input type="checkbox" name="htmltag" value=1 <?php if($pc["EDITOR"] != 0) echo "checked"; ?>>ʹ��HTML���
	</td>
</tr>
<tr>
	<td class="t8">
<?php
	if($pc["EDITOR"]!=2)// not use ubb
	{
		$blogbody = isset($_POST["blogbody"])?$_POST["blogbody"]:"";
?>	
	<textarea name="blogbody" class="f1" style="width:100%" rows="30" id="blogbody" wrap="physical"><?php echo $pcconfig["EDITORALERT"].$blogbody; ?></textarea>
<?php
	}
	else
		pc_ubb_content();
?>
	</td>
</tr>
<?php
	if($tag == 0)
	{
?>
<tr>
	<td class="t8">
	����ͨ��&nbsp;&nbsp;&nbsp;&nbsp;
<?php
    if ($pcconfig["ENCODINGTBP"]) { //֧��ѡ��ͬ�ķ��ͱ���
        echo 'ʹ��<select name="tbpencoding" class="f1">';
        $encodings = explode(',',$support_encodings);
        for ($i = 0 ; $i < sizeof($encodings) ; $i ++) {
            if ($encodings[$i] == $sending_encoding)
                echo '<option value="'.$encodings[$i].'" selected>'.htmlspecialchars($encodings[$i]).'</option>';
            else
                echo '<option value="'.$encodings[$i].'">'.htmlspecialchars($encodings[$i]).'</option>';
        }
        echo '</select>���뷢������ͨ��';
    }
?>
	</td>
</tr>
<tr>
	<td class="t8">
	<input type="checkbox" name="autodetecttbps" value="1">�Զ���������ͨ��
	(ʲô���Զ���������ͨ��?)<br />
	��������: <input type="text" size="80" maxlength="255" name="trackbackname" class="f1" value="<?php echo htmlspecialchars(@$_GET["tbArtAddr"]); ?>"><br />
	Trackback Ping URL: <input type="text" size="80" maxlength="255" name="trackbackurl" value="<?php echo htmlspecialchars(@$_GET["tbTBP"]); ?>" class="f1">
	(������"http://"��ͷ)
	</td>
</tr>
<tr>
	<td class="t5">
	<input type="checkbox" name="trackback" value="1">��������
	(��ע�⣬���ѡ����������������վ�ڲ�����ͬ�������������� blog �����������ӵģ��ܿ��ܴ�������������Ϣ��)
	</td>
</tr>
<?php
	}
?>
<tr>
	<td class="t2">
		<input type="button" name="ins" value="����HTML" class="b1" onclick="return insertHTML();" />
		<input type="button" name="hil" value="����" class="b1" onclick="return highlight();" />
		<input type="submit" name="postbutton" id="postbutton" value="������" class="b1">
		<input type="button" value="������ҳ" onclick="doCancel();" class="b1">
	</td>
</tr>
</table>
</form></center>
<?php				
			}
		}
		elseif($act == "edit")
		{
			$nid = (int)($_GET["nid"]);
			$query = "SELECT `theme`,`nodetype` , `subject` , `body` ,`comment`,`type`,`tid`,`access`,`htmltag`,`trackback`,`pid` FROM nodes WHERE `nid` = '".$nid."' AND `uid` = '".$pc["UID"]."' LIMIT 0 , 1 ;";
			$result = mysql_query($query,$link);
			$rows = mysql_fetch_array($result);
			mysql_free_result($result);
			if(!$rows)
			{
				html_error_quit("���²�����!");
				exit();
			}
			/*
			if($rows[nodetype] != 0)
			{
				html_error_quit("���Ĳ��ɱ༭!");
				exit();
			}
			*/
			if($subject)
			{
				if($_POST["comment"]==1)
					$c = 0;
				else
					$c = 1;
				$useHtmlTag = ($_POST["htmltag"]==1)?1:0;
				$trackback = ($_POST["trackback"]==1)?1:0;
				$emote = (int)($_POST["emote"]);
				$query = "UPDATE nodes SET `theme` = '".addslashes($_POST["theme"])."'  , `subject` = '".addslashes($_POST["subject"])."' , `body` = '".addslashes(html_editorstr_format($_POST["blogbody"]))."' , `changed` = '".date("YmdHis")."' , `comment` = '".$c."' , `tid` = '".(int)($_POST["tid"])."' , `emote` = '".$emote."' , `htmltag` = '".$useHtmlTag."' , `trackback` = '".$trackback."' WHERE `nid` = '".$nid."' AND nodetype = 0;";
				mysql_query($query,$link);
				pc_update_record($link,$pc["UID"]);
				if($rows["subject"]==$_POST["subject"])
					$log_action = "EDIT NODE: ".$rows[subject];
				else
				{
					$log_action = "EDIT NODE: ".$_POST["subject"];
					$log_content = "OLD SUBJECT: ".$rows["subject"]."\nNEW SUBJECT: ".$_POST["subject"];
				}
				if($rows["type"]==1)
					pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=3&pid=".$rows["pid"]);
				else
					pc_return("pccon.php?id=".$pc["UID"]."&nid=".$nid);
			}
			else
			{
?>
<br><center>			
<form name="postform" id="postform" action="pcmanage.php?userid=<?php echo $pc["USER"]; ?>&act=edit&nid=<?php echo $nid; ?>" method="post" onsubmit="return submitwithcopy();">
<table cellspacing="0" cellpadding="5" border="0" width="90%" class="t1">
<?php
		if($rows["type"]==1)
		{
?>
<tr>
	<td class="t2">�޸�Ŀ¼</td>
</tr>
<tr>
	<td class="t8">
	����
	<input type="text" size="100" class="f1" maxlength="200" name="subject" value="<?php echo htmlspecialchars(stripslashes($rows["subject"])); ?>">
	</td>
</tr>
<tr>
	<td class="t2">
		<input type="submit" value="�޸�Ŀ¼" class="b1">
		<input type="button" value="������ҳ" class="b1" onclick="doCancel();">
	</td>
</tr>
<?php
		}
		else
		{
?>
<tr>
	<td class="t2">�޸�����</td>
</tr>
<tr>
	<td class="t8">����
	<input type="text" size="100" class="f1" name="subject" value="<?php echo htmlspecialchars($rows["subject"]); ?>">
	</td>
</tr>
<tr>
	<td class="t5">
	����
	<input type="radio" name="comment" class="f1" value="0" <?php if($rows["comment"]!=0) echo "checked"; ?>>����
	<input type="radio" name="comment" class="f1" value="1" <?php if($rows["comment"]==0) echo "checked"; ?>>������
	</td>
</tr>
<tr>
	<td class="t8">
	Blog
	<select name="tid" class="f1">
<?php
		$blogs = pc_blog_menu($link,$pc,$rows["access"]);
		for($i = 0 ; $i < count($blogs) ; $i ++)
		{
			if($blogs[$i]["TID"] == $rows["tid"])
				echo "<option value=\"".$blogs[$i]["TID"]."\" selected>".html_format($blogs[$i]["NAME"])."</option>";
			else
				echo "<option value=\"".$blogs[$i]["TID"]."\" >".html_format($blogs[$i]["NAME"])."</option>";
		}
?>
	</select>
	&nbsp;&nbsp;&nbsp;����
	<select name="theme" class="f1">
<?php
    while (list ($key,$val) = each ($pcconfig["SECTION"])) {
        if ($key == $rows["theme"])
            echo "<option value=\"".$key."\" selected>".html_format($val)."</option>";
        else
            echo "<option value=\"".$key."\">".html_format($val)."</option>";
    }
?>	
	</td>
</tr>
<tr>
	<td class="t13">�������</td>
</tr>
<tr>
	<td class="t5"><?php @require("emote.html"); ?></td>
</tr>
<tr>
	<td class="t11">����
	<input type="checkbox" name="htmltag" value=1 <?php if(strstr($rows["body"],$pcconfig["NOWRAPSTR"]) || $rows["htmltag"] == 1) echo "checked"; ?> >ʹ��HTML���
	</td>
</tr>
<tr>
	<td class="t8">
	<textarea name="blogbody" class="f1" style="width:100%" rows="30" id="blogbody" wrap="physical"><?php echo $pcconfig["EDITORALERT"]; ?><?php echo htmlspecialchars($rows["body"]); ?></textarea>
	</td>
</tr>
<tr>
	<td class="t5">
	��������
	<input type="checkbox" name="trackback" value="1" <?php if($rows["trackback"]==1) echo "checked"; ?>>
	</td>
</tr>
<tr>
	<td class="t2">
		<input type="button" name="ins" value="����HTML" class="b1" onclick="return insertHTML();" />
		<input type="button" name="hil" value="����" class="b1" onclick="return highlight();" />
		<input type="submit" name="postbutton" id="postbutton" value="�޸ı���" class="b1">
		<input type="button" value="������ҳ" onclick="doCancel();" class="b1">
	</td>
</tr>
<?php
		}
?>
</table>
</form></center>
<?php				
			}
		}
		elseif($act == "del")
		{
			$nid = (int)($_GET["nid"]);	
			$query = "SELECT `tid`,`pid`,`access`,`type`,`nodetype`,`subject` FROM nodes WHERE `uid` = '".$pc["UID"]."' AND `nid` = '".$nid."' ;";
			$result = mysql_query($query,$link);
			$rows = mysql_fetch_array($result);
			mysql_free_result($result);
			if(!$rows)
			{
				html_error_quit("���²�����!");
				exit();
			}
			/*
			if($rows[nodetype]!=0)
			{
				html_error_quit("���Ĳ���ɾ��!");
				exit();
			}
			*/
			if($rows["access"] == 4)
			{
				//����ɾ��	
				$query = "DELETE FROM nodes WHERE `nid` = '".$nid."' ";
				mysql_query($query,$link);
				$query = "DELETE FROM comments WHERE `nid` = '".$nid."' ";
				mysql_query($query,$link);
				$query = "DELETE FROM trackback WHERE `nid` = '".$nid."' ";
				mysql_query($query,$link);
				$log_action = "DEL NODE: ".$rows["subject"];
			}
			else
			{
				if($rows["type"] == 1)
				{
					$query = "SELECT `nid` FROM nodes WHERE `pid` = '".$nid."' LIMIT 0, 1 ;";
					$result = mysql_query($query);
					if($rows0 = mysql_fetch_array($result))
					{
						mysql_free_result($result);
						html_error_quit("����ɾ����Ŀ¼�µ�����!");
						exit();
					}
					mysql_free_result($result);
					$query = "DELETE FROM nodes WHERE `nid` = '".$nid."' ;";
					mysql_query($query,$link);
					$log_action = "DEL DIR: ".$rows["subject"];
				}
				else
				{
					$query = "UPDATE nodes SET `access` = '4' , `changed` = '".date("YmdHis")."' , `tid` = '0' WHERE `nid` = '".$nid."' ;";
					mysql_query($query,$link);
					$log_action = "DEL TO JUNK: ".$rows["subject"];
					if($rows["access"] == 0)
						pc_update_record($link,$pc["UID"]," - 1");
				}
			}
			pc_update_record($link,$pc["UID"]);
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=".$rows["access"]."&tid=".$rows["tid"]."&pid=".$rows["pid"]);	
		}
		elseif($act == "clear")
		{
			$query = "SELECT `nid` FROM nodes WHERE `uid` = '".$pc["UID"]."' AND `access` = '4' ;";	
			$result = mysql_query($query,$link);
			$query = "DELETE FROM comments WHERE `nid` = '0' ";
			$query_tb = "DELETE FROM trackback WHERE `nid` = '0' ";
			while($rows = mysql_fetch_array($result))
			{
				$query.= "  OR `nid` = '".$rows["nid"]."' ";	
				$query_tb.= "  OR `nid` = '".$rows["nid"]."' ";	
			}
			mysql_query($query,$link);
			mysql_query($query_tb,$link);
			$query = "DELETE FROM nodes WHERE `uid` = '".$pc["UID"]."' AND `access` = '4' ;";
			mysql_query($query,$link);
			$log_action = "EMPTY JUNK";
			pc_update_record($link,$pc["UID"]);
			pc_return($_GET["ret"]);		
		}
		elseif($act == "tedit")
		{
			$tid = pc_load_topic($link,$pc["UID"],intval($_GET["tid"]),$topicname);
			if(!$tid)
			{
				html_error_quit("Blog������!");
				exit();
			}
			if(@$_POST["topicname"])
			{
				pc_edit_topics($link,$tid,$_POST["topicname"]);
				$log_action = "UPDATE TOPIC: ".$_POST["topicname"];
				pc_update_record($link,$pc["UID"]);
				pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=6");		
			}
			else
			{
				$sec = array("������","������","˽����");
?>
<br>
<center>
<form action="pcmanage.php?userid=<?php echo $pc["USER"]; ?>&act=tedit&tid=<?php echo $tid; ?>" method="post" onsubmit="if(this.topicname.value==''){alert('������Blog����!');return false;}">
<table cellspacing="0" cellpadding="5" border="0" width="90%" class="t1">
<tr>
	<td class="t2">�޸�Blog</td>
</tr>
<tr>
	<td class="t8">
	Blog��
	<input type="text" class="f1" style="width:300px;" name="topicname" value="<?php echo htmlspecialchars(stripslashes($topicname)); ?>">
	</td>
</tr>
<tr>
	<td class="t2">
	<input type="submit" value="�޸�Blog" class="b1">
	<input type="button" value="������ҳ" class="b1" onclick="doCancel();">
	</td>
</tr>
</table>
</form></center>
<?php
			}
		}
		elseif($act == "tdel")
		{
			$tid = pc_load_topic($link,$pc["UID"],intval($_GET["tid"]),$topicname);
			if(!$tid)
			{
				html_error_quit("Blog������!");
				exit();
			}
			$ret = pc_del_topics($link,$tid);
			if($ret==-1)
			{
				html_error_quit("����ɾ���÷������������!");
				exit();
			}
			if($ret!=0)
			{
				html_error_quit("ɾ��ʧ��,����ϵ����Ա!");
				exit();
			}
			pc_update_record($link,$pc["UID"]);
			$log_action = "DEL TOPIC: ".$topicname;
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=6");				
		}
		elseif($act == "tadd" && $_POST["topicname"])
		{
			if(!pc_add_topic($link,$pc,$_POST["access"],$_POST["topicname"]))
			{
				html_error_quit("�������ʧ��");
				exit();
			}
			$log_action = "ADD TOPIC: ".$_POST["topicname"];
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=6");	
		}
		elseif($act == "sedit" && $_POST["pcname"])
		{
			$favmode = (int)($_POST["pcfavmode"]);
			if($favmode != 1 && $favmode != 2)
				$favmode = 0;
			$tmpsave = ($_POST["pctmpsave"]==0)?0:1;
			$query = "UPDATE `users` SET `createtime` = `createtime` , `corpusname` = '".addslashes(undo_html_format($_POST["pcname"]))."',`description` = '".addslashes(undo_html_format($_POST["pcdesc"]))."',`theme` = '".addslashes(undo_html_format($_POST["pcthem"]))."' , `backimage` = '".addslashes(undo_html_format($_POST["pcbkimg"]))."' , `logoimage` = '".addslashes(undo_html_format($_POST["pclogo"]))."' , `htmleditor` = '".(int)($_POST["htmleditor"])."', `style` = '".(int)($_POST["template"])."' , `indexnodechars` = '".(int)($_POST["indexnodechars"])."' , `indexnodes` = '".(int)($_POST["indexnodes"])."' , `favmode` = '".$favmode."' , `useremail` = '".addslashes(trim($_POST["pcuseremail"]))."' , `userinfor` = '".addslashes(trim($_POST["userinfor"]))."' , `defaulttopic` = '".addslashes(trim($_POST["pcdefaulttopic"]))."'";
			if(defined("_BLOG_ANONY_COMMENT_"))
				$query .= " , `anonycomment` = " . (($_POST["anonycomment"]=="yes")?"1":"0");
			$query .= " WHERE `uid` = '".$pc["UID"]."';";	
			mysql_query($query,$link);
			
			$log_action = "UPDATE SETTINGS";
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=7");	
			
		}
		elseif($act == "adddir" && $_POST["dir"])
		{
			$ret = pc_add_favdir($link,$pc,$_POST["pid"],$_POST["dir"]);
			switch($ret)
			{
				case -1:
					html_error_quit("ȱ��Blog��Ϣ!");
					exit();
				case -2:
					html_error_quit("ȱ�ٸ�Ŀ¼ID!");
					exit();
				case -3:
					html_error_quit("ȱ��Ŀ¼��!");
					exit();
				case -4:
					html_error_quit("��Ŀ¼��Ŀ¼���Ѵ�����!");
					exit();
				case -5:
					html_error_quit("ϵͳ����,����ϵ����Ա!");
					exit();
				default:	
			}
			pc_update_record($link,$pc["UID"]);
			$log_action = "ADD DIR: ".$_POST["dir"];
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=3&pid=".intval($_POST["pid"]));
		}
		elseif($act == "favcut" || $act == "favcopy")
		{
			//Ŀǰ��֧��Ŀ¼�ļ��к͸���
			$query = "SELECT `nid`,`type`,`pid`,`subject`,`tid` FROM nodes WHERE `nid` = '".(int)($_GET["nid"])."' AND `uid` = '".$pc["UID"]."' AND `access` = 3  AND `type` = 0 LIMIT 0 , 1;";
			$result = mysql_query($query,$link);
			$rows = mysql_fetch_array($result);
			if(!$rows)
			{
				pc_html_init("gb2312",stripslashes($pc["NAME"]));
				html_error_quit("���²�����!");
				exit();
			}
			mysql_free_result($result);
			setcookie("BLOGFAVACTION",$act);
			setcookie("BLOGFAVNID",$rows["nid"]);
			
			pc_html_init("gb2312",stripslashes($pc["NAME"]));
?>
<script language="javascript">
alert("�ѽ� <?php echo htmlspecialchars($rows[subject]); ?> ������а�!");
</script>
<?php			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=3&pid=".$rows[pid]);
			
		}
		elseif($act == "favpaste")
		{
			if(!$_COOKIE["BLOGFAVACTION"])
			{
				pc_html_init("gb2312",stripslashes($pc["NAME"]));
				html_error_quit("���ļ������ǿյģ����ȼ��л��߸���һ���ļ�!");
				exit();
			}
			$pid = intval($_GET["pid"]);
			if(!pc_load_directory($link,$pc["UID"],$pid))
			{
				pc_html_init("gb2312",stripslashes($pc["NAME"]));
				html_error_quit("Ŀ���ļ��в�����!");
				exit();
			}
			
			if(pc_file_num($link,$pc["UID"],$pid)+1 > $pc["NLIM"])
			{
				pc_html_init("gb2312",stripslashes($pc["NAME"]));
				html_error_quit("Ŀ���ļ����е��ļ����Ѵ����� ".$pc["NLIM"]. " ��!");
				exit();
			}
			
			if(intval($_COOKIE["BLOGFAVNID"]))
			{
				if($_COOKIE["BLOGFAVACTION"] == "favcut")
				{
					$query = "UPDATE nodes SET `pid` = '".$pid."' WHERE `nid` = '".intval($_COOKIE["BLOGFAVNID"])."';";
				}
				elseif($_COOKIE["BLOGFAVACTION"] == "favcopy")
				{
					$query = "SELECT * FROM nodes WHERE `nid` = '".intval($_COOKIE["BLOGFAVNID"])."' LIMIT 0 , 1 ;";
					$result = mysql_query($query,$link);
					$rows = mysql_fetch_array($result);
					mysql_free_result($result);
					$query = "INSERT INTO `nodes` ( `nid` , `pid` , `type` , `source` , `hostname` , `changed` , `created` , `uid` , `comment` , `commentcount` , `subject` , `body` , `access` , `visitcount` , `tid` , `emote` ,`htmltag`) ".
						"VALUES ('', '".$pid."', '0', '".addslashes($rows["source"])."', '".addslashes($rows["hostname"])."', NOW( ) , '".addslashes($rows["created"])."', '".$pc["UID"]."', '".intval($rows["comment"])."', '".intval($rows["commentcount"])."', '".addslashes($rows["subject"])."', '".addslashes($rows["body"])."', '3', '".intval($rows["visitcount"])."', '".intval($rows["tid"])."', '".intval($rows["emote"])."','".intval($rows["htmltag"])."');";
				}
				mysql_query($query,$link);
			}
			setcookie("BLOGFAVACTION");
			setcookie("BLOGFAVNID");
			
			pc_html_init("gb2312",stripslashes($pc["NAME"]));
			pc_update_record($link,$pc["UID"]);
			$log_action = "CUT/COPY FAV";
			pc_return("pcdoc.php?userid=".$pc["USER"]."&tag=3&pid=".$pid);	
		}
	
		if(pc_is_groupwork($pc))
			pc_group_logs($link,$pc,$log_action,$log_content);
		
		html_normal_quit();
	}
	
?>
