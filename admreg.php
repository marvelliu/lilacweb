<?php
    require("www2-admin.php");
   
    admin_check("reg");
  
    $reglist = array();
    $count = bbs_admin_getnewreg($reglist);

    if($count == -1)
        html_error_quit("�޷���ȡע�ᵥ�ļ���");

    admin_header("��ע�ᵥ", "�趨ʹ����ע������");
    
    if($count == 0) {
        print("Ŀǰû����Ҫ������ע�ᵥ��");
    }
    else {
        print("���� {$count} ���û��ȴ�������");
        print("<table align=\"center\" cellpadding=\"3\" border=\"1\"><tr><th>��ź�ʱ��</th><th>�û���</th><th>��ʵ����</th><th>����λ</th></tr>");
        for($i=0; $i<$count; $i++) {
            print("<tr><td>{$reglist[$i]["usernum"]}</td><td>{$reglist[$i]["userid"]}</td><td>{$reglist[$i]["realname"]}</td><td>{$reglist[$i]["career"]}</td></tr>");
        }
        print("</table><br>");
    }

    page_footer();
?>
