<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 7/14/2004                                //
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

// Main Menu
// lifecycle_manager.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
dms_get_config();

$row_id = dms_get_var("row_id");

/*
if($HTTP_POST_VARS["row_id"]) $row_id = $HTTP_POST_VARS["row_id"];
else $row_id = $HTTP_GET_VARS["row_id"];
*/
include XOOPS_ROOT_PATH.'/header.php';
 
// Get audit information
$query  = "SELECT * FROM ".$dmsdb->prefix("dms_audit_log")." ";
$query .= "WHERE row_id='".$row_id."'";
$audit_info = $dmsdb->query($query,"ROW");
 
// Get object information
$query  = "SELECT obj_name,obj_type FROM ".$dmsdb->prefix("dms_objects")." ";
$query .= "WHERE obj_id='".$audit_info->obj_id."'";  
$obj_info = $dmsdb->query($query,"ROW");

$obj_id = $audit_info->obj_id;
$obj_name = $obj_info->obj_name;

if(0 == strlen($obj_name)) $obj_name = $audit_info->obj_name;

print "<form method='post' name='frm_audit_log_obj' action='audit_log_obj.php'>\r";
print "<table width='100%'>\r";
  
//  display_dms_header();
  
print "  <tr>\r";
  
// Content
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$class_header.">\r";
print "            <center><b><font size='2'>" . _DMS_AUDITING . "</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";
  
print "      <BR>\r";

print "      <table width='100%' cellspacing='4' cellpadding='0'>\r";
print "        <tr>\r";
print "          <td align='left' ".$class_content.">\r";
print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"index.php\";'>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";

print "        <tr><td align='left'>\r";
print "          <b>Date & Time</b>  ".strftime("%d-%B-%Y %I:%M%p",$audit_info->time_stamp)."\r";
print "        </td></tr>\r";

print "        <tr><td><BR></td></tr>\r";

print "        <tr><td align='left'>\r";  
  
if($obj_info->obj_type == FILE) print "          <b>Document Name:</b>  <a href=\"audit_log_obj.php?obj_id=".$obj_id."\">".$obj_name."</a>\r";
else                         print "          <b>Folder Name:</b>  <a href=\"audit_log_obj.php?obj_id=".$obj_id."\">".$obj_name."</a>\r";
  
print "        </td></tr>\r";

print "        <tr><td align='left'>\r";
print "          <b>User Name:</b>  <a href='audit_log_user.php?user_id=".$audit_info->user_id."'>".$xoopsUser->getUnameFromId($audit_info->user_id)."</a>\r";
print "        </td></tr>\r";
  
print "        <tr><td><BR></td></tr>\r";

print "        <tr><td align='left'>\r";
print "          <b>Description:</b>  ".$audit_info->descript."\r";
print "        </td></tr>\r";

if($audit_info->descript == "document/route")
	{

	print "        <tr><td><BR></td></tr>\r";

	print "        <tr><td><b>Document Has Been Routed To The Following Inbox(es):</b></td></tr>\r";
	
	$query = "SELECT obj_owner FROM ".$dmsdb->prefix("dms_objects")." WHERE ptr_obj_id='".$obj_id."' AND obj_type='".DOCLINK."'";
	$inboxes = $dmsdb->query($query);
	
	$index = 0;
	while($indiv_inbox = $dmsdb->getarray($inboxes))
		{
		$query = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$indiv_inbox['obj_owner']."'";
		$inbox_names[$index] = $dmsdb->query($query,"obj_name");
		$index++;
		//print "        <tr><td>&nbsp;&nbsp;&nbsp;".$inbox_name."</td></tr>\r";
		}
	
	asort($inbox_names);
	
	$index = 0;
	while($inbox_names[$index])
		{
		print "        <tr><td>&nbsp;&nbsp;&nbsp;".$inbox_names[$index]."</td></tr>\r";
		$index++;
		}
	}

print "      </table>\r";  
    

print "    </td>\r";
  
print "  </tr>\r";
print "</table>\r";
  
print "<input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
    
print "</form>\r";
  
include_once XOOPS_ROOT_PATH.'/footer.php';
  
?>
