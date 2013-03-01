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

// file_checkout.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

// Determine which web page to return to.
$return_url = "index.php";


// Permissions required to access this page:
//  ADMIN

if ( !($xoopsUser->IsAdmin() ) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}
  
include XOOPS_ROOT_PATH.'/header.php';
  

print "<form method='post' action='audit_log_user.php'>\r";
print "<table width='100%'>\r";
  
print "  <tr><td colspan='2' ".$class_header."><center><b><font size='2'>" . _DMS_AUDIT_BY_USER . "</font></b></center></td></tr>\r";

  
print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <tr><td colspan='2' align='left'><b>" . _DMS_SELECT_USER . "</b></td></tr>\r";
print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <tr>\r";
print "    <td colspan='2' align='left'>" . _DMS_USER_NAME . "&nbsp;&nbsp;&nbsp;";


$query = "SELECT uid,uname from ".$xoopsDB->prefix("users")." ORDER BY uname";
$result = $dmsdb->query($query);

  
print "<select name='user_id'>\r";
while($result_data = $dmsdb->getarray($result))
	{
	print "<option value='".$result_data['uid']."' ";
	print ">".$result_data['uname']."</option>";
	}
print "</select>\r";


print "    </td>\r";
print "  </tr>\r";
print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='" . _DMS_SUBMIT . "'>";
print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"".$return_url."\";'></td>\r";
print "</table>\r";
print "<input type='hidden' name='hdn_checkout_file_confirm' value='confim'>\r";
//print "<input type='hidden' name='hdn_file_id' value='".$HTTP_GET_VARS["obj_id"]."'>\r";
//print "<input type='hidden' name='hdn_file_name' value='".mysql_result($first_result,'obj_name')."'>\r";
//print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
print "</form>\r";
  
include_once XOOPS_ROOT_PATH.'/footer.php';
  
?>
