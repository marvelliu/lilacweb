<?php
/*
**  Ϊˮľ�廪blog���붨�Ƶ������
**  ������������������ύ��$pcconfig["APPBOARD"]
**  �������ݱ�newapply�б�����������
**  @windinsn Mar 12 , 2004
*/
require("pcfuncs.php");

/*
** management�ֶΣ�0ͨ����1������2��ͨ����3�Ժ󲻵�����
*/
function pc_app_have_applied($link , $currentuser)
{
	$query = "SELECT management FROM newapply WHERE username = '".$currentuser["userid"]."' AND management != 2 LIMIT 0 , 1;";
	$result = mysql_query($query,$link);
	$rows = mysql_fetch_array($result);
	mysql_free_result($result);
	if( $rows) 
		return $rows[management];
	else
		return FALSE;
}

if ($loginok != 1)
	html_nologin();
elseif(!strcmp($currentuser["userid"],"guest"))
{
	html_init("gb2312");
	html_error_quit("���¼���ٽ���Blog����!");
	exit();
}
else
{
	if( time() - $currentuser["firstlogin"] < intval( $pcconfig["MINREGTIME"] * 2592000 ) )
	{
		html_init("gb2312");
		html_error_quit("�Բ�������ע��ʱ���в���".$pcconfig["MINREGTIME"]."����");
		exit();	
	}
	if( !$_POST["appname"] || !$_POST["appself"] || !$_POST["appdirect"] )
	{
		html_init("gb2312");
		html_error_quit("�Բ�������ϸ��дBlog�����");
		exit();	
	}
	
	$link = pc_db_connect();
	if( pc_load_infor($link,$currentuser["userid"]) )
	{
		pc_db_close($link);
		html_init("gb2312");
		html_error_quit("�Բ������Ѿ�ӵ��Blog��");
		exit();	
	}
	
	$appstate = pc_app_have_applied($link , $currentuser);
	if($appstate === 0 )
	{
		pc_db_close($link);
		html_init("gb2312");
		html_error_quit("�Բ�������Blog������ͨ��");
		exit();	
	}
	elseif($appstate == 1 )
	{
		pc_db_close($link);
		html_init("gb2312");
		html_error_quit("�Բ�������Blog���뻹�ڴ����У������ĵȺ�");
		exit();	
	}
	elseif($appstate == 3 )
	{
		pc_db_close($link);
		html_init("gb2312");
		html_error_quit("�Բ��������ܽ���Blog���룬����ϵ����Ա");
		exit();	
	}
	
	
	$apptitle = "[����] ".$currentuser["userid"]." ���뽨��ˮľBLOG";
	$appbody  = "(1) BLOG���ƣ�".$_POST["appname"]."\n\n\n".
		    "(2) ������ ID ����Ҫ���ҽ���\n".
		    "    ID��".$currentuser["userid"]."\n".
		 //   "    ע��ʱ�䣺".date("Y��m��d��",$currentuser["firstlogin"])."\n".
		    "        \n".$_POST["appself"]."\n\n\n".
		    "(3) ��Ӫ����(����������Blog�ĳ����滮)\n        ".$_POST["appdirect"]."\n\n";
	
	$appself =  "ID��".$currentuser["userid"]."\n".
		   // "ע��ʱ�䣺".date("Y��m��d��",$currentuser["firstlogin"])."\n".
		    "���ҽ��ܣ�\n".$_POST["appself"];
	$query = "INSERT INTO `newapply` ( `naid` , `username` , `appname` , `appself` , `appdirect` , `hostname` , `apptime` , `manager` , `management` ) ".
	 	 "VALUES ('', '".$currentuser["userid"]."', '".addslashes($_POST["appname"])."', '".addslashes($appself)."', '".addslashes($_POST["appdirect"])."', '".addslashes($_SERVER["REMOTE_ADDR"])."', NOW( ) , NULL , '1');";
	if(!mysql_query($query,$link))
	{
		pc_db_close($link);
		html_init("gb2312");
		html_error_quit("ʮ�ֱ�Ǹ������ϵͳԭ������������δ�Ǽ���⡣��������дһ��");
		exit();	
	}
	
	pc_db_close($link);
	
	$ret = bbs_postarticle($pcconfig["APPBOARD"], preg_replace("/\\\(['|\"|\\\])/","$1",$apptitle), preg_replace("/\\\(['|\"|\\\])/","$1",$appbody), 0 , 0 , 0 , 0);
	switch ($ret) {
			case -1:
				html_error_quit("���������������!");
				break;
			case -2: 
				html_error_quit("����Ϊ����Ŀ¼��!");
				break;
			case -3: 
				html_error_quit("����Ϊ��!");
				break;
			case -4: 
				html_error_quit("����������Ψ����, ����������Ȩ���ڴ˷�������!");
				break;		
			case -5:	
				html_error_quit("�ܱ�Ǹ, �㱻������Աֹͣ�˱����postȨ��!");
				break;	
			case -6:
				html_error_quit("���η��ļ������,����Ϣ��������!");	
				break;
			case -7: 
				html_error_quit("�޷���ȡ�����ļ�! ��֪ͨվ����Ա, лл! ");
				break;
			case -8:
				html_error_quit("���Ĳ��ɻظ�!");
				break;
			case -9:
				html_error_quit("ϵͳ�ڲ�����, ��Ѹ��֪ͨվ����Ա, лл!");
				break;
            case -21:
                html_error_quit("���Ļ��ֲ����ϵ�ǰ���������趨, ��ʱ�޷��ڵ�ǰ��������������...");
                break;
		}
	pc_html_init("gb2312","Blog����");
?>
<table cellpadding=3 cellspacing=1 align=center class=TableBorder1>
<tr align=center><th width="100%">�����ύ�ɹ���</td>
</tr><tr><td width="100%" class=TableBody1>
����BLOG�����Ѿ��ύ�ɹ�������Ա���������ڴ����������롣<br/><br/>
��ҳ�潫��3����Զ��л���Blog��̳<meta HTTP-EQUIV=REFRESH CONTENT='3; URL=/bbsdoc.php?board=<?php echo $pcconfig["BOARD"]; ?>' >��<b>������ѡ�����²�����</b><br><ul>
<li><a href="/mainpage.php">������ҳ</a></li>
<li><a href="/pc/index.html">����Blog��ҳ</a></li>
<li><a href="/bbsdoc.php?board=<?php echo $pcconfig["BOARD"]; ?>">����Blog��̳</a></li>
<li><a href="/bbsdoc.php?board=<?php echo $pcconfig["APPBOARD"]; ?>">����Blog�������</a></li>
</ul></td></tr></table>
<p align="center">
<?php
    /**
     *    ˮľ��web����bbslib��cgi�������޸���NJUWWWBBS-0.9���˲���
     * ������ѭԭ�е�nju www bbs�İ�Ȩ������GPL����php���ֵĴ��루
     * phplib�Լ�phpҳ�棩������ѭGPL�����ڿ���ʹ����������Դ��İ�
     * Ȩ������BSD����MPL֮�ࣩ��
     *
     *   ϣ��ʹ��ˮľ�����Webվ�����powered by kbs��ͼ��.��ͼ��
     * λ��html/images/poweredby.gifĿ¼,����ָ��http://dev.kcn.cn
     * ʹ��ˮľ�����վ�����ͨ��dev.kcn.cn��ô����������Ϣ.
     *
     */
    powered_by_smth();
?>
</p>
<?php	
	html_normal_quit();
}
	
?>
