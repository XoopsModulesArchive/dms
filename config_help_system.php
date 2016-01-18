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
$location=XOOPS_URL."/modules/dms/config_help_system.php";  

/*
$help_id = "";
if($HTTP_GET_VARS["id"]) $help_id = $HTTP_GET_VARS["id"];
*/
$help_id = dms_get_var("id");

if(strlen($help_id) > 0)  $dms_var_cache['help_id'] = $help_id;
dms_var_cache_save();

$help_id = $dms_var_cache['help_id'];

$delete_help_id = dms_get_var("delete_help_id");
if (strlen($delete_help_id) > 0)
	{
	$query = "DELETE FROM ".$dmsdb->prefix('dms_help_system')." WHERE help_id='".$delete_help_id."'";
	$dmsdb->query($query);
	
	//header("Location:index.php");
	dms_header_redirect("index.php");
	exit(0);
	}

$hdn_update_obj_id_ptr = dms_get_var("hdn_update_obj_id_ptr");
	
//if ($HTTP_POST_VARS["hdn_update_obj_id_ptr"])
if(strlen($hdn_update_obj_id_ptr) > 0)
	{
//	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$HTTP_POST_VARS["rad_folder_id"]."' ";
//	$query .= "WHERE name='template_root_obj_id'";
//	$dmsdb->query($query);

	$query = "SELECT * FROM ".$dmsdb->prefix('dms_help_system')." WHERE help_id='".$help_id."'";
	$dmsdb->query($query);
	if($dmsdb->getnumrows() > 0)
		{
		// Update the help entry.
		$query  = "UPDATE ".$dmsdb->prefix('dms_help_system')." ";
		$query .= "SET obj_id_ptr = '".dms_get_var("rad_file_id")."' ";
		$query .= "WHERE help_id='".$help_id."'";
		$dmsdb->query($query);
		}
	else
		{
		// Insert the help entry.
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_help_system')." ";
		$query .= "(help_id,obj_id_ptr) VALUES ";
		$query .= "('".$help_id."','".dms_get_var("rad_file_id")."')";
		$dmsdb->query($query);
		}
	
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "location='index.php';";
	print "</SCRIPT>";  
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

print "<form name='frm_module_id' method='post' action='config_help_system.php'>\r";

print "<div ".$dms_config['class_content']." style='text-align: left' >\r";

print '<b>DMS Configuration</b><BR><BR>';

print 'Select Document for Help:';

$obj_id='-1';
include 'inc_file_select.php';

print "<BR>\r";
print "<input type='submit' name='btn_select_folder' value='Submit'>\r";
print "&nbsp;&nbsp;";
print "<input type='button' name='btn_delete' value='Delete' onclick='location=\"config_help_system.php?delete_help_id=".$help_id."\";'>\r";
print "&nbsp;&nbsp;";
print "<input type='button' name='btn_cancel' value='Cancel' onclick='location=\"index.php\";'>\r";
print "<input type='hidden' name='hdn_update_obj_id_ptr' value='TRUE'>\r";
print "</form>\r";

print "</div>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';
?>
