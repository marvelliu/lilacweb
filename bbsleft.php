<?php
	require("www2-funcs.php");
	login_init();
	
	function display_board_list()
	{
?>
<div class="b1">
<?php
		for($i = 0; $i < BBS_SECNUM; $i++)
		{
?>
<a href="javascript:submenu(0,0,<?php echo $i; ?>,0,0)" target="_self">
<img id="submenuimg_brd_<?php echo $i; ?>_0" src="images/close.gif" class="pm" alt="+"
></a><a href="bbsboa.php?group=<?php echo $i; ?>"><script type="text/javascript">putImage('kfolder1.gif','class="s16x16"');</script><?php echo constant("BBS_SECNAME".$i."_0"); ?></a><br/>
<div id="submenu_brd_<?php echo $i; ?>_0" class="lineback"></div>
<?php
		}
?>
<img src="images/open.gif" class="pm" alt="-"
><a href="bbsxmlbrd.php?flag=2"><script type="text/javascript">putImage('kfolder1.gif','class="s16x16"');</script>�¿�������</a>
</div>
<?php
	}
	
	function display_my_favorite()
	{
?>
<div class="b1">
<?php
		$select = 0; 

		if( bbs_load_favboard($select)!=-1 && $boards = bbs_fav_boards($select, 1)) 
		{
			$brd_name = $boards["NAME"]; // Ӣ����
			$brd_desc = $boards["DESC"]; // ��������
			$brd_flag = $boards["FLAG"]; 
			$brd_bid = $boards["BID"];  //�� ID ���� fav dir ������ֵ 
			$rows = sizeof($brd_name);
			
			for ($j = 0; $j < $rows; $j++)
			{
				if( $brd_bid[$j] == -1) { //���ղ�Ŀ¼
					echo "-��Ŀ¼-";
				}
				else if ($brd_flag[$j]==-1)
				{
?>
<div class="fi">
<a href="javascript:submenu(1,<?php echo $brd_bid[$j]; ?>,0,0,0)" target="_self">
<img id="submenuimg_fav_<?php echo $brd_bid[$j]; ?>" src="images/close.gif" class="pm" alt="+"
></a><a href="bbsfav.php?select=<?php echo $brd_bid[$j]; ?>&up=-1"><script type="text/javascript">putImage('kfolder1.gif','class="s16x16"');</script><?php echo $brd_desc[$j]; ?></a></div>
<div id="submenu_fav_<?php echo $brd_bid[$j]; ?>" class="lineback"></div>
<?php
				}
				else
				{
					$brd_link="bbsdoc.php?board=" . urlencode($brd_name[$j]);

					if( $j != $rows - 1 )
						echo "<div class='lb'><div class='mi'><a href='".$brd_link."'>".$brd_desc[$j]."</a></div></div>";
					else
						echo "<div class='lmi'><a href='".$brd_link."'>".$brd_desc[$j]."</a></div>";
				}
			}
		}
?>
</div>
<?php 
	}
	
	function display_mail_menu($userid)
	{
?>
<div class="mi"><a href="bbsnewmail.php">�������ʼ�</a></div>
<div class="mi"><a href="bbsmailbox.php?path=.DIR&title=<?php echo rawurlencode("�ռ���"); ?>">�ռ���</a></div>
<div class="mi"><a href="bbsmailbox.php?path=.SENT&title=<?php echo rawurlencode("������"); ?>">������</a></div>
<div class="mi"><a href="bbsmailbox.php?path=.DELETED&title=<?php echo rawurlencode("������"); ?>">������</a></div>
<?php
		//custom mailboxs
		$mail_cusbox = bbs_loadmaillist($userid);
		if ($mail_cusbox != -1)
		{
			foreach ($mail_cusbox as $mailbox)
			{
				echo "<div class=\"mi\">".
					"<a href=\"bbsmailbox.php?path=".$mailbox["pathname"]."&title=".urlencode($mailbox["boxname"])."\">".htmlspecialchars($mailbox["boxname"])."</a></div>\n";
			}
		}
?>
<div class="lmi"><a href="bbspstmail.php">�����ʼ�</a></div>
<?php		
	}
		
	function display_blog_menu($userid,$userfirstlogin)
	{
/*
		$db["HOST"]=bbs_sysconf_str("MYSQLBLOGHOST");
		$db["USER"]=bbs_sysconf_str("MYSQLBLOGUSER");
		$db["PASS"]=bbs_sysconf_str("MYSQLBLOGPASSWORD");
		$db["NAME"]=bbs_sysconf_str("MYSQLBLOGDATABASE");
		
		@$link = mysql_connect($db["HOST"],$db["USER"],$db["PASS"]) ;
		if (!$link) return;
		@mysql_select_db($db["NAME"],$link);
		
		$query = "SELECT `uid` FROM `users` WHERE `username` = '".$userid."' AND `createtime`  > ".date("YmdHis",$userfirstlogin)." LIMIT 0,1 ;";
		$result = mysql_query($query,$link);
		$rows = mysql_fetch_array($result);
		@mysql_free_result($result);
*/
		global $currentuser;
		$rows = ( $currentuser["flag1"] & BBS_PCORP_FLAG );

		if(!$rows)
		{
?>
<div class="mi"><a href="pc/pcapp0.html">����BLOG</a></div>
<?php
		}
		else
		{
?>
<div class="mi"><a href="pc/index.php?id=<?php echo $userid; ?>">�ҵ�Blog</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=0">������</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=1">������</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=2">˽����</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=3">�ղ���</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=4">ɾ����</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=5">���ѹ���</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=6">�������</a></div>
<div class="mi"><a href="pc/pcdoc.php?userid=<?php echo $userid; ?>&tag=7">�����趨</a></div>
<div class="mi"><a href="pc/pcfile.php?userid=<?php echo $userid; ?>">���˿ռ�</a></div>
<div class="mi"><a href="pc/pcmanage.php?userid=<?php echo $userid; ?>&act=post&tag=0&pid=0">�������</a></div>
<?php		
		}	
	}

	cache_header("nocache");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"/>
<script type="text/javascript" src="static/www2-main.js"></script>
<script type="text/javascript"><!--
	writeCssLeft();
	top.document.title = '��ӭݰ��<?php echo BBS_FULL_NAME; ?>';
//-->
</script>
<script type="text/javascript" src="static/bbsleft.js"></script>
<!--[if IE]>
<style type="text/css">
.t2,.logo {
	width: 167px;
}
</style>
<![endif]-->
<base target="f3" />
</head>
<body>
<iframe id="hiddenframe" name="hiddenframe" width="0" height="0" src="images/img.gif" frameborder="0" scrolling="no"></iframe>

<!--վ���־-->
<?php if (defined("SITE_NEWSMTH")) { ?>
<script type="text/javascript">putImage('t1.gif','class="pm"');</script>
<?php } else { ?>
<center style="padding: 0.3em;font-weight:bold;font-size:120%;"><?php echo BBS_FULL_NAME; ?></center>
<?php } ?>

<?php
		if($currentuser["userid"]=="guest")
		{
?>
<div class="t2">
<form action="bbslogin.php" method="post" name="form1" target="_top" onsubmit="return fillf3(this);" class="m0">
<nobr><script type="text/javascript">putImage('u1.gif','alt="��¼�û���" class="pm"');</script>
<input type="text" class="upinput" LENGTH="10" onmouseover="this.focus()" onfocus="this.select()" name="id" onkeypress="return input_okd(this, event);" /></nobr><br/>

<nobr><script type="text/javascript">putImage('u3.gif','alt="�û�����" class="pm"');</script>
<input type="password" class="upinput" LENGTH="10" name="passwd" maxlength="39" onkeypress="return input_okd(this, event);" /></nobr><br />
<div class="m9">
<nobr><a href="javascript:form1.submit();">
<script type="text/javascript">putImage('l1.gif','alt="��¼��վ" class="m10" onClick="form1.submit();"');</script></a>
<a href="bbsreg0.html" target="_top">
<script type="text/javascript">putImage('l3.gif','alt="ע�����û�" class="m10"');</script></a></nobr>
</div>
</form>
<?php
		}
		else
		{
?>
<div class="t2">
<nobr><script type="text/javascript">putImage('u1.gif','alt="��¼�û���" class="pm"');</script>
&nbsp;&nbsp;<?php echo $currentuser["userid"]; ?></nobr><br/>
<?php
		}
?>
</div>

<div class="b1 m4">
	<img src="images/open.gif" class="pm" alt="-"
	><a href="<?php echo MAINPAGE_FILE; ?>"><script type="text/javascript">putImage('i_main.gif','class="sfolder"');</script>��ҳ����</a><br/>
	
	<img src="images/open.gif" class="pm" alt="-"
	><a href="bbs0an.php"><script type="text/javascript">putImage('i_ann.gif','class="sfolder"');</script>������</a><br/>
	
	<a href='javascript:changemn("board");' target="_self"><img id="imgboard" src="images/close.gif" class="pm" alt="+"
	></a><a href="bbssec.php"><script type="text/javascript">putImage('i_board.gif','class="sfolder"');</script>����������</a><br/>
	<div class="pp" id="divboard">
<?php
	display_board_list();
?>
	</div>

	<img src="images/open.gif" class="pm" alt="-"><a href="bbsfav.php?x"><script type="text/javascript">putImage('i_newsec.gif','class="sfolder"');</script>�·���������</a><br />
	<div><form action="bbssel.php" method="get" class="m0"><nobr
		><img src="images/open.gif" class="pm" alt="-"><script type="text/javascript">putImage('i_search.gif','class="sfolder"');</script><input name="board" type="text" class="f2" value="����������" size="12" onmouseover="this.focus()" onfocus="this.select()" /> 
		<input name="submit" type="submit" value="GO" class="sgo" />
		</nobr>
	</form></div>
<?php
	if($currentuser["userid"]!="guest"){
?>
	<a href='javascript:changemn("fav");' target="_self"><img id="imgfav" src="images/close.gif" class="pm" alt="+"></a
	><a href="bbsfav.php?select=0"><script type="text/javascript">putImage('i_fav.gif','class="sfolder"');</script>�ҵ��ղؼ�</a><br/>

	<div class="pp" id="divfav">
<?php
		display_my_favorite();
?>
	</div>

	<img src="images/open.gif" class="pm" alt="-"
	><a href="bbssfav.php?userid=<?php echo $currentuser['userid']; ?>"><script type="text/javascript">putImage('i_sfav.gif','class="sfolder"');</script><?php echo FAVORITE_NAME; ?></a><br/>

<?php
	}
	if (defined("BBS_HAVE_BLOG"))
	{
?>
	<a href='javascript:changemn("pc");' target="_self"><img id="imgpc" src="images/close.gif" class="pm" alt="+"
	></a><a href='pc/index.html'><script type="text/javascript">putImage('i_blog.gif','class="sfolder"');</script><?php echo BBS_NICKNAME; ?>Blog</a><br/>

	<div class="pp" id="divpc">
<?php
		if($currentuser["userid"]!="guest")
			display_blog_menu($currentuser["userid"],$currentuser["firstlogin"]);
?>
		<div class="mi"><a href="pc/index.html">Blog��ҳ</a></div>
		<div class="mi"><a href="pc/pc.php">�û��б�</a></div>
		<div class="mi"><a href="pc/pcreco.php">�Ƽ�����</a></div>
		<div class="mi"><a href="pc/pclist.php">��������</a></div>
		<div class="mi"><a href="pc/pcsec.php">����Ŀ¼</a></div>
		<div class="mi"><a href="pc/pcnew.php">������־</a></div>
		<div class="mi"><a href="pc/pcnew.php?t=c">��������</a></div>
		<div class="mi"><a href="pc/pcsearch2.php">��������</a></div>
		<div class="mi"><a href="pc/pcnsearch.php">��־����</a></div>
<?php
		@include("pc/pcconf.php");
		if (isset($pcconfig["BOARD"])) {
?>
		<div class="mi"><a href="bbsdoc.php?board=<?php echo $pcconfig["BOARD"]; ?>">Blog��̳</a></div>
<?php
		}
		if ($currentuser && $currentuser["index"]) { //blog manage menu
			if (isset($pcconfig["BOARD"])) {
				$brdarr = array();
				$pcconfig["BRDNUM"] = bbs_getboard($pcconfig["BOARD"], $brdarr);
				if (bbs_is_bm($pcconfig["BRDNUM"], $currentuser["index"])) {
?>
		<div class="mi"><a href="pc/pcadmin_rec.php">Blog����</a></div>
<?php
		}}} //blog manage menu
?>
		<div class="lmi"><a href="pc/index.php?id=SYSOP">��������</a></div>
		</div>
<?php
	} // defined(BBS_HAVE_BLOG)

	if($currentuser["userid"]!="guest"){
?>
	<a href='javascript:changemn("mail");' target="_self"><img id="imgmail" src="images/close.gif" class="pm" alt="+"
	></a><a href="bbsmail.php"><script type="text/javascript">putImage('i_mail.gif','class="sfolder"');</script>�ҵ�����</a><br/>

	<div class="pp" id="divmail">
<?php
		display_mail_menu($currentuser["userid"]);
?>					
	</div>
<?php
	}
?>
	<a href='javascript:changemn("chat");' target="_self"><img id="imgchat" src="images/close.gif" class="pm" alt="+"
	><script type="text/javascript">putImage('i_talk.gif','class="sfolder"');</script≯��˵��</a><br/>
	<div class="pp" id="divchat">
<?php
	if (!defined("SITE_SMTH")) { // Smth���ṩ�����û��б� add by windinsn, May 5,2004
?>
		<div class="mi"><a href="bbsuser.php">�����û�</a></div>
<?php
	}
	if($currentuser["userid"]=="guest"){
?>					
		<div class="lmi"><a href="bbsqry.php">��ѯ����</a></div>
<?php
	}					
	else{
?>
		<div class="mi"><a href="bbsqry.php">��ѯ����</a></div>
		<div class="mi"><a href="bbsfriend.php">���ߺ���</a></div>
		<div class="mi"><a href="bbsmsg.php">�鿴����ѶϢ</a></div>
		<div class="lmi"><a href="bbssendmsg.php">����ѶϢ</a></div>
<?php
	}
?>	
	</div>

	<img src="images/open.gif" class="pm" alt="-" 	 
	><a href="bbsstyle.php"><script type="text/javascript">putImage('i_style.gif','class="sfolder"');</script>�Զ������</a><br/> 	 

<?php
	if($currentuser["userid"]!="guest")
	{
?>
	<a href='javascript:changemn("tool");' target="_self"><img id="imgtool" src="images/close.gif" class="pm" alt="+"
	><script type="text/javascript">putImage('i_config.gif','class="sfolder"');</script>���˲�������</a><br/>

	<div class="pp" id="divtool">
<?php
		if(!($currentuser["userlevel"]&BBS_PERM_LOGINOK) ||
			((defined("SITE_NEWSMTH")) && (!($currentuser["flag1"]&BBS_ACTIVATED_FLAG))) )
		{
?>
		<div class="mi"><a href="bbsnew.php">���û���֪</a></div>
		<div class="mi"><a href="bbssendacode.php">���ͼ�����</a></div>
<?php
			if(!($currentuser["userlevel"]&BBS_PERM_LOGINOK)) {
?>
		<div class="mi"><a href="bbsfillform.html">��дע�ᵥ</a></div>
<?php
		}}
?>
		<div class="mi"><a href="bbsinfo.php">��������</a></div>
		<div class="mi"><a href="bbsplan.php">��˵����</a></div>
		<div class="mi"><a href="bbssig.php">��ǩ����</a></div>
		<div class="mi"><a href="bbspwd.php">�޸�����</a></div>
		<div class="mi"><a href="bbsbadlist.php">������</a></div>
		<div class="mi"><a href="bbsparm.php">�޸ĸ��˲���</a></div>
<?php
		if($currentuser["userlevel"]&BBS_PERM_CLOAK)
		{
?>
		<div class="mi"><a href="bbscloak.php">������</a></div>
<?php
		}
		if (defined("SITE_NEWSMTH")) {
?>
		<div class="mi"><a href="bbsal.php">ͨѶ¼</a></div>
<?php
		}
?>
		<div class="mi"><a href="bbsnick.php">��ʱ���ǳ�</a></div>
		<div class="lmi"><a href="bbsfall.php">�趨����</a></div>
	</div>
<?php
	}
	if (defined("SITE_NEWSMTH")) {
?>
	<a href='javascript:changemn("ser");' target="_self"><img id="imgser" src="images/close.gif" class="pm" alt="+"
	><script type="text/javascript">putImage('i_down.gif','class="sfolder"');</script>�ļ����ؼ�����</a><br/>

	<div class="pp" id="divser">
		<div class="mi"><a href="games/index.html">��������</a></div>
		<div class="mi"><a href="games/quiztop.php">���Ĵǵ�</a></div>
		<div class="lmi"><a href="data/fterm-2004memory.rar" target="_blank">Fterm����</a></div>
	</div>
<?php
		if ($currentuser["userlevel"]&BBS_PERM_SYSOP) {
			@include_once ('bbsleftmenu.php');
		}
	}

	if($currentuser["userid"]!="guest"){
?>
	<img src="images/open.gif" class="pm" alt="-"
	><a href="bbslogout.php" id="kbsrc_logout" target="_top"><script type="text/javascript">putImage('i_exit.gif','class="sfolder"');</script>�뿪��վ</a><br/><div id="kbsrcInfo">menu</div>
<?php
	}
?>
</div>
<?php if (!defined("SITE_NEWSMTH")) { ?>
<p align="center">
<a href="http://dev.kcn.cn/" target="_blank"><img src="images/poweredby.gif" border="0" alt="Powered by KBS" /></a>
</p>
<?php } ?>
</body>
</html>
