<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 9/10/2007                                //
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

$obj_id = dms_get_var("obj_id");
 
// Get all of the users and their id's
$user_list = array();
$user_list = $dms_users->list_all();

function display_objects($obj_id)
	{
	global $dmsdb, $dms_config, $user_list;

	// Get the objects and auditing data for the specified folder.
	$query  = "SELECT do.obj_id,do.obj_name,do.obj_type,do.obj_owner,";
	$query .= "dal.row_id,dal.time_stamp,dal.user_id,dal.obj_id,dal.descript FROM ".$dmsdb->prefix("dms_objects")." AS do ";
	$query .= "INNER JOIN ".$dmsdb->prefix("dms_audit_log")." AS dal ";
	$query .= "ON do.obj_id=dal.obj_id ";
	$query .= "WHERE obj_owner='".$obj_id."' AND (obj_type='".FILE."' OR obj_type = '".FOLDER."') ORDER BY dal.time_stamp";
	
	//  $result = mysql_query($query) or die(mysql_error());
	
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			print "        <tr>\r";
			print "          <td align='left' ".$dms_config['class_content']." width='25%'>\r";
			print "            <a href=\"audit_log_detail.php?row_id=".$result_data['row_id']."\">".strftime("%d-%B-%Y %I:%M%p",$result_data['time_stamp'])."</a>\r";
			print "          </td>\r";
		
			print "          <td align='left' ".$dms_config['class_content']." width='25%'>\r";
			print "            <a href='audit_log_obj.php?obj_id=".$result_data['obj_id']."'>".$result_data['obj_name']."</a>\r";
			print "          </td>\r";
		
			print "          <td align='left' ".$dms_config['class_content']." width='25%'>\r";
			print "            <a href='audit_log_user.php?user_id=".$result_data['user_id']."'>".$user_list[$result_data['user_id']]."</a>\r";
			print "          </td>\r";
		
			print "          <td align='left' ".$dms_config['class_content'].">\r";
			print "            ".$result_data['descript']."\r";
			print "          </td>\r";
		
			print "        </tr>\r";

			if($result_data['obj_type'] == FOLDER) display_objects($result_data['obj_id']);
			}
		}
	}


include XOOPS_ROOT_PATH.'/header.php';
   
// Get object information
$query  = "SELECT obj_name,obj_type from ".$dmsdb->prefix("dms_objects")." ";
$query .= "WHERE obj_id='".$obj_id."'";  
$result = $dmsdb->query($query,"ROW");

print "<form method='post' name='frm_audit_log_obj' action='audit_log_obj.php'>\r";
print "<table width='100%'>\r";
  
//  display_dms_header();
  
print "  <tr>\r";
 
// Content
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>" . _DMS_AUDITING . "</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table width='100%' cellspacing='4' cellpadding='0'>\r";
print "        <tr>\r";
 
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"audit_log_obj.php?obj_id=".$obj_id."\";'>\r";
print "          </td>\r";

print "        </tr>\r";
print "      </table>\r";

  
print "      <BR>\r";

print "      <table>\r";
print "        <tr><td align='left'>\r";  
print "          <b>" . _DMS_FOLDER_NAME . "</b>  ".$result->obj_name."\r";
print "        </td></tr>\r";
print "        <tr><td><BR></td></tr>\r";
print "      </table>\r";  

print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_subheader'].">\r";
print "            <b>Audit Log--Folder Tree</b>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";
 
print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <b>" . _DMS_DATE_AND_TIME . "</b>\r";
print "          </td>\r";

print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <b>Object Name</b>\r";
print "          </td>\r";


print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <b>User Name</b>\r";
print "          </td>\r";
    
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <b>" . _DMS_DESCRIPTION . "</b>\r";
print "          </td>\r";
  
print "        </tr>\r";

display_objects($obj_id);

print "      </table>\r";
print "    </td>\r";
  
print "  </tr>\r";
print "</table>\r";

print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';
  
?>
