<?php
$acinf=array();
/*
load_acinf("/home/hitbbs/home/L/luohao/active.inf");
print_r ($acinf);
*/
function load_acinf($filename)
{
	GLOBAL $acinf;
	$acinf["NAME"]="";

    $fp=fopen($filename,"r");
    if ($fp == NULL)
        return NULL;
    while (!feof($fp))
    {
        $buff = fgets($fp , 128);
        if (preg_match_all("|([^:]*):[ ]{0,}([^\n]*)|", $buff, $matches, PREG_PATTERN_ORDER))
        {
			$acinf[$matches[1][0]]=$matches[2][0];
//          echo $matches[1][0];
//          echo ":";
//          echo $matches[2][0];
//          echo "\n";
		}

    }
    fclose($fp);

}
?>
