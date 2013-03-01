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

// file_checkin.php

include '../../mainfile.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_dms_functions.php';
//include_once 'inc_defines.php';
include_once 'inc_adn_system.php';
include_once 'inc_adv_system.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';
include_once 'inc_file_upload.php';

// Determine which web page to return to.
$return_url = dms_get_var("return_url");
if($return_url == FALSE) $return_url = dms_get_var("hdn_return_url");
if($return_url == FALSE) $return_url = "index.php";

if (dms_get_var("hdn_checkin_file_confirm") == "confirm")
	{
	$obj_id = dms_get_var("hdn_file_id");
	$temp_file_name = dms_get_var('hdn_temp_file_name');
	
	// Check for an upload error.
	$error_code = $_FILES[$temp_file_name]['error'];
	if($error_code > 0)
		{
		$error_message = dms_get_file_upload_error_message($error_code);
	
		include XOOPS_ROOT_PATH.'/header.php';
		print "<table>\r";
		print "  <tr><td align='left'>Error:  The document has not been checked-in into the system.</td></tr>\r";
		print "  <tr><td align='left'>".$error_message."</td></tr>\r";
		print "  <tr><td><BR></td></tr>\r";
		print "  <tr><td align='left'><input type='button' name='btn_continue' value='Continue' onclick='location=\"file_checkin.php?obj_id=".$obj_id."\";'></td></tr>\r";
		
		include_once XOOPS_ROOT_PATH.'/footer.php';
		exit(0);

		}
	
/*
	if( $_FILES['upload_file']['size'] == 0)
		{
		include XOOPS_ROOT_PATH.'/header.php';
		print "<table>\r";
		print "  <tr><td align='left'>Error:  The document has not been checked-in.</td></tr>\r";
		print "  <tr><td><BR></td></tr>\r";
		print "  <tr><td align='left'>&nbsp;&nbsp;&nbsp;The most likely problem is that the file name has not been selected.  Please select the file and try again.</td></tr>\r";
		print "  <tr><td><BR></td></tr>\r";
		print "  <tr><td align='left'><input type='button' name='btn_continue' value='Continue' onclick='location=\"file_checkin.php?obj_id=".dms_get_var('hdn_file_id')."\";'></td></tr>\r";
		
		include_once XOOPS_ROOT_PATH.'/footer.php';
		exit(0);
		}
*/
	if (    (dms_get_var('hdn_new_major_version')      == dms_get_var('hdn_current_major_version') )
	 &&     (dms_get_var('hdn_new_minor_version')      == dms_get_var('hdn_current_minor_version') )
	 &&     (dms_get_var('hdn_new_sub_minor_version')  == dms_get_var('hdn_current_sub_minor_version')) )
		{
		// *** Version # is same as current version #
		$version_change_flag = "SAME";
		
		// Determine the path and filename of the new file
		
		// Get the document path and filename of the destination file
		$query  = "SELECT file_path,row_id from ".$dmsdb->prefix("dms_object_versions")." ";
		$query .= "WHERE obj_id='".$obj_id."' ";
		$query .= "AND   major_version='".dms_get_var('hdn_current_major_version')."' ";
		$query .= "AND   minor_version='".dms_get_var('hdn_current_minor_version')."' ";
		$query .= "AND   sub_minor_version='".dms_get_var('hdn_current_sub_minor_version')."'";
		$result = $dmsdb->query($query,"ROW");

		$partial_path_and_file = $result->file_path;
		$db_row_id = $result->row_id;
		
		// Get the path of the document repository
		$query = "SELECT data from ".$dmsdb->prefix("dms_config")." WHERE name='doc_path'";
		$file_sys_root = $dmsdb->query($query,'data'); 
    
		// Get the document path and filename of the destination file
		$dest_path_and_file = $file_sys_root."/".$partial_path_and_file;

		// Get the source path and filename
		$source_path_and_file = $_FILES[$temp_file_name]['tmp_name'];
   
		// Move the file
		if(is_uploaded_file($source_path_and_file))
		 move_uploaded_file($source_path_and_file,$dest_path_and_file) or die(_DMS_UNABLE_TO_MOVE);
		else die(_DMS_FILE_INACCESSABLE);
		
		$file_name = dms_strprep($_FILES[$temp_file_name]['name']);
		
		$file_type = $_FILES[$temp_file_name]['type'];
		if($dms_config['OS']=="Linux") $file_type = trim(exec('file -bi '. escapeshellarg($dest_path_and_file)));
		
		// Update the entry in dms_object_versions and store the appropriate information.
		$query  = "UPDATE ".$dmsdb->prefix('dms_object_versions')." SET ";
		$query .= "init_version_flag='0',";
		$query .= "file_name='".$file_name."',";
		$query .= "file_type='".$file_type."',";
		$query .= "file_size='".$_FILES[$temp_file_name]['size']."',";
		$query .= "time_stamp='".dms_get_var('hdn_time_stamp')."' ";
		$query .= "WHERE row_id='".$db_row_id."'";
//print $query;
		$dmsdb->query($query);
		
		// Set file status as normal
		$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
		$query .= "SET ";
		$query .= "obj_status='0',";
		$query .= "obj_checked_out_user_id='0' ";
		$query .= "WHERE obj_id='".$obj_id."'";
    
		$dmsdb->query($query);
		}
	else
		{
		// *** Version # is different from current version #
		$version_change_flag = "DIFF";
		
		// Determine the path and filename of the new file
  
		// Get the document path and filename of the destination file
		$partial_path_and_file = dest_path_and_file();

		// Get the path of the document repository
		$query = "SELECT data from ".$dmsdb->prefix("dms_config")." WHERE name='doc_path'";
		$file_sys_root = $dmsdb->query($query,'data');
    
		// Get the document path and filename of the destination file
		$dest_path_and_file = $file_sys_root."/".$partial_path_and_file;
  
		// Get the source path and filename
		$source_path_and_file = $_FILES[$temp_file_name]['tmp_name'];
    
		// Move the file
		if(is_uploaded_file($source_path_and_file))
		 move_uploaded_file($source_path_and_file,$dest_path_and_file) or die(_DMS_UNABLE_TO_MOVE);
		else die(_DMS_FILE_INACCESSABLE);

		$file_name = dms_strprep($_FILES[$temp_file_name]['name']);

		$file_type = $_FILES[$temp_file_name]['type'];
		if($dms_config['OS']=="Linux") $file_type = trim(exec('file -bi '. escapeshellarg($dest_path_and_file)));
		
		// Add a new version of this file to dms_object_versions
		// Create an entry in dms_object_versions and store the appropriate information.
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_versions')." (obj_id,file_path,file_name,file_type,file_size,";
		$query .= "major_version,minor_version,sub_minor_version,time_stamp)";
		$query .= " VALUES ('";
		$query .= $obj_id."','";
		$query .= $partial_path_and_file."','";
		$query .= $file_name."','";
		$query .= $file_type."','";
		$query .= $_FILES[$temp_file_name]['size']."','";
		$query .= dms_get_var('hdn_new_major_version')."','";
		$query .= dms_get_var('hdn_new_minor_version')."','";
		$query .= dms_get_var('hdn_new_sub_minor_version')."','";
		$query .= dms_get_var('hdn_time_stamp')."')";
		//print $query;
		$dmsdb->query($query);
  
		// Find the row_id of the entry just created in dms_object_versions.
		$dms_object_versions_row_id = $dmsdb->getid();
      
		// Set file status as normal and change the current_version_row_id
		$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
		$query .= "SET ";
		$query .= "obj_status='0',";
		$query .= "obj_checked_out_user_id='0',";
		$query .= "current_version_row_id='".$dms_object_versions_row_id."' ";
		$query .= "WHERE obj_id='".$obj_id."'";
    
		$dmsdb->query($query);
  
		}

		
//	$obj_id = dms_get_var('hdn_file_id');
	$comments = dms_strprep(dms_get_var('txt_comments'));
	
	dms_auditing($obj_id,"document/update comments");

	$query = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$current_version_row_id = $dmsdb->query($query,"current_version_row_id");

	if($version_change_flag == "SAME")
		{
		$query  = "UPDATE ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= "SET comment='".$comments."' ";
		$query .= "WHERE dov_row_id='".$current_version_row_id."'";
		$dmsdb->query($query);
		}
	else
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= "(dov_row_id,comment) ";
		$query .= "VALUES ('".$current_version_row_id."','".$comments."')";
		$dmsdb->query($query);
		}

		  
	dms_auditing(dms_get_var("hdn_file_id"),"document/checkin");

	if($dms_config['sub_email_enable']=='1')
		{
		$query  = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".$obj_id."'";  
		$doc_name = $dmsdb->query($query,'obj_name');

		$user_name = $xoopsUser->getUnameFromId($dms_user_id);
		
		$message  = "A document has been checked-in to the DMS:<BR><BR>";
		$message .= "&nbsp;&nbsp;Document:&nbsp;&nbsp;".$doc_name."<BR>";
		$message .= "&nbsp;&nbsp;User:&nbsp;&nbsp;".$user_name."<BR>";
		
		if($dms_config['comments_enable'] == 1)
			{
			$message .= "<BR><BR>";
			$message .= "Comments:<BR><BR>";
			$message .= $comments;
			}
		
		dms_email_subscribers($obj_id,$message);
		}
	
	
	
	// Add to the document history
	dms_doc_history($obj_id);
	
	dms_folder_subscriptions($obj_id);
	
	// Add the document number to the document.  If the ADN system is not enabled, the function will return without making changes.
	dms_adn_system($obj_id);
	
	// Add the version number to the document.
	dms_adv_system($obj_id);
	
	// If enabled, adjust the file names for the full text search.
	dms_fts_doc_maintenance($obj_id);
	
	dms_message("The document has been successfully checked-in.");

	dms_document_name_sync($obj_id);
	
	$active_folder = dms_active_folder();
	
	// Check to see if an automatic lifecycle exists for this document.  If it exists, apply it.
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id=".$active_folder." AND data_type='".FOLDER_AUTO_LIFECYCLE_NUM."'";
	$folder_auto_lifecycle_num = $dmsdb->query($query,'data');
	if($dmsdb->getnumrows() == 1) dms_apply_lifecycle($obj_id,$folder_auto_lifecycle_num);
	
	//header("Location:".$return_url);
	dms_header_redirect($return_url);
//exit(0);
	}
else
	{
	// Permissions required to access this page:
	//  EDIT, OWNER
	$perms_level = dms_perms_level(dms_get_var('obj_id'));

	if ( ($perms_level != 3) && ($perms_level != 4) )
		{
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("  location='index.php';");
		print("</SCRIPT>");  
		end();
		}
    
	include 'inc_pal_header.php';
	   
	$obj_id = dms_get_var('obj_id');
	
	// Get file information
	$query  = "SELECT obj_name,current_version_row_id from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$first_result = $dmsdb->query($query,"ROW");
	
	$query  = "SELECT major_version,minor_version,sub_minor_version,init_version_flag from ".$dmsdb->prefix('dms_object_versions')." ";
	$query .= "WHERE row_id='".$first_result->current_version_row_id."'";
	$version_number = $dmsdb->query($query,"ROW");
	
	$version_number_base = $version_number->major_version;
	$version_number_base += ($version_number->minor_version * 0.1);
	$version_number_base += ($version_number->sub_minor_version * 0.01);
	
	$init_version_flag = $version_number->init_version_flag;
	//  print $version_number_base."<BR>";
	
	$version_number_inc_major = ($version_number->major_version + 1);
	$version_number_inc_minor = ($version_number->major_version + (($version_number->minor_version * 0.1)+ 0.1));
	$version_number_inc_sub_minor 
			= ($version_number->major_version 
				+ ($version_number->minor_version * 0.1) 
				+ (($version_number->sub_minor_version * 0.01) + 0.01));
	
	if ($version_number_inc_sub_minor == $version_number_inc_minor) $version_number_inc_sub_minor = 0;
	if ($version_number_inc_minor == $version_number_inc_major) $version_number_inc_minor = 0;
	
	//  print $version_number_inc_sub_minor."<BR>"; 
	//  print $version_number_inc_minor."<BR>";
	//  print $version_number_inc_major."<BR>";
	
	print "<form name='frm_checkin' method='post' action='file_checkin.php' enctype='multipart/form-data'>\r";
	print "<table width='100%'>\r";
	//print "  <tr><td colspan='2' class='cHeader'><center><b><font size='2'>Title Goes Here</font></b></center></td></tr>\r";
	display_dms_header();
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>" . _DMS_CHECKIN_FILE . "</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>" . _DMS_FILE_NAME . "&nbsp;&nbsp;&nbsp;";
	print "        ".$first_result->obj_name."</td>\r";
	print "  </tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>" . _DMS_CURRENT_VERSION . "&nbsp;&nbsp;&nbsp;";
	print        $version_number->major_version.".";
	print        $version_number->minor_version;
	print        $version_number->sub_minor_version."</td>\r";
	print "  </tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left' valign='top' nowrap>" . _DMS_NEW_VERSION . "<BR>\r";
	
	display_spaces(20);

	$first_available_flag = 0;
	$checked = "";
	
	if($init_version_flag == 1) 
		{
		$first_available_flag = SAME;
		$checked = "checked";
		}
		
	print "      <input type='radio' name='rad_new_version_number' onclick='select_version(".SAME.");' ".$checked.">";
	print $version_number->major_version.".".$version_number->minor_version.$version_number->sub_minor_version;
	print "&nbsp;&nbsp;&nbsp(" . _DMS_SAME . ")<BR>\r";
	

	
	$checked = "checked";
	if($init_version_flag == 1) $checked = "";
	
	if ($version_number_inc_sub_minor != 0)
		{
		display_spaces(20);

		print "      <input type='radio' name='rad_new_version_number' onclick='select_version(".INCSUB.");' ".$checked.">";
		print $version_number->major_version.".".$version_number->minor_version.($version_number->sub_minor_version +1 );
		print "<BR>\r";
	
		if($first_available_flag == 0)
			{
			$first_available_flag = INCSUB;
			
			$new_default_major = $version_number->major_version;
			$new_default_minor = $version_number->minor_version;
			$new_default_sub_minor = $version_number->sub_minor_version + 1;
			}
		$checked = "";
		}
  
	if ($version_number_inc_minor != 0)
		{
		display_spaces(20);

		print "      <input type='radio' name='rad_new_version_number' onclick='select_version(".INCMINOR.");' ".$checked.">";
		print $version_number->major_version.".".($version_number->minor_version + 1)."0";
		print "<BR>\r";

		if ($first_available_flag == 0) 
			{
			$first_available_flag = INCMINOR;
			
			$new_default_major = $version_number->major_version;
			$new_default_minor = $version_number->minor_version + 1;
			$new_default_sub_minor = 0;
			}
		$checked = "";
		}
	
	display_spaces(20);
	
	print "      <input type='radio' name='rad_new_version_number' onclick='select_version(".INCMAJOR.");' ".$checked.">";
	print ($version_number->major_version +1 ).".00";
	print "<BR>\r";
	
	if ($first_available_flag == 0)
		{
		$first_available_flag = INCMAJOR;
		
		$new_default_major = $version_number->major_version + 1; 
		$new_default_minor = 0;
		$new_default_sub_minor = 0;
		}
	
	print "  </tr>\r";
	
	// Select File
	
	print "  <tr>\r";
	print "    <td align='left'>" . _DMS_SELECT_FILE . "&nbsp;&nbsp;&nbsp;";
//	print "        <input name='upload_file' type='file'></td>\r";
	print "        <input name='".$temp_file_name."' type='file'></td>\r";
	print "        <input type='hidden' name='hdn_temp_file_name' value='".$temp_file_name."'>\r";
	print "  </tr>\r";

	// Comments
	
	if($dms_config['comments_enable'] == '1')
		{
		$query = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
		$current_version_row_id = $dmsdb->query($query,"current_version_row_id");
	
		$query  = "SELECT row_id, comment FROM ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= " WHERE dov_row_id='".$current_version_row_id."'";
		$result = $dmsdb->query($query,"ROW");
		$num_rows = $dmsdb->getnumrows();
	
		if($perms_level == READONLY) $readonly = "READONLY";
		else $readonly = "";
		
		print "        <tr><td colspan='2' align='left' ".$dms_config['class_content'].">Comments:</td></tr>\r";
		
		print "        <tr><td colspan='2' align='left' ".$dms_config['class_content'].">\r";
		print "          &nbsp;&nbsp;&nbsp;<textarea name='txt_comments' rows='4' cols='80' ".$readonly.">";
		if($num_rows > 0) print $result->comment;
		print "</textarea>\r";
		print "        </td></tr>\r";
		}

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='" . _DMS_CHECKIN . "'>";
	print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"".$return_url."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_current_major_version' value='".$version_number->major_version."'>\r";
	print "<input type='hidden' name='hdn_current_minor_version' value='".$version_number->minor_version."'>\r";
	print "<input type='hidden' name='hdn_current_sub_minor_version' value='".$version_number->sub_minor_version."'>\r"; 
	print "<input type='hidden' name='hdn_new_major_version' value='".(int)$new_default_major."'>\r";
	print "<input type='hidden' name='hdn_new_minor_version' value='".(int)$new_default_minor."'>\r";
	print "<input type='hidden' name='hdn_new_sub_minor_version' value='".(int)$new_default_sub_minor."'>\r";
	print "<input type='hidden' name='hdn_checkin_file_confirm' value='confirm'>\r";
	print "<input type='hidden' name='hdn_time_stamp' value='".time()."'>\r";
	print "<input type='hidden' name='hdn_file_id' value='".$obj_id."'>\r";
	print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "</form>\r";

  	// Set up JavaScript
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "function select_version(selected_version)\r";
	print "{\r";
  
	print "  document.frm_checkin.hdn_new_major_version.value     = document.frm_checkin.hdn_current_major_version.value;\r";
	print "  document.frm_checkin.hdn_new_minor_version.value     = document.frm_checkin.hdn_current_minor_version.value;\r";
	print "  document.frm_checkin.hdn_new_sub_minor_version.value = document.frm_checkin.hdn_current_sub_minor_version.value;\r";

	print "  switch(selected_version)\r";
	print "    {\r";
	print "    case ".SAME.":\r";
	print "      {\r";
	print "      break;\r";
	print "      }\r";

	print "    case ".INCSUB.":\r";
	print "      {\r";
	print "      document.frm_checkin.hdn_new_sub_minor_version.value++;\r";
	print "      break;\r";
	print "      }\r";
  
	print "    case ".INCMINOR.":\r";
	print "      {\r";
	print "      document.frm_checkin.hdn_new_minor_version.value++;\r";
	print "      document.frm_checkin.hdn_new_sub_minor_version.value=0;\r";
	print "      break;\r";
	print "      }\r";

	print "    case ".INCMAJOR.":\r";
	print "      {\r";
	print "      document.frm_checkin.hdn_new_major_version.value++;\r";
	print "      document.frm_checkin.hdn_new_minor_version.value = 0;\r";
	print "      document.frm_checkin.hdn_new_sub_minor_version.value = 0;\r";
	print "      break;\r";
	print "      }\r";

	print "    default:\r";
	print "      {\r";
	print "      }\r";
          
	print "    }\r";
	print "}\r";
	
	// Set the default version number to increment to
	print "  select_version(".$first_available_flag.");\r";
	
	print "</SCRIPT>\r";  
      
	include 'inc_pal_footer.php';
	}
?>
