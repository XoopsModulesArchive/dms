<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2007                                     //
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

// perms_xfer_ownership.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

function list_users($select_box_name)
	{
	global $dmsdb, $dms_tab_index;
  
	$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." ORDER BY uname";
	$result = $dmsdb->query($query);
  
	print "<select name='".$select_box_name."' tabindex='".$dms_tab_index++."'>\r";
	while($result_data = $dmsdb->getarray($result))
		{
		print "<option value='".$result_data['uid']."' ";
//		if ($current_owner_id == $result_data['uid']) print "selected";
		print ">".$result_data['uname']."</option>";
		}
	print "</select>\r";
	}


if (dms_get_var("hdn_xfer_ownership") == "confirm")
	{
	$query  = "UPDATE ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "SET user_id='".dms_get_var("hdn_to_uname")."' ";
	$query .= "WHERE user_id='".dms_get_var("hdn_from_uname")."' AND user_perms='".OWNER."'";
	$dmsdb->query($query);
	
	if(dms_get_var_chk("chk_del_on_all")==1)
		{
		$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "WHERE user_id='".dms_get_var("hdn_from_uname")."'";
		$dmsdb->query($query);
		}
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='perms_xfer_ownership.php';");
	print("</SCRIPT>");
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';
	//$location="perms_xfer_ownership.php";  
	
	if($dms_admin_flag == 0)
		{  
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		}  
	
	//$file_id=$HTTP_GET_VARS["file_id"];
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_xfer_ownership' action='perms_xfer_ownership.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Globally Transfer Ownership Permissions:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
	print "      Old User Account:&nbsp;&nbsp;";
	list_users("hdn_from_uname");
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
	print "      New User Account:&nbsp;&nbsp;";
	list_users("hdn_to_uname");
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
	print "      Delete User Permissions On All Documents and Folders:&nbsp;&nbsp;";
	print "      <input name='chk_del_on_all' type='checkbox'>\r";
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
		
	print "  <td colspan='2' align='left'><input type='submit' name='btn_submit' value='Transfer' tabindex='".$dms_tab_index++."'>";
	print "                               <input type='button' name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex='".$dms_tab_index++."'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_xfer_ownership' value='confirm'>\r";
	print "</form>\r";
	

	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>



