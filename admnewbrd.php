<?php
    require("www2-admin.php");

    admin_check("newbrd");

    if(isset($_POST["boardname"])) {
        $boardname = $_POST["boardname"];
        $ret = bbs_admin_newboard($boardname);
        switch($ret) {
        case 0:
            html_success_quit("���� {$boardname} ����ɹ�����������������趨�������ԡ�", array("<a href=\"admeditbrd.php?board={$boardname}\">�趨 {$boardname} �İ�������</a>"));
            break;
        case -1:
            html_error_quit("�������Ʋ���Ϊ�ա�", array("<a href=\"admnewbrd.php\">���ذ��濪�����</a>"));
            break;
        case -2:
            html_error_quit("�������Ʋ����Ϲ涨��", array("<a href=\"admnewbrd.php\">���ذ��濪�����</a>"));
            break;
        case -3:
            html_error_quit("ͬ�������Ѿ����ڣ������һ�����ơ�", array("<a href=\"admnewbrd.php\">���ذ��濪�����</a>"));
            break;
        case -4:
            html_error_quit("�޷���Ӱ��棬Ҳ���Ѿ��ﵽϵͳ�趨�İ����������ޡ�", array("<a href=\"admnewbrd.php\">���ذ��濪�����</a>"));
            break;
        case -5:
            html_error_quit("ϵͳ��������ϵ������Ա��", array("<a href=\"admnewbrd.php\">���ذ��濪�����</a>"));
            break;
        default:
        }
    }

    admin_header("����", "����һ���µ�������");
?>
<form method="post" action="admnewbrd.php" class="medium">
<fieldset><legend>�����°���</legend><div class="inputs">
<label>����Ӣ������:</label><input type="text" name="boardname" size="20" maxlength="30">
<input type="submit" value="ȷ��"></div></fieldset></form><br>
<?php
    page_footer();
?>
