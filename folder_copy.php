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

// folder_copy.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';

if (dms_get_var("hdn_folder_copy") == "confirm")
	{
	$source_folder = dms_get_var("hdn_folder_id");
	$dest_folder = dms_get_var("rad_folder_id");
	
	$location = "folder_options.php?obj_id=".$source_folder;
	
	// Step through all documents in folder and copy them to destination folder.
	$query  = "SELECT obj_id,obj_type FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_owner='".$source_folder."'";
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
  
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			if($result_data['obj_type'] == 0)
				{
				$new_obj_id = dms_file_copy($result_data['obj_id'],$dest_folder);
				}
			}
		}
	
	dms_auditing(dms_get_var("hdn_folder_id"),"folder/copy/dest folder id=".$dest_folder);

	dms_message("The contents of the folder has been copied to the selected destination folder.");
	
	//header("Location:".$location);
	
	dms_header_redirect($location);
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';
	
	if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
	else $obj_id = $HTTP_GET_VARS['obj_id'];
	
	$obj_id = dms_get_var("hdn_obj_id");
	if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");
	
	// Permissions required to access this page:
	//  EDIT, OWNER
	$perms_level = dms_perms_level($obj_id);
	
	if ( ($perms_level != 3) && ($perms_level != 4) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}
	
	$location="file_archive.php";
		
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$folder_name = $dmsdb->query($query,'obj_name');
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_select_dest' action='folder_copy.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Archive Folder</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>Folder:&nbsp;&nbsp;&nbsp;";
	print "        ".$folder_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";
	
	include "inc_folder_select.php";
	
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <td colspan='2' align='left'><input type=button name='btn_submit' value='Copy' onclick='check_for_dest();'>";
	print "                               <input type=button name='btn_cancel' value='Cancel' onclick='location=\"folder_options.php?obj_id=".$obj_id."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_folder_copy' value='confim'>\r";
	print "<input type='hidden' name='hdn_folder_id' value='".$obj_id."'>\r";
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>



