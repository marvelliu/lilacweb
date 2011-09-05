<?php
require("funcs.php");
login_init();
$name = $currentuser["userid"];
$email = $currentuser["email"];
$remail = $currentuser["email"];
echo $name;
echo "<br/>";
echo $email;
echo "<br/>";
echo $remail;
echo "<br/>";

#$remail = "a@c";
echo $remail=="";
echo "---";
echo isset($remail);
$pos = strpos($remail, "@");
echo $pos;
if (bbs_verified_email_file_exist($name) && !$currentuser["reg_email"]=="")
echo "+++";
else
echo "---";
?>
