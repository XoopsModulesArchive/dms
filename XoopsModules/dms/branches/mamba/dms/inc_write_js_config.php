<?php
$file = XOOPS_ROOT_PATH."/modules/dms/job_server_config.php";
//chmod(0777,$file);
$fp = fopen($file,'w') or die("<BR><BR>Unable to open $file");

$line = "<?php\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_ROOT_PATH','".XOOPS_ROOT_PATH."');\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_DB_PREFIX','".XOOPS_DB_PREFIX."');\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_DB_HOST','".XOOPS_DB_HOST."');\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_DB_USER','".XOOPS_DB_USER."');\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_DB_PASS','".XOOPS_DB_PASS."');\n";
fputs($fp,$line);
$line = "define('JS_XOOPS_DB_NAME','".XOOPS_DB_NAME."');\n";
fputs($fp,$line);
$line = "?>\n";
fputs($fp,$line);

fclose($fp);

chmod($file,0744);
?>
