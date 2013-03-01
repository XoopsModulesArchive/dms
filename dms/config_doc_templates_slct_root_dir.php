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

// doc_templates_slct_root_dir.php
// Administration Page

include_once '../../mainfile.php';
include_once (XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once (XOOPS_ROOT_PATH."/include/cp_functions.php");

include_once 'inc_defines.php';
include_once 'inc_dms_functions.php';

//global $db, $HTTP_POST_VARS;

include XOOPS_ROOT_PATH.'/header.php';
$location=XOOPS_URL."/modules/dms/config_doc_templates_slct_root_dir.php";  

if (dms_get_var("hdn_update_template_root_obj_id") != FALSE)
	{
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$HTTP_POST_VARS["rad_folder_id"]."' ";
	$query .= "WHERE name='template_root_obj_id'";
	$dmsdb->query($query);
	
	dms_update_config_time_stamp();
	
	dms_header_redirect("config_main.php");
	
	/*
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "location='config_main.php';";
	print "</SCRIPT>";  
	*/
	}

// Get active folder
$query = "SELECT folder_id from ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
$active_folder = $dmsdb->query($query,'folder_id');
if(!$active_folder>=1) $active_folder=0;

// Get root of update storage.
$query  = "SELECT data FROM ".$dmsdb->prefix('dms_config')." ";
$query .= "WHERE name='updates_root_obj_id'";
$root_folder = $dmsdb->query($query,'data');

include XOOPS_ROOT_PATH.'/header.php';

print "<form name='frm_module_id' method='post' action='config_doc_templates_slct_root_dir.php'>\r";
print '<b>DMS Configuration</b><BR><BR>';

print 'Select Root Folder for Template Documents:';

$obj_id='-1';
include 'inc_folder_select.php';

print "<BR>\r";
print "<input type='submit' name='btn_select_folder' value='Submit'>\r";
print "&nbsp;&nbsp;";
print "<input type='button' name='btn_cancel' value='Cancel' onclick='location=\"config_main.php\";'>\r";
print "<input type='hidden' name='hdn_update_template_root_obj_id' value='TRUE'>\r";
print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';
?>
