<?php
/*
** personal corp. configure start
$pcconfig["LIST"] :Blog��ҳ��ÿҳ��ʾ���û���;
$pcconfig["HOME"] :BBS��Ŀ¼,Ĭ��ΪBBS_HOME;
$pcconfig["BBSNAME"] :վ������,Ĭ��ΪBBS_FULL_NAME;
$pcconfig["ETEMS"] :RSS�������Ŀ��;
$pcconfig["NEWS"] :ͳ��ȫվ��������/����ʱ��ʾ����Ŀ��;
$pcconfig["THEMLIST"] :���������ʱÿ��������ʾ��Blog��;
$pcconfig["SITE"] :վ�������,��blog��ʾ,RSS����о�Ҫ�õ�;
$pcconfig["BOARD"] :Blog��Ӧ�İ�������,�ð������Ĭ��ΪBlog����Ա;
$pcconfig["SEARCHFILTER"] :������������ʱ���˵�������;
$pcconfig["SEARCHNUMBER"] :���������������ʱÿҳ��ʾ����Ŀ��;
$pcconfig["SECTION"] :Blog���෽ʽ;
$pcconfig["MINREGTIME"] :����ʱҪ������ע��ʱ��;
$pcconfig["ADMIN"] :����ԱID�����ú����й���Ա������ά����Blog
$pcconfig["TMPSAVETIME"] :���������ݴ湦��ʱ�������ʱ������ ��λΪ��
$pcconfig["USERFILES"] :֧���û����˿ռ�,��֧����Ҫ���� _USER_FILE_ROOT_
$pcconfig["USERFILESLIMIT"]:�û����˿ռ��Ĭ�ϴ�С,��λ��b
$pcconfig["USERFILESNUMLIMIT"]:�û����˿ռ��Ĭ������
$pcconfig["USERFILEPERM"]:�û����˿ռ��Ƿ�֧��Ȩ�޿���
$pcconfig["USERFILEREF"] :�û����˿ռ��Ƿ���HTTP_REFERER�Է�ֹ����,�����˹���ʱ��༭ $accept_hosts ��Ԥ��ֵ
$pcconfig["ENCODINGTBP"] :�� trackback ping ���ַ������б��봦��,�����ͳ�����ͽ��ձ���.�����˹�����ȷ�����PHP֧�� mbstring �� iconv
$pcconfig["TYPES"]: blog����
$pcconfig["BLOGONBLOG"]: blog on blog,hehe,���û�У����""���ɡ�
pc_personal_domainname($userid)���� :�û�Blog������;
*/
$pcconfig["LIST"] = 100;
$pcconfig["HOME"] = BBS_HOME;
$pcconfig["BBSNAME"] = BBS_FULL_NAME;
$pcconfig["ETEMS"] = 20;
$pcconfig["NEWS"] = 100;
$pcconfig["THEMLIST"] = 50;
$pcconfig["SITE"] = "www.newsmth.net";
$pcconfig["BOARD"] = "blogassistant";
$pcconfig["APPBOARD"] = "Blog_Apply";
$pcconfig["SEARCHFILTER"] = " ��";
$pcconfig["SEARCHNUMBER"] = 10;
$pcconfig["ADMIN"] = "SYSOP";
$pcconfig["MINREGTIME"] = 1;
$pcconfig["TMPSAVETIME"] = 300;
$pcconfig["ALLCHARS"] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$pcconfig["USERFILES"] = true;
$pcconfig["USERFILESLIMIT"] = 5*1024*1024;
$pcconfig["USERFILESNUMLIMIT"] = 1000;
$pcconfig["USERFILEPERM"]= false;
$pcconfig["USERFILEREF"] = true;
$pcconfig["ENCODINGTBP"] = true;
$pcconfig["BLOGONBLOG"] = "smth.blog";
$pcconfig["TYPES"] = array (
                    "normal" => "",
                    "group"  => "group",
                    "system" => "smth",
                    "board" => "board",
                    "columnist" => "columnist"
                                                    );
$pcconfig["SECTION"] = array(
			"personal" => "���˿ռ�" ,
			"literature" => "ԭ����ѧ" ,
			"computer" => "���Լ���" ,
			"feeling" => "��еش�" ,
			"collage" => "�ഺУ԰" ,
			"learning" => "ѧ����ѧ" ,
			"amusement" => "��������" ,
			"travel" => "�۹�����" ,
			"literae" => "�Ļ�����" ,
			"community" => "�����Ϣ" ,
			"game" => "��Ϸ��԰" ,
			"sports" => "��������" ,
			"publish" => "ý������" ,
			"business" => "��ҵ����",
			"life" => "������Ѷ",
			"picture" => "ͼƬ����",
			"collection" => "�����ղ�",
			"others" => "�������"
			);

//define("_BLOG_ANONY_COMMENT_", 1);  // �Ƿ�������������

//��ҳ��ʾ��һЩ����
define("_PCMAIN_TIME_LONG_" , 259200 ); //��־ͳ��ʱ��
define("_PCMAIN_NODES_NUM_" , 20 );     //��ʾ����־��Ŀ
define("_PCMAIN_USERS_NUM_" , 20 );     //��ʾ���û���Ŀ
define("_PCMAIN_REC_NODES_" , 40 );     //�Ƽ���־��Ŀ
define("_PCMAIN_NEW_NODES_" , 40 );     //����־��Ŀ
define("_PCMAIN_SECTION_NODES_" , 5 );     //������־��
define("_PCMAIN_ANNS_NUM_"  , 6  );     //������Ŀ
define("_PCMAIN_RECOMMEND_" , 1   );  //�����Ƽ�
//define("_PCMAIN_RECOMMEND_BLOGGER_" , "SYSOP"); //�̶��Ƽ�
/*
** ע: smth.org��blog��ʹ���Ƽ����У�����վ�������Ƽ����У������趨�̶��Ƽ�
*/
define("_PCMAIN_RECOMMEND_QUEUE_" , "smthblogger.php");        //ʹ���Ƽ�����

function pc_personal_domainname($userid)
{
	return "http://".$userid.".mysmth.net";	
}

define('_USER_FILE_ROOT_' , BBS_HOME.'/blogs'); //���˿ռ��Ŀ¼λ�� ��Ҫ�ֹ�����

/*
* $accept_hosts: ���û����˿ռ�֧�ַ�����ʱ������Ƿ���������������ӹ���
*/
$accept_hosts = array(
                '127.0.0.1',
				'61.182.213.237',
				'10.0.4.237',
				'61.182.213.238',
				'10.0.4.238',
				'61.182.213.215',
				'61.182.213.217'
                );
                
/* Trackback Ping String Encoding Configure Start */
$support_encodings = 'ISO-8859-1,UTF-8';
$default_encoding  = 'ISO-8859-1';
$sending_encoding  = 'UTF-8';
/* Trackback Ping String Encoding Configure End */

/* personal corp. configure end */
?>
