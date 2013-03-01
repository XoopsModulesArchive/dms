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

// file_checkout_cancel.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';


// Determine which web page to return to.
$return_url = dms_get_var("return_url");
if($return_url == FALSE) $return_url = dms_get_var("hdn_return_url");
if($return_url == FALSE) $return_url = "index.php"; 

if (dms_get_var("hdn_checkout_cancel_confirm") == "confirm")
	{
	$query =  "UPDATE ".$xoopsDB->prefix("dms_objects")." ";
	$query .= "SET obj_status='".NORMAL."',obj_checked_out_user_id='0' WHERE obj_id='".dms_get_var("hdn_file_id")."'";
	$xoopsDB->query($query);
  
	dms_auditing(dms_get_var("hdn_file_id"),"document/cancel checkout");
 
	if($dms_config['sub_email_enable']=='1')
		{
		$query  = "SELECT obj_name FROM ".$xoopsDB->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".dms_get_var('hdn_file_id')."'";  
		$doc_name = $dmsdb->query($query,'obj_name');

		$user_name = $xoopsUser->getUnameFromId($dms_user_id);
		
		$message  = "The check-out of a document has been cancelled in the DMS:<BR><BR>";
		$message .= "&nbsp;&nbsp;Document:&nbsp;&nbsp;".$doc_name."<BR>";
		$message .= "&nbsp;&nbsp;User:&nbsp;&nbsp;".$user_name."<BR>";
		
		dms_email_subscribers(dms_get_var('hdn_file_id'),$message);
		}
   
	dms_folder_subscriptions(dms_get_var('hdn_file_id'));
	
	// Add to the document history
	dms_doc_history(dms_get_var('hdn_file_id'));
	
	//header("Location:".$return_url);
	dms_header_redirect($return_url);
	
	exit(0);
	}
else
	{
	// Permissions required to access this page:
	//  EDIT, OWNER
	$perms_level = dms_perms_level(dms_get_var('obj_id'));
	
	if ( ($perms_level != 3) && ($perms_level != 4) )
		{
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		end();
		}
	
	include 'inc_pal_header.php';
	
	// Get file information
	$query  = "SELECT obj_name from ".$xoopsDB->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".dms_get_var("obj_id")."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "<form method='post' action='file_checkout_cancel.php'>\r";
	print "<table width='100%'>\r";
	//print "  <tr><td colspan='2' class='cHeader'><center><b><font size='2'>Title Goes Here</font></b></center></td></tr>\r";
	display_dms_header();
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>" . _DMS_CONFIRM_CANCEL_CHECKOUT . "</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>" . _DMS_FILE_NAME . "&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='" . _DMS_CANCEL_CHECKOUT . "'>";
	print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"".$return_url."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_checkout_cancel_confirm' value='confirm'>\r";
	print "<input type='hidden' name='hdn_file_id' value='".dms_get_var("obj_id")."'>\r";
	print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "</form>\r";
	
	include 'inc_pal_footer.php';
	}
?>
