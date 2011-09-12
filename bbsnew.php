<?php
require("funcs.php");
login_init();

if ($loginok != 1)
{
	html_nologin();
	exit();
}
cache_header("nocache");
if(!strcmp($currentuser["userid"],"guest"))
{
	header("Location: ".MAINPAGE_FILE);
	exit();
}

if(bbs_verified_email_file_exist($name) && !$currentuser["reg_email"]=="" && $currentuser["userlevel"]&BBS_PERM_LOGINOK)
//if($currentuser["userlevel"]&BBS_PERM_LOGINOK )
{
	header("Location: ".MAINPAGE_FILE);
	exit();
}

html_init("gb2312","","",1);
?>
<br /><br /><br />
<p align="center"><b>欢迎来到<?php echo BBS_FULL_NAME; ?></b></p>
<center>
<table cellspacing="0" cellpadding="5" width="80%" border="0">
<tr><td>
尊敬的<?php echo $currentuser["userid"]; ?>，<br />
您好！<br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;欢迎来到<?php echo BBS_FULL_NAME; ?>，目前您的状态是新人。如果您想拥有发文、聊天、信件和信息等权利，请按照如下演示，注册成为本站合法用户：<br /><br />
<ol>
<?php
	if (defined("HAVE_ACTIVATION")) {
?>
<li>激活帐户，激活码在您的注册信箱内，如果您还未收到本站发出的激活码，请<a href="bbssendacode.php">点击此处重新发送激活码</a>。</li>
<li>熟悉一下本站的环境，在 <?php echo MIN_REG_TIME; ?> 小时后填写注册单。</li>
<?php
	}
?>
<!--<li>填写注册单。注册单在本站站务手工认证通过以后，你就将成为本站合法用户。 请<a href="bbsfillform.html">点击此处填写注册单</a>。</li>-->
<form action="bbssendacode.php" method="get">
<ul>激活帐户，激活码在您的注册信箱内，如果您还未收到本站发出的激活码，请点击下面重新发送激活码。</ul>
<ul>邮箱: <input type="text" value="<?php echo $currentuser["email"]; ?>" id="txtemail" name="txtemail" /></ul>
<ul>如果你在激活账户的时候遇到问题，请注意以下情况：</ul>
<ul><b>调试中,有任何问题，请到bbshelp版发文求助。</b></ul>
<ul><b>注，前几天由于受垃圾邮件的影响，激活信件可能无法收到，现在可以了，同学们可以试一下，如果有问题请到bbshelp版发文</b></ul>
<ul>已知问题：有的用户可能需要重新验证一遍，如果你两次验证后，还是没有发文权限，请</ul>
	<li>点击发送激活邮件</li>
	<li>退出登录</li>
	<li>接收邮件，激活</li>
	<li>重新登录</li>
	<li>务必按照顺序，否则可能需要重试</li>
<input type="submit" value="提交" id="submit" name="submit"/>
</form>



</ol>
</td></tr>
<tr><td align="center">
如果您需要更多帮助，请进入<a href="bbsdoc.php?board=BBShelp">BBS使用求助</a>讨论区。<br /><br />
如果您想先参观一下，请进入<a href="<?php echo MAINPAGE_FILE; ?>">本站首页</a>。<br />
</td></tr>
</table></center>
<?php
html_normal_quit();
?>
