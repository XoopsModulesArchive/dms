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

// file_import.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_properties.php';


if(dms_get_var('hdn_confirm') == "confirm")
	{
	$time_stamp = time();
	$active_folder = dms_active_folder();
	
	//dms_strprep($HTTP_POST_VARS['txt_file_name']);
	// Get the name of the document.  If a name has not been entered, use the filename of the document.
	$obj_name =  dms_get_var("txt_link_name");

	$obj_id = dms_get_var("txt_obj_id");
//	if(0 == strlen($obj_name))
//		$obj_name = $_FILES[$temp_file_name]["name"];  //$_FILES[dms_get_var("hdn_temp_file_name")]["name"];
	
	$obj_name = dms_strprep($obj_name);

	// Determine the object type
	$query  = "SELECT obj_type FROM ".$dmsdb->prefix('dms_objects')." WHERE obj_id='".$obj_id."'";
	$obj_type = $dmsdb->query($query,"obj_type");

	switch($obj_type)
		{
		case FILE:
			$obj_type = FILELINK;
			break;
		case FOLDER:
			$obj_type = FOLDERLINK;
			break;
		default:
			print "Error:  Invalid object type --";
			print $obj_type;
			exit(0);
		}

	// Create the new object in dms_objects
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." (ptr_obj_id,obj_type,obj_name,obj_owner,time_stamp_create,file_type)";
	$query .= " VALUES ('";
	$query .= $obj_id."','";
	$query .= $obj_type."','";
	$query .= $obj_name."','";
	$query .= $active_folder."','";
	$query .= $time_stamp."','";
	$query .= $file_type."')";
	
	$dmsdb->query($query);
	
	// Get the obj_id of the new object
	$obj_id = $dmsdb->getid();
	
	dms_perms_set_init($obj_id,$active_folder);

	// Create an entry in dms_object_properties.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id)";
	$query .= " VALUES ('";
	$query .= $obj_id."')";
	
	$dmsdb->query($query);
	
	// Add all additional document properties
	update_file_properties($obj_id);
	
	// Create an entry in dms_object_versions and store the appropriate information.
	$file_name = dms_strprep($_FILES[$temp_file_name]["name"]);
	
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_versions')." (obj_id,file_path,file_name,file_type,file_size,";
	$query .= "major_version,minor_version,sub_minor_version,time_stamp)";
	$query .= " VALUES ('";
	$query .= $obj_id."','";
	$query .= $partial_path_and_file."','";
	$query .= $file_name."','";
	//$query .= $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['name']."','";
	$query .= $file_type."','";
	$query .= $_FILES[$temp_file_name]["size"]."','";
	$query .= dms_get_var("slct_version_major")."','";
	$query .= dms_get_var("slct_version_minor")."','";
	$query .= dms_get_var("slct_version_sub_minor")."','";
	$query .= $time_stamp."')";
	
//print $query;  
	$dmsdb->query($query);  

	dms_auditing($obj_id,"link/create");

	//header("Location:index.php");
	dms_header_redirect("link_create.php");
	}
else
	{
	//include XOOPS_ROOT_PATH.'/header.php';
	include 'inc_pal_header.php';
	
	// Get active folder
	$active_folder = dms_active_folder();
		
	if(!$dms_admin_flag)
		{  
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		}
		
	print "<form name='frm_link_create' method='post' action='link_create.php'>\r";
	print "<table width='100%'>\r";
	
	display_dms_header();
	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Create Link:</b></td></tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";

	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td align='left'>Name:</td>\r";
	print "    <td align='left'><input type='text' name='txt_link_name' size='40' maxlength='250' class='".$dms_config['class_content']."' tabindex='".$dms_tab_index++."'></td>\r";
	print "  </tr>\r";

	print "  <tr>\r";
	print "    <td align='left'>Object ID:</td>\r";
	print "    <td align='left'><input type='text' name='txt_obj_id' size='10' maxlength='10' class='".$dms_config['class_content']."' tabindex='".$dms_tab_index++."'></td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='Submit' tabindex='".$dms_tab_index++."'>";
	print "    <input type=button name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex=".$dms_tab_index++."></td>\r";
	print "    <input type=hidden name='hdn_confirm' value='confirm'>\r";
	print "</table>\r";
	print "</form>\r";
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("  document.frm_link_create.txt_link_name.focus();");
	print("</SCRIPT>");  
	
	include 'inc_pal_footer.php';
	//include_once XOOPS_ROOT_PATH.'/footer.php';
  }
?>
