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

import_request_variables("P","post_");

// If this function is not enabled, return to index.php
if($dms_config['document_email_enable']=='0')
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	//print("location='index.php';");
	print("</SCRIPT>");  
	exit(0);
	}

// Determine which web page to return to.
/*
$return_url = "";
if ($HTTP_GET_VARS["return_url"])      $return_url = $HTTP_GET_VARS["return_url"];
if ($HTTP_POST_VARS["hdn_return_url"]) $return_url = $HTTP_POST_VARS["hdn_return_url"];
if (strlen($return_url) <= 1)          $return_url = "index.php"; 
*/

$return_url = dms_get_var("return_url");
if($return_url == FALSE) $return_url = dms_get_var("hdn_return_url");
if($return_url == FALSE) $return_url = "index.php";

if (dms_get_var("hdn_file_email") == "confirm")
	{
	// Permissions required to email document:
	//  READ-ONLY, EDIT, OWNER
	$obj_id = dms_get_var("hdn_obj_id");
	
	$perms_level = dms_perms_level($obj_id);
	
	if ( ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
		{
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		end();
		}
	
	if(dms_get_var("hdn_obj_id") != FALSE)
		{
		
		dms_auditing($HTTP_POST_VARS["hdn_obj_id"],"document/email: ".$HTTP_POST_VARS['txt_to']);
		
		$subject = "DMS Document";
		
		$message  = "Document Name:  ".$HTTP_POST_VARS['hdn_obj_name']."<BR>";
		$message .= "Sent By:  ".$xoopsUser->getUnameFromId($dms_user_id)."<BR>";
		
		dms_send_email(dms_get_var("txt_to"),$dms_config['document_email_from'],$dms_config['document_email_subject'],$message,$obj_id);
		}

	$index = 0;
	while(isset($post_slct_user[$index]))
		{
		$dest_email_addr = $dms_users->get_email_addr($post_slct_user[$index]);
		
		dms_auditing(dms_get_var("hdn_obj_id"),"document/email: ".$dest_email_addr);
		
		$subject = "DMS Document";
		
		$message  = "Document Name:  ".dms_get_var("hdn_obj_name")."<BR>";
		$message .= "Sent By:  ".$xoopsUser->getUnameFromId($dms_user_id)."<BR>";
		
		dms_send_email($dest_email_addr,$dms_config['document_email_from'],$dms_config['document_email_subject'],$message,$obj_id);
		
		$index++;
		}
		
	dms_redirect($return_url);
	exit(0);
	}
else
	{
	// Permissions required to access this page:
	//  READ-ONLY, EDIT, OWNER
	$perms_level = dms_perms_level($HTTP_GET_VARS['obj_id']);
	
	if ( ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
		{
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		end();
		}
	
	include XOOPS_ROOT_PATH.'/header.php';
	
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$HTTP_GET_VARS["obj_id"]."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "<form name='frm_email' method='post' action='file_email.php'>\r";
	print "<table width='100%'>\r";
	//print "  <tr><td colspan='2' class='cHeader'><center><b><font size='2'>Title Goes Here</font></b></center></td></tr>\r";
	display_dms_header();
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>" . _DMS_EMAIL_DOC . "</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>" . _DMS_FILE_NAME . "&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>Select the recipient and/or enter their e-mail address below:<BR><BR>\r";
	//print "    <td colspan='2' align='left'><BR>Select a recipient or enter the e-mail address of the recipient below:<BR><BR>\r";
	
	
	$mlist= array();
	$mlist = $dms_groups->usr_list_all(); //($selected_group);
		
	// Sort $mlist alphabetically
	asort($mlist);
	reset($mlist);
	
	dms_display_spaces(6);
	print "        Recipient:<BR>";
	dms_display_spaces(9);
	$perms_select_width = " style='width: 60mm;' ";
	print "        <select name='slct_user[]' size='10' ".$perms_select_width." multiple>\r";
	
	foreach ($mlist as $u_id => $u_name)
		{
		print "          <option value='".$u_id."'>".$u_name."</option>\r";
		}  
		
	print "        </select>\r";
	
	print "        <BR><BR>\r";

	dms_display_spaces(6);
	print "        E-mail Address of Recipient:<BR>\r";
	dms_display_spaces(9);
	print "        <input type='text' name='txt_to' maxlength='60' size='30'>\r";

	
	
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='Send'>";
	print "                               <input type=button name='btn_cancel' value='Cancel' onclick='location=\"".$return_url."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_file_email' value='confirm'>\r";
	print "<input type='hidden' name='hdn_obj_id' value='".$HTTP_GET_VARS["obj_id"]."'>\r";
	print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "<input type='hidden' name='hdn_obj_name' value='".$doc_name."'>\r";
	
/*
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "  function check_input()\r";
	print "    {\r";
	print "    if (document.frm_email.txt_to.value.length < 1)\r";
	print "      {\r";
	print "      alert(\"Please enter the e-mail address to send this document to.\");\r";
	print "      document.frm_email.txt_to.focus();\r";
	print "      }\r";
	print "    else document.frm_email.submit();\r";
	print "    }\r";
	print "</SCRIPT>";  
*/
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>
