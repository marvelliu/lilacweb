<?php
require("pcadmin_inc.php");
pc_admin_check_permission();
$link = pc_db_connect();
$fid = intval($_GET["fid"]);
$query = 'SELECT * FROM filter WHERE fid = '.$fid.  ' LIMIT 1;';
$result = mysql_query($query);
$node = mysql_fetch_array($result);
if (!$node)
    html_error_quit("���²�����");
if (strtolower($_GET['filter'])=='n') {
    if ($node[state]==0 || $node[state]==2) {
        $query = 'UPDATE filter SET state = 1 WHERE fid = '.$fid.' LIMIT 1;';
        mysql_query($query);
        $pc = pc_load_infor($link,"",$node[uid]);
        
        if ($node[nid])//���˵�������
            $ret = pc_add_comment($link,$pc,$node[nid],$node[emote],$node[username],$node[subject],$node[body],$node[htmltag],true,$node[hostname]);
        else
            $ret = pc_add_node($link,$pc,$node[pid],$node[tid],$node[emote],$node[comment],$node[access],$node[htmltag],$node[trackback],$node[theme],$node[subject],$node[body],$node[nodetype],$node[auto_tbp],$node[tbp_url],$node[tbp_art],$node[tbpencoding],true,$node[hostname],$node[publisher]);
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
					case -10:
						$error_alert = "����ϵͳ����,����ͨ�淢��ʧ��!";
						break;
					default:
				}
    }    
}

if (strtolower($_GET['filter'])=='y') {
    if ($node[state]==0 || $node[state]==1) {
        $query = 'UPDATE filter SET state = 2 WHERE fid = '.$fid.' LIMIT 1;';
        mysql_query($query);
    }
}
if (strtolower($_GET['filter'])=='r') {
    if ($node[state]==4) {
    	$nid = $node[nid];
    	$query = 'SELECT * FROM nodes WHERE nid = ' . $nid . ' LIMIT 1;';
		$result = mysql_query($query);
		$node_o = mysql_fetch_array($result);
		if (!$node_o)
    		html_error_quit("�����Ѿ�������ɾ��");
    	if (($node_o[body] != '@ @ �����ѱ�����Ա���� @ @') && (!isset($_GET["or"])))
    		html_error_quit("ԭ���Ѿ����޸Ĺ�<br/><a href='pcadmin_flt.php?filter=r&or=1&fid=".$fid."'>ǿ�лָ�</a>");
    	$body = addslashes($node[body]);
    	$query = "UPDATE nodes SET `body` = '".$body."', `changed`='".$node[changed]."' where `nid` = " . $nid . ";";
		if (!mysql_query($query)) {
			html_error_quit("�޸�ԭ�ĳ���");
		}
		pc_update_record($link,$node[uid]);
		$query = "DELETE FROM filter WHERE `fid` = '".$fid."' ";
		if (!mysql_query($query)) {
			html_error_quit("�ӹ��˱�ɾ���������������Ѿ��ָ�...");
		}
    }
}


pc_db_close($link);
pc_return("pcdoc.php?userid=_filter&tag=".$node[state]);
?>
