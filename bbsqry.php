<?php
	require("www2-funcs.php");
	login_init();
	
	bbs_session_modify_user_mode(BBS_MODE_QUERY);
	if (isset($_GET["userid"])){
		$userid = trim($_GET["userid"]);

		$lookupuser=array ();
		if( $userid=="" || bbs_getuser($userid, $lookupuser) == 0 )
			html_error_quit("���û�������");

		$usermodestr = bbs_getusermode($userid);
		page_header($lookupuser["userid"], "<a href='bbsqry.php'>��ѯ����</a>");
?>
<div class="main smaller">
<pre>
<?php echo $lookupuser["userid"];?> (<?php echo htmlspecialchars($lookupuser["username"]);?>) ����վ <?php echo $lookupuser["numlogins"];?> �Σ������ <?php echo $lookupuser["numposts"];?> ƪ����
�ϴ���  [<?php echo date("D M j H:i:s Y",$lookupuser["lastlogin"]);?>] �� [<?php echo $lookupuser["lasthost"];?>] ����վһ�Ρ�<?php if (isset($lookupuser["score_user"])) echo "����: [".$lookupuser["score_user"]."]"; ?>

����ʱ��[<?php 
if( $usermodestr!="" && $usermodestr{0}=="1" ){
	echo date("D M j H:i:s Y", $lookupuser["lastlogin"]+60+( $lookupuser["numlogins"]+$lookupuser["numposts"] )%100 );
} else if( $lookupuser["exittime"] < $lookupuser["lastlogin"] )
	echo "�������ϻ�ǳ����߲���";
else
	echo date("D M j H:i:s Y", $lookupuser["exittime"]); 
?>] ����: [<?php
	echo (bbs_checknewmail($lookupuser["userid"])) ? "��" : "  ";
?>] ������: [<?php echo bbs_compute_user_value($lookupuser["userid"]); ?>] ���: [<?php echo bbs_user_level_char($lookupuser["userid"]); ?>]��
<?php if( $usermodestr!="" && $usermodestr{1} != "") echo substr($usermodestr, 1); ?>
</pre>
<br/><span class="c36">
<?php
		$plansfile = bbs_sethomefile($lookupuser["userid"], "plans");

		if( file_exists( $plansfile ) ){
?>
����˵��������: </span><br/>
<?php
			$s = bbs2_readfile($plansfile);
			if (is_string($s)) {
?>
<link rel="stylesheet" type="text/css" href="static/www2-ansi.css"/>
<script type="text/javascript" src="static/www2-addons.js"></script>
<div id="divPlan" class="AnsiArticleBW"><div id="dp1">
<script type="text/javascript"><!--
triggerAnsiDiv('divPlan','dp1');
<?php
				echo $s;
?>
//-->
</script></div></div>
<?php
			}
		}else{
?>
û�и���˵����</span>
<?php
		}
?>
</div>
<div class="oper smaller">
[<a href="bbspstmail.php?userid=<?php echo $lookupuser["userid"];?>&title=û����">д���ʺ�</a>]
[<a href="bbssendmsg.php?destid=<?php echo $lookupuser["userid"];?>">����ѶϢ</a>]
[<a href="bbsfadd.php?userid=<?php echo $lookupuser["userid"];?>">�������</a>]
[<?php bbs_add_super_fav ('[�û�] '.$lookupuser['userid'], 'bbsqry.php?userid='.$lookupuser['userid']); ?>]
[<a href="bbsfdel.php?userid=<?php echo $lookupuser["userid"];?>">ɾ������</a>]
<?php if ( $lookupuser["flag1"] & BBS_PCORP_FLAG ) { ?>
[<a href="pc/index.php?id=<?php echo $lookupuser["userid"];?>">BLOG</a>]
<?php } ?>
</div>
<?php
	} else {
		page_header("��ѯ����");
?>
<div class="oper">
<form action="bbsqry.php" method="get">
�������û���: <input name="userid" maxlength="12" size="12" type="text" id="sfocus"/>
<input type="submit" value="��ѯ�û�"/>
</form>
</div>
<?php
	}
	page_footer();
?>

