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

// index.php
// Repository Configuration Page

include_once '../../mainfile.php';
include_once (XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once (XOOPS_ROOT_PATH."/include/cp_functions.php");
include_once 'inc_dms_functions.php';

//include_once (XOOPS_ROOT_PATH."/modules/dms/inc_pal.php");

global $db;

if (dms_get_var("hdn_update_form") != FALSE)
	{
	//  Document Repository
	
	$doc_path = dms_get_var('txt_doc_path');
	$doc_path = trim($doc_path);
	$doc_path = rtrim($doc_path,"/");
	$doc_path = rtrim($doc_path,"\\");
	$doc_path = str_replace("\\","\\\\",$doc_path);
	
	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".$doc_path."' WHERE name='doc_path'";
	$dmsdb->query($query);
	
	dms_update_config_time_stamp();
	}

dms_get_config();

include XOOPS_ROOT_PATH.'/header.php';

print "<form method='post' action='config_repo.php'>\r";

print "<div ".$dms_config['class_content']." style='text-align: left' >\r";

print "<b>DMS Document Repository Configuration</b><BR><BR>\r";

//  Document Repository
dms_display_spaces(5);
print 'Document Repository Path:  ';
printf("<input type=text name='txt_doc_path' value='%s' size='60' maxlength='250'>",$dms_config['doc_path']);

//  Update and Exit Buttons
print "<BR><BR>\r";
print "<input type='hidden' name='hdn_update_form' value='TRUE'>\r";
print "<input type='submit' value='Update' style='text-align:  left;'>\r";
print "<input type='button' value='Exit' onclick='location=\"index.php\";'>\r";
print "</form>";

print "</div>";

include_once XOOPS_ROOT_PATH.'/footer.php';
?>
