<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2006                                     //
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

// file_recall.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';

import_request_variables("P","post_");


if (dms_get_var("hdn_file_recall") == "confirm")
	{
	$obj_id = dms_get_var("hdn_obj_id");
	$location = "file_options.php?obj_id=".$obj_id;
	
	$index = 0;
	while(isset($post_slct_recall_doc_ids[$index]))
		{
		$obj_id = $post_slct_recall_doc_ids[$index];
		
		// Get the obj_id of the inbox
		$query = "SELECT obj_owner FROM ".$dmsdb->prefix('dms_objects')." WHERE obj_id='".$obj_id."'";
		$obj_owner = $dmsdb->query($query,"obj_owner");
			
		// Delete the link
		$query = "DELETE from ".$dmsdb->prefix('dms_objects')." WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
	
		$query = "DELETE from ".$dmsdb->prefix('dms_object_perms')." WHERE ptr_obj_id='".$obj_id."'";
		$dmsdb->query($query);
	
		$query = "DELETE from ".$dmsdb->prefix('dms_routing_data')." WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
	
		dms_set_inbox_status($obj_owner);
		
		$index++;
		}
	
	dms_message("The document has been recalled.");
	
	dms_header_redirect($location);
	exit(0);
	}
else
	{
	include 'inc_pal_header.php';
	
//	if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
//	else $obj_id = $HTTP_GET_VARS['obj_id'];
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
	
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_recall_r_docs' action='file_recall.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Recall Routed Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>&nbsp;&nbsp;&nbsp;Document Name:&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	// Get the object ID's and inbox(es) that the document has been routed to...
	$query  = "SELECT o.obj_id,o.obj_owner FROM ".$dmsdb->prefix("dms_objects")." AS o ";
	$query .= "INNER JOIN ".$dmsdb->prefix("dms_routing_data")." AS rd ON rd.obj_id = o.obj_id ";
	$query .= "WHERE ptr_obj_id = '".$obj_id."' AND obj_type = '".DOCLINK."' AND source_user_id = '".$dms_user_id."'";
	$routed_docs = $dmsdb->query($query);
	
	$index = 0;
	while($indiv_doc = $dmsdb->getarray($routed_docs))
		{
		$query = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$indiv_doc['obj_owner']."'";
		$inbox_names[$index] = $dmsdb->query($query,"obj_name");
		
		$routed_doc_obj_id[$index] = $indiv_doc['obj_id'];
		
		$index++;
		//print "        <tr><td>&nbsp;&nbsp;&nbsp;".$inbox_name."</td></tr>\r";
		}
	
	asort($inbox_names);

	print "  <tr><td colspan='2'>\r";
	
	print "    &nbsp;&nbsp;&nbsp;Inbox(es):<BR>\r";
	print "    &nbsp;&nbsp;&nbsp;<select size='10' name='slct_recall_doc_ids[]' multiple>\r";
	
	foreach ($inbox_names as $index => $indiv_inbox_name)	
		{
		print "        <option value='".$routed_doc_obj_id[$index]."'>".$indiv_inbox_name."</option>\r";
		}

	print "    </select>\r";
	print "  </td></tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <td colspan='2' align='left'><input type='submit' name='btn_submit' value='Recall'>";
	print "                               <input type='button' name='btn_cancel' value='Cancel' onclick='location=\"file_options.php?obj_id=".$obj_id."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_file_recall' value='confirm'>\r";
	print "<input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
	print "</form>\r";
	
	include_once 'inc_pal_footer.php';
	}
?>



