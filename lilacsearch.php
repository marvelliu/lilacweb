<?php
	require("www2-funcs.php");
	login_init();
//	bbs_session_modify_user_mode(BBS_MODE_POSTING);
	assert_login();
	page_header("�϶�������");
?>
<script type="text/javascript" src="static/ajax.js"></script>
<script type="text/javascript" src="static/search.js"></script>
<h1> �϶�������</h1>
<form action="" method="post">
�����ؼ��� <input type="text" id="q"><br/>
<input type="submit" value="����" onclick="return search();">
</form>
<hr/>
<div id=errorInfo></div>
<div id=warningInfo></div>
<div id=messageInfo></div>
<div id=results>
</div>
<?php
page_footer();
?>
