<?php
	/**
	 * ��ʧ������û�ȡ��������
	 * by binxun 2003.5
	 */
	require("funcs.php");
	html_init("gb2312");

	@$userid=$_POST["userid"];
	@$realname=$_POST["realname"];
	@$email=$_POST["email"];

	if(strlen($userid)<2 || strlen($realname) < 2 || strlen($email) < 4)
     	html_error_quit("����д��ĳһ��̫����");

	$ret=bbs_findpwd_check($userid,$realname,$email);
    //settype($ret,"integer");
	echo " ret is ".$ret;
	settype($ret,"string");
	if(strlen($ret) > 1)
	{
        echo "������ ".$ret;
		//todo �������뵽ָ��������
	}
	else
	    html_error_quit("ȡ������ʧ��!,������д�����Ϻ�ע�����ϲ���!");

?>
<body>
ȡ������ɹ�,�뵽ָ����email��ȡ�ظ�ID������</body>
</html>
