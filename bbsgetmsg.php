<?php
	require("www2-funcs.php");
	login_init();
	page_header("ѶϢ", FALSE);
?>
<body class="msgs">
<?php
	$frameheight = 0;
	if (strcmp($currentuser["userid"], "guest")) {
		$ret=bbs_getwebmsg($srcid,$msgbuf,$srcutmpnum,$sndtime);
		if ($ret) {
?>
<bgsound src="/sound/msg.wav">
<div id="msgs">
<?php echo $srcid; ?> (<?php echo strftime("%b %e %H:%M", $sndtime); ?>): <?php echo htmlspecialchars($msgbuf); ?> 
(<?php if($srcid != "վ���㲥") { ?>
<a target="f3" href="bbssendmsg.php?destid=<?php echo $srcid; ?>&destutmp=<?php echo $srcutmpnum; ?>">[��ѶϢ]</a> 
<?php } ?><a href="bbsgetmsg.php?refresh">[����]</a>)
</div>
<?php
			$frameheight = 25;
		}
	}
?>
<script>
var ff = top.document.getElementById("viewfrm");
if (ff) ff.rows = "<?php echo $frameheight; ?>,*,20";
</script>
</body>
</html>
