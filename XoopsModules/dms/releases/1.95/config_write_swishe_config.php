<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                                                                           //
//                                                                           //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

// search_write_config.php
// Administration Page

include_once '../../mainfile.php';
include_once (XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once (XOOPS_ROOT_PATH."/include/cp_functions.php");

include_once (XOOPS_ROOT_PATH."/modules/dms/inc_pal.php");

// Get the path to the repository
$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='doc_path'";
$doc_path = $dmsdb->query($query,'data');

// Create _binfilter.sh
//$file = $doc_path."/_binfilter.sh";
//$fp = fopen($file,'w') or die("<BR><BR>Unable to open $file");

//fputs($fp,"strings \"\$1\" - 2>/dev/null\n");
//fclose($fp);

//chmod($file,0755);

// Create swish-e.conf
$file = $doc_path."/swish-e.conf";
$fp = fopen($file,'w') or die("<BR><BR>Unable to open $file");

$line = "IndexDir ".$doc_path."/\n";
fputs($fp,$line);
$line = "IndexFile ".$doc_path."/index.swish-e\n";
fputs($fp,$line);
//$line = "TruncateDocSize 100000\n";
//fputs($fp,$line);
$line = "IndexReport 1\n";
fputs($fp,$line);
$line = "IndexContents TXT* .dat\n";
fputs($fp,$line);
//$line = "FileFilter .dat \"".$doc_path."/_binfilter.sh\" \"'%p'\"\n";
//fputs($fp,$line);
$line = "IndexOnly .dat\n";
fputs($fp,$line);
$line = "MinWordLimit 3\n";
fputs($fp,$line);

fclose($fp);

chmod($file,0755);

print("<SCRIPT LANGUAGE='Javascript'>\r");
print("location='config_main.php';");
print("</SCRIPT>");  
?>
