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

// file_move.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';

$obj_id = dms_get_var("obj_id");

$hdn_link_move = dms_get_var("hdn_link_move");
if ($hdn_link_move == "confirm")
	{
	$dest_folder_id = dms_get_var('rad_folder_id');
	
	$location = "link_options.php?obj_id=".$obj_id;
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
	$query .= "SET ";
	$query .= "obj_owner='".$dest_folder_id."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);

	dms_auditing($obj_id,"link/move/dest folder id=".$dest_folder_id);

	dms_folder_subscriptions($obj_id);

	// Check to see if an automatic lifecycle exists for this document.  If it exists, apply it.
	/*
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id=".$dest_folder_id." AND data_type='".FOLDER_AUTO_LIFECYCLE_NUM."'";
	$folder_auto_lifecycle_num = $dmsdb->query($query,'data');
	*/
	
	//if($dmsdb->getnumrows() == 1) dms_apply_lifecycle($obj_id,$folder_auto_lifecycle_num);
	
	dms_set_inbox_status(dms_get_var("hdn_obj_owner") );
	
	dms_message("The routed document has been moved to the selected destination folder.");
	
	//header("Location:".$location);
	dms_header_redirect($location);
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';
	
	// Permissions required to access this page:
	//  EDIT, OWNER
//	$perms_level = dms_perms_level($obj_id);
	
/*
	if ( ($perms_level != 3) && ($perms_level != 4) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}
*/
		
	$location="link_move.php";
	
	
	// Get actual object ID
	$query  = "SELECT ptr_obj_id,obj_owner from ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$result = $dmsdb->query($query,'ROW');
	$obj_owner = $result->obj_owner;
	
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$result->ptr_obj_id."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_select_dest' action='link_move.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Move Routed Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>Name:&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";
	
	include "inc_folder_select.php";
	
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <td colspan='2' align='left'><input type=button name='btn_submit' value='" . _DMS_MOVE . "' onclick='check_for_dest();'>";
	print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"link_options.php?obj_id=".$obj_id."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_link_move' value='confirm'>\r";
	print "<input type='hidden' name='obj_id' value='".$obj_id."'>\r";
	print "<input type='hidden' name='hdn_destination_folder_id' value=''>\r";
	print "<input type='hidden' name='hdn_obj_owner' value='".$obj_owner."'>\r";
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>



