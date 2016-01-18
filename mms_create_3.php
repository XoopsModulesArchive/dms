<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2003                                     //
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

// MMS Integration
// mms_create.php

include '../../mainfile.php';
//include_once 'defines.php';
include_once 'inc_dms_functions.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_properties.php';
include_once 'inc_adn_system.php';
include_once 'inc_adv_system.php';

//$mms_id=$HTTP_GET_VARS['mmsid'];
$mms_property_num = '3';   // This sets the property number that stores the MMS Number.

//if(strlen($mms_id) < 1) $mms_id = $HTTP_POST_VARS['hdn_mmsid'];

/*
foreach ($_SESSION['dms_var_cache'] as $key=>$value)
	{
	print "\$_SESSION['dms_var_cache'][\"$key\"]==$value<br>";
	}
*/
//print $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['tmp_name'];
	

if ($HTTP_POST_VARS["hdn_file_new"])
	{
	$location = "file_options.php?obj_id=";
	$time_stamp = time();
	$partial_path_and_file = dest_path_and_file();

	$mms_id = $dms_var_cache['mms_create_mmsid'];
	
	// Get the location of the document repository
	$file_sys_root = $dms_config['doc_path'];

	$dest_path_and_file    = $file_sys_root."/".$partial_path_and_file;

	// Get the name, path and file of the source file.
	if($dms_var_cache['mms_create_function'] == "CREATE")
		{
		$query  = "SELECT obj_name, current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".$HTTP_POST_VARS['rad_file_id']."'";
		$source_object = $dmsdb->query($query,'ROW');
	
		$query  = "SELECT file_path, file_name, file_type, file_size FROM ".$dmsdb->prefix("dms_object_versions")." ";
		$query .= "WHERE row_id='".$source_object->current_version_row_id."'";
		$source_file_info = $dmsdb->query($query,'ROW');
	
		$source_path_and_file = $dms_config['doc_path']."/".$source_file_info->file_path;
		}
	else
		{
		$source_path_and_file = $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['tmp_name'];
		}
	
	
	// Get active folder
	$query = "SELECT folder_id from ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
	$active_folder = $dmsdb->query($query,'folder_id');

	if(!$active_folder>=1) $active_folder=0;

	if($dms_admin_flag == 0)
		{  
		$active_folder_perms = dms_perms_level($active_folder);
		if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
			{
			print "<SCRIPT LANGUAGE='Javascript'>\r";
			print "location='index.php';";
			print "</SCRIPT>";  
			}
		}
//print "<br>sp:  ".$source_path_and_file;
//print "<br>dp:  ".$dest_path_and_file;
	// Copy the file.
	if (!copy($source_path_and_file,$dest_path_and_file))
		{
		print "Error:  Failure to copy file.  Either source path or destination path is not accessable.\r";
		exit(0);
		}

	// Set the $obj_name and the $file_name.  If the user has not entered a document extension, add one.
	$obj_name = dms_strprep($dms_var_cache['mms_create_obj_name']);
	if(0 == strlen($obj_name)) $obj_name = $source_file_info->file_name;
	$file_name = $obj_name;
	$file_name = str_replace(" ","_",$file_name);    // Replace <space> with _
	// If an extension has not been given for the document, add one. 
	if(!strrchr($file_name,".")) 
	  $file_name = dms_filename_plus_ext($file_name,$source_file_info->file_type);
	
	// Create the new object in dms_objects
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." ";
	$query .= "(template_obj_id,obj_type,obj_name,obj_status,obj_owner,obj_checked_out_user_id,time_stamp_create) ";
	$query .= "VALUES ('";
	
	if($dms_var_cache['mms_create_function'] == "CREATE")
		$query .= $HTTP_POST_VARS['rad_file_id']."','";
	else
		$query .= "0','";
	
	$query .= "0','";
	$query .= $obj_name."','";
	
	if($dms_var_cache['mms_create_function'] == "CREATE")
		$query .= "1','";
	else
		$query .= "0','";
	
	$query .= $dms_var_cache['mms_create_destid']."','";
	
	if($dms_var_cache['mms_create_function'] == "CREATE")
		$query .= $dms_user_id."','";
	else
		$query .= "0','";
	
	$query .= $time_stamp."')";

	$dmsdb->query($query);

	// Get the obj_id of the new object
	$obj_id = $dmsdb->getid();

	dms_perms_set_init($obj_id,$active_folder);

	// Create an entry in dms_object_properties.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id, property_".$mms_property_num.")";
	$query .= " VALUES ('";
	$query .= $obj_id."','".$mms_id."')";

	//mysql_query($query);
	$dmsdb->query($query);

	// Add all additional document properties
	//update_file_properties($obj_id);

	// Create an entry in dms_object_versions and store the appropriate information.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_versions')." (obj_id,file_path,file_name,file_type,file_size,";
	$query .= "major_version,minor_version,sub_minor_version,init_version_flag,time_stamp)";
	$query .= " VALUES ('";
	$query .= $obj_id."','";
	$query .= $partial_path_and_file."','";
	//$query .= $source_file_info->file_name."','";
	
	if($dms_var_cache['mms_create_function'] == "CREATE")
		{
		$query .= $file_name."','";
		$query .= $source_file_info->file_type."','";
		$query .= $source_file_info->file_size."','";
		}
	else	
		{
		$query .= $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['name']."','";
		$query .= $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['type']."','";
		$query .= $_FILES[$HTTP_POST_VARS['hdn_temp_file_name']]['size']."','";
		}
	
	$query .= "1"."','";
	$query .= "0"."','";
	$query .= "0"."','";
	$query .= "1"."','";
	$query .= $time_stamp."')";

	$dmsdb->query($query);
	//mysql_query($query);  

	// Find the row_id of the entry just created in dms_object_versions.
	//$dms_object_versions_row_id = mysql_insert_id();
	$dms_object_versions_row_id = $dmsdb->getid();

	// Enter the row_id of the entry for the current version into dms_objects
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects');
	$query .= " SET current_version_row_id='".$dms_object_versions_row_id."' ";
	$query .= " WHERE obj_id='".$obj_id."'";  

	$dmsdb->query($query);
	//mysql_query($query);

	$location .= $obj_id;

	 // Update the misc_text in the dms_objects table
	dms_update_misc_text($obj_id);

	if($dms_var_cache['mms_create_function'] == "CREATE")
		dms_auditing($obj_id,"document/new");
	else
		dms_auditing($obj_id,"document/import");
	
	// Add the document number to the document.  If the ADN system is not enabled, the function will return without making changes.
	dms_adn_system($obj_id);
	
	// Add the document version number to the document.
	dms_adv_system($obj_id);
	
	dms_folder_subscriptions($obj_id);
	
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	
	if($dms_var_cache['mms_create_function'] == "CREATE")
		{
		// Open the document, in the appropriate application, for editing.
		print "tempwindow = window.open('file_retrieve.php?function=open&obj_id=".$obj_id."','');\r";
		}
		
	print "location='".$location."';";
	print "</SCRIPT>";  

	// Don't use the header() function because of opening a new window with the document.  
	//header("Location:".$location);
	}
else
	{

	include XOOPS_ROOT_PATH.'/header.php';
	$location="mms_create_3.php"; 
	$obj_id = -1;    // Fake object id....used to force folder_expand.php and folder_contract.php to return to this page.
	
	// Get active folder
//	$active_folder = dms_active_folder();

	//  Save the template id in the session variable cache. 
	if(!isset($dms_var_cache['mms_create_destid']))  
		{
		$dms_var_cache['mms_create_destid'] = 0;
		}
	
	if($dms_var_cache['mms_create_destid']==0)
		$dms_var_cache['mms_create_destid']=$HTTP_POST_VARS['rad_folder_id'];
		
	dms_var_cache_save();
/*
	if($dms_admin_flag == 0)
			{  
			$active_folder_perms = dms_perms_level($active_folder);
			if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
				{
				print("<SCRIPT LANGUAGE='Javascript'>\r");
				print("location='index.php';");
				print("</SCRIPT>");  
				}
			}  
*/
	//$file_id=$HTTP_GET_VARS["file_id"];
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_mms_create' action='mms_create_3.php' enctype='multipart/form-data'>\r";
	//display_dms_header(2);
	dms_display_header(2,"","",FALSE);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	if($dms_var_cache['mms_create_function'] == "CREATE") $title = "Create Document:";
	else $title = "Import Document:";
	
	
	print "  <tr><td colspan='2' align='left'><b>".$title."</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left' ".$dms_config['class_content'].">\r";  
	dms_display_spaces(3);
	print "      Name:  ".$dms_var_cache['mms_create_obj_name'];
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td><br></td></tr>\r";
	
	print "  <tr>\r";
	
	print "    <td colspan='2' align='left'>\r";
	
	$query  = "SELECT obj_name FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$dms_var_cache['mms_create_destid']."'";
	$dest_folder_name = $dmsdb->query($query,'obj_name');
	
	display_spaces(3);
	print "Destination Folder:\r&nbsp;&nbsp;&nbsp;";
	$module_url = XOOPS_URL."/modules/dms/";
	print "<img src='".$module_url."images/folder_closed.png'>&nbsp\r";
	print $dest_folder_name."</td>\r";
	
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td><br></td></tr>\r";
	
	print "  <tr>\r";
	
	print "    <td colspan='2' align='left'>\r";
	
	$query  = "SELECT data FROM ".$dmsdb->prefix('dms_config')." ";
	$query .= "WHERE name='template_root_obj_id'";
	$root_folder = $dmsdb->query($query,'data');
	//$root_folder = mysql_result(mysql_query($query),'data');

	if($dms_var_cache['mms_create_function'] == "CREATE")
		{
		dms_display_spaces(3);
		print "Select Template:\r&nbsp;&nbsp;&nbsp;";
		include "inc_file_select.php";
		}
	else
		{
		// Initialize magic_number.  This number is used to create unique file names in order to guarantee that 2 file names
		// will not be identical if 2 users upload a file at the exact same time.  100000 will allow almost 100000 users to use
		// this system.  Ok, the odds of this happening are slim; but, I want the odds to be zero.
		$magic_number = 100000;
		
		$temp_file_name = (string) time().(string) ($magic_number + $dms_user_id);
		dms_display_spaces(3);
		print "Select Document to Import:\r";
		dms_display_spaces(3);
		print "<input name='".$temp_file_name."' size='30' type='file'></td>\r";
		print "<input type='hidden' name='hdn_temp_file_name' value='".$temp_file_name."'>\r";
		print "<input type='hidden' name='MAX_FILE_SIZE' value='5000000'>\r";
		}
		
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  </tr><td colspan='2' align='left'><input type='button' name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex='".$dms_tab_index++."'>\r";
	print "                                    <input type='submit' name='btn_submit' value='Finish' tabindex='".$dms_tab_index++."'></td></tr>\r";
	print "</table>\r";

	print "<input type='hidden' name='hdn_file_new' value='confim'>\r";
	
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';

/*
	foreach ($_SESSION['dms_var_cache'] as $key=>$value)
		{
		print "\$_SESSION['dms_var_cache'][\"$key\"]==$value<br>";
		}
*/

	}
?>



