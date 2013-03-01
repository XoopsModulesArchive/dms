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

// lifecycle_demote.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_lifecycle_functions.php';

// Determine which web page to return to.
$return_url = "";
if ($HTTP_GET_VARS["return_url"])      $return_url = $HTTP_GET_VARS["return_url"];
if ($HTTP_POST_VARS["hdn_return_url"]) $return_url = $HTTP_POST_VARS["hdn_return_url"];
if (strlen($return_url) <= 1)          $return_url = "index.php"; 

if($HTTP_POST_VARS["hdn_function"]) 
	{
	$function = $HTTP_POST_VARS["hdn_function"];
	$file_id = $HTTP_POST_VARS["hdn_file_id"];
	}
else 
	{
	$file_id = $HTTP_GET_VARS["obj_id"];
	}

if ($function == "DEMOTE")
	{
	$new_lifecycle_stage = $HTTP_POST_VARS['slct_demote_lc_stage'];
	
	// get current lifecycle info
	$query  = "SELECT obj_owner, lifecycle_id, lifecycle_stage ";
	$query .= "FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$file_id."' ";
	$result = $dmsdb->query($query,'ROW');
	
	$current_lifecycle_id = $result->lifecycle_id;
	$current_lifecycle_stage = $result->lifecycle_stage;
	$current_object_location = $result->obj_owner;
		
	$query  = "SELECT lifecycle_stage, lifecycle_stage_name, new_obj_location ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$current_lifecycle_id."' AND ";
	$query .= "lifecycle_stage='".$new_lifecycle_stage."'";
	$result = $dmsdb->query($query,"ROW");

	// Update the file properties.
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
	$query .= "SET ";
	$query .= "obj_owner='".$result->new_obj_location."', ";
	$query .= "lifecycle_id='".$current_lifecycle_id."', ";
	$query .= "lifecycle_stage='".$new_lifecycle_stage."' ";
	$query .= "WHERE obj_id='".$file_id."'";
	//print $query;
	
	$dmsdb->query($query);
	
	dms_update_misc_text($file_id);
	
	dms_set_lifecycle_stage_perms($file_id,$current_lifecycle_id,$new_lifecycle_stage);
	
	dms_alpha_move($file_id);
	
	dms_auditing($file_id,"document/lifecycle/demote id=".$current_lifecycle_id.",dest folder=".$new_obj_location);
	
	dms_message("The document has been demoted to a previous lifecycle stage.");
	
	//header("Location:index.php");
	
	// Return to the options screen
	//header("Location:file_options.php?obj_id=".$file_id);
	
	dms_header_redirect("file_options.php?obj_id=".$file_id);
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';
	
	// Get file information
	$query  = "SELECT obj_name, lifecycle_id, lifecycle_stage from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$file_id."'";  
	$result = $dmsdb->query($query,"ROW");
	
	$doc_name = $result->obj_name;
	$lifecycle_id = $result->lifecycle_id;
	$lifecycle_stage = $result->lifecycle_stage;
	
	print "<form method='post' action='lifecycle_demote.php'>\r";
	print "<table width='100%'>\r";
	
	display_dms_header();
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Demote Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>File Name:&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";
	print "      Demote this document to:<BR>\r";
	dms_display_spaces(5);
	print "      <select name='slct_demote_lc_stage'>\r";
	
	$query  = "SELECT lifecycle_stage_name, lifecycle_stage ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
	$query .= "ORDER BY lifecycle_stage";
	$result = $dmsdb->query($query);
	
	while($result_data = $dmsdb->getarray($result))
		{
		if($result_data['lifecycle_stage'] < $lifecycle_stage)
			print "        <option value='".$result_data['lifecycle_stage']."'>".$result_data['lifecycle_stage_name']."</option>\r";
		}
		
	print "      </select>\r";
	print "    </td>\r";
	print "  </tr>\r";
		
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='Demote'>";
	print "                               <input type=button name='btn_cancel' value='Cancel' onclick='location=\"index.php\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_function' value='DEMOTE'>\r";
	print "<input type='hidden' name='hdn_file_id' value='".$file_id."'>\r";
	print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>
