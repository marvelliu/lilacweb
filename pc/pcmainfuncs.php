<?php
function pcmain_html_init()
{
	global $pcconfig;
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $pcconfig["BBSNAME"]; ?>BLOG</title>
<style type="text/css">
<!--
body {font-family: ����;}
.table {border-top-width: 1px;border-left-width: 1px;border-top-style: solid;border-left-style: solid;border-top-color: #999999;border-left-color: #999999;}
.channel {font-size: 12px;font-weight: bold;color: #FFFFFF;text-align: left;text-indent: 3pt;}
.more {font-size: 11px;color: #ffffff;}
.channelback {background-color: #1F66A7;border-right-width: 1px;border-bottom-width: 1px;border-right-style: solid;border-bottom-style: solid;border-right-color: #999999;border-bottom-color: #999999;font-size: 12px;font-weight: bold;color: #FFFFFF;text-align: left;text-indent: 3pt;}
.td {border-right-width: 1px;border-bottom-width: 1px;border-right-style: solid;border-bottom-style: solid;border-right-color: #999999;border-bottom-color: #999999;line-height: 16px;word-wrap:break-word;word-break:break-all;}
body {font-size: 12px;line-height: 16px;}
td {word-wrap:break-word;word-break:break-all;}
a:link {color: #333333;text-decoration: none;}
a:visited {color: #003333;text-decoration: none;}
a:hover {color: #003366;text-decoration: underline;}
.topic {color: #666666;background-color: #D2E9FF;text-align: left;text-indent: 5pt;font-size: 12px;font-weight: bold;border: 1px solid #999999;}
input {font-size: 12px;font-weight: lighter;}
select {font-size: 12px;font-weight: lighter;}
.textinput {background-color: #F6F6F6;border: 1px solid #999999;}
.low {font-size: 12px;color: #666666;}
.td1 {border-bottom-width: 1px;border-bottom-style: dashed;border-bottom-color: #999999;text-align:left;line-height: 16px;background-color: #FCFCFC;word-wrap:break-word;word-break:break-all;}
.td2 {border-bottom-width: 1px;border-bottom-style: dashed;border-bottom-color: #999999;text-align:left;background-color: #F0F0F0;line-height: 16px;word-wrap:break-word;word-break:break-all;}
.low2 {color: #3399CC;}
.navigationbar{color: #ffffff;background-color: #999999;text-decoration: none;line-height: 20px;text-align:center;}
.navigationbar a {color: #ffffff;}
.navigationbar a:visited {color: #f6f6f6;}
.navigationbar a:hover {color: #ff0000;text-decoration: none;text-align: center;}
.table2{border-width: 1px;border-color: #999999; border-top-style: dashed; border-left-style: dashed;border-right-style: none; border-bottom-style: none;}
.td3{valign: top; border-width: 1px;border-color: #999999; border-right-style: dashed; border-bottom-style: dashed;border-top-style: none; border-left-style: none;}
.td4{valign: top; border-width: 1px;border-color: #999999; border-right-style: none; border-bottom-style: dashed;border-top-style: none; border-left-style: none;}
ul {margin: 0px 0px 0px 5px; padding: 0px;}
li {padding: 0px; margin-left: 10px;}
-->
</style>
</head>
<body topmargin="0" leftmargin="0"><center>
<table width="750"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" background="images/bg.gif">
	<?php
	    echo $pcconfig["BBSNAME"];
	?>
	BLOG
	<a href="/pc/pcapp0.html"><font color="red">��������BLOG</font></a>
	</td>
    </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td width="180"><img src="/images/logo.gif" /></td>
        
        <td>
      	&nbsp;
        </td>
        <td width="80" align="center" valign="middle" style="line-height:25px ">
		<a href="/mainpage.html">��վ��ҳ</a><br/>
		<a href="/bbsdoc.php?board=<?php echo $pcconfig["BOARD"]; ?>">BLOG��̳</a><br />
		<a href="/pc/index.php?id=<?php echo $pcconfig["ADMIN"]; ?>">��������</a>
		</td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td height="1" bgcolor="#06337D"> </td>
  </tr>
  <tr>
            <td class="navigationbar"><?php pcmain_navigation_bar(); ?></td>
  </tr>
  <tr>
    <td height="1" bgcolor="#06337D"> </td>
  </tr>  
<?php	
}


function pcmain_html_quit()
{
	global $pcconfig;
?>	
<tr><td height="1" bgcolor="#06337D"> </td>
  </tr>
  <tr><td height="5"> </td></tr>
         <tr>
            <td class="navigationbar"><?php pcmain_navigation_bar(); ?></td>
          </tr>
          <tr><td height="5"> </td></tr>
  <tr>
    <td align="center">
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
    <br />��Ȩ���� &copy;<?php echo $pcconfig["BBSNAME"]; ?>
	</td>
    </tr>
</table>
</center></body></html>
<?php
}

function pcmain_navigation_bar()
{
	global $pcconfig;
?>
<a href="/pc/index.html">��ҳ</a>
|
<a href="/bbsdoc.php?board=<?php echo $pcconfig["BOARD"]; ?>">��̳</a>
<?php
    if ($pcconfig["BLOGONBLOG"]) {
?>
|
<a href="/pc/index.php?id=<?php echo urlencode ($pcconfig["BLOGONBLOG"]); ?>">�ĵü�</a>
<?php
    }
?>
|
<a href="/pc/pc.php">�û��б�</a>
|
<a href="/pc/pcreco.php"><?php echo BBS_NICKNAME; ?>�Ƽ�</a>
|
<a href="/pc/pcnew.php">������־</a>
|
<a href="/pc/pcnew.php?t=c">��������</a>
|
<a href="/pc/pcsec.php">�����б�</a>
|
<a href="/pc/pclist.php">��������</a>
|
<a href="/pc/pcnsearch.php">��־����</a>
|
<?php bbs_add_super_fav ('BLOG'); ?>
|
<a href="/pc/pcapp0.html"><font color="red">��������</font></a>
<?php
}
?>
