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

// url_add.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';


if (dms_get_var("txt_url_name") != FALSE)
{
	//dms_folder_create(dms_get_var("txt_folder_name"),dms_get_var("hdn_active_folder"));
	
	//global $dmsdb, $dms_user_id;
	
	$obj_name = dms_get_var("txt_url_name");
	$obj_name = dms_strprep($obj_name);
	$obj_owner = dms_get_var("hdn_active_folder");
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type,obj_name,obj_owner) VALUES (";
	$query .= "'".WEBPAGE."',";
	$query .= "'".$obj_name."',";
	$query .= "'".$obj_owner."')";
	$dmsdb->query($query);

	// Get the obj_id of the new object
	$obj_id = $dmsdb->getid();

	/*
	// Store the owner permissions in dms_object_perms  TEMP CODE
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." ";
	$query .= "(ptr_obj_id,user_id, group_id, user_perms, group_perms, everyone_perms) VALUES ('";
	$query .= $obj_id."','";
	$query .= $dms_user_id."','";
	$query .= "0','";
	$query .= "4','";
	$query .= "0','";
	$query .= "0')";
	$dmsdb->query($query);
	*/
	dms_perms_set_init($obj_id,$obj_owner);
	
	
	// Store URL in dms_object_misc
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_misc')." ";
	$query .= "(obj_id,data_type,data) VALUES ('";
	$query .= $obj_id."','";
	$query .= URL."','";
	$query .= dms_get_var("txt_url_address")."')";
	
	$dmsdb->query($query);
	
	
	//  Create an entry in dms_object_properties.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id)";
	$query .= " VALUES ('";
	$query .= $obj_id."')";
	$dmsdb->query($query);
	
	dms_auditing($obj_id,"url/add");  

		
	//header("Location:index.php");
	
	dms_header_redirect("index.php");
	}
else
{
	include XOOPS_ROOT_PATH.'/header.php';

	// Get active folder
	$active_folder = dms_active_folder();

	if(!$xoopsUser->IsAdmin())
			{  
			$active_folder_perms = dms_perms_level($active_folder);
			if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
				{
				print("<SCRIPT LANGUAGE='Javascript'>\r");
				print("location='index.php';");
				print("</SCRIPT>");  
				}
			}

	print "<form name='frm_url_add' method='post' action='url_add.php'>\r";
	print "<table width='100%'>\r";

	display_dms_header();

	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Add Web Page:</b></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td align='left'>Name of Web Page:</td>\r";
	
	print '    <td align="left"><input type="text" name="txt_url_name" size="40" maxlength="250" tabindex="'.$dms_tab_index++.'"></td>'."\r";
	print "  </tr>\r";

	print "  <tr>\r";
	print "    <td align='left'>Address of Web Page:</td>\r";
	
	print '    <td align="left"><input type="text" name="txt_url_address" size="40" maxlength="250" tabindex="'.$dms_tab_index++.'"></td>'."\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='" . _DMS_SUBMIT . "' tabindex='".$dms_tab_index++."'>";
	print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"index.php\";' tabindex='".$dms_tab_index++."'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_active_folder' value='".$active_folder."'>\r";
	print "</form>\r";

	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("  document.frm_url_add.txt_url_name.focus();");
	print("</SCRIPT>");
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
}

?>
