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

// inc_notify_set.php

include_once 'inc_defines.php';

/*
//The following lines must be at the beginning of any file using inc_notify_set.php:
//NOTE:  If inc_perms_set.php is also used, do not use these lines of code...it will be redundant.
import_request_variables("P","post_");
$this_file = "";  // Add the filename of this file here.

if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
else $obj_id = $HTTP_GET_VARS['obj_id'];

*/

$notify_ar_button_width = " style='width: 5em;' ";
$notify_select_width = " style='width: 45mm;' ";

print "<SCRIPT LANGUAGE='Javascript'>\r";

print "  function add_group()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_notify.slct_group_none.options[document.frm_notify.slct_group_none.selectedIndex].text;\r";
print "    value = document.frm_notify.slct_group_none.options[document.frm_notify.slct_group_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_notify.elements['slct_group[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_notify.elements['slct_group[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_notify.elements['slct_group[]'].options[document.frm_notify.elements['slct_group[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_group()\r";
print "    {\r";
print "    if (document.frm_notify.elements['slct_group[]'].selectedIndex >= 0)\r";
print "     document.frm_notify.elements['slct_group[]'].options[document.frm_notify.elements['slct_group[]'].selectedIndex] = null;\r";
print "    }\r";



print "  function add_user()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_notify.slct_user_none.options[document.frm_notify.slct_user_none.selectedIndex].text;\r";
print "    value = document.frm_notify.slct_user_none.options[document.frm_notify.slct_user_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_notify.elements['slct_user[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_notify.elements['slct_user[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_notify.elements['slct_user[]'].options[document.frm_notify.elements['slct_user[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_user()\r";
print "    {\r";
print "    if (document.frm_notify.elements['slct_user[]'].selectedIndex >= 0)\r";
print "     document.frm_notify.elements['slct_user[]'].options[document.frm_notify.elements['slct_user[]'].selectedIndex] = null;\r";
print "    }\r";

/*
print "  function notify_change_user_group()\r";
print "    {\r";
print "    document.frm_notify.hdn_notify_change_users_group.value='TRUE';\r";
print "    update_notify();\r";
print "    }\r";
*/
print "  function update_notify()\r";
print "    {\r";
print "    var index;\r";
print "    for ( index = 0; index < document.frm_notify.elements['slct_group[]'].length; index++)\r";
print "      {\r";
print "      document.frm_notify.elements['slct_group[]'].options[index].selected = 'TRUE';\r";
print "      }\r";

print "    for ( index = 0; index < document.frm_notify.elements['slct_user[]'].length; index++)\r";
print "      {\r";
print "      document.frm_notify.elements['slct_user[]'].options[index].selected = 'TRUE';\r";
print "      }\r";

print "    document.frm_notify.hdn_update_notify.value = 'TRUE';\r";
print "    document.frm_notify.submit();\r";
print "    }\r";

print "</SCRIPT>\r";  

if( !($obj_id > 0)  )   // If a valid $obj_id doesn't exist, then look for one.
	{
	if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
	else $obj_id = $HTTP_GET_VARS['obj_id'];
	}
	
if ($HTTP_POST_VARS["hdn_update_notify"] == "TRUE")
	{
	// Delete all notifications for this object
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_notify")." ";
	$query .= "WHERE obj_id='".$obj_id."'";
//print $query;
	$dmsdb->query($query);

	// Add groups notifications
	$index = 0;
	while ( strlen($post_slct_group[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_notify")." ";
		$query .= "(obj_id,group_id) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_group[$index]."')";
//print $query;
		$dmsdb->query($query);
	
		$index++;
		}

	  
	// Add users notifications
	$index = 0;
	while ( strlen($post_slct_user[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_notify")." ";
		$query .= "(obj_id,user_id) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_user[$index]."')";
		$dmsdb->query($query);
	
		$index++;
		}
 
	dms_auditing($obj_id,"folder/update notifications");
	}

/*
if ($HTTP_POST_VARS["hdn_notify_change_users_group"] == "TRUE")
	{
	$selected_group = $HTTP_POST_VARS['slct_user_groups'];
	}
else $selected_group = 1;
*/

// Get notification settings
$query  = "SELECT user_id, group_id FROM ".$dmsdb->prefix("dms_notify")." ";
$query .= "WHERE obj_id='".$obj_id."'";

$notify = $dmsdb->query($query);
  
$notify_row_count = $dmsdb->getnumrows();

$notify_user_id        = array();
$notify_user_name      = array();
$notify_group_id       = array();
$notify_group_name     = array();

$group_array_index = 0;
$user_array_index = 0;
    
while($notify_data = $dmsdb->getarray($notify))
	{
	// Determine User Permissions
	if ($notify_data['user_id'] > 0)
    	{
		$notify_user_id[$user_array_index] = $notify_data['user_id'];
		$notify_user_name[$user_array_index] = $xoopsUser->getUnameFromID($notify_user_id[$user_array_index]);
		
		$user_array_index++;
	}

	// Determine Group Permissions
	if ($notify_data['group_id'] > 0)
		{
		$notify_group_id[$group_array_index] = $notify_data['group_id'];

		$group_details = $dms_groups->grp_details($notify_group_id[$group_array_index]);
		$notify_group_name[$group_array_index] = $group_details['name']; 
		  
		$group_array_index++;
		}
	}

// Sort the users and groups by name
asort($notify_user_name);
asort($notify_group_name);
reset($notify_user_name);
reset($notify_group_name);

print "      <table width='100%' border='0'>\r";
  
print "      <form name='frm_notify' action='".$this_file."#notify_set' method='post'>\r";

print "        <tr><td colspan='5' align='left' ".$class_subheader.">&nbsp;Folder Subscriptions</td></tr>\r";

print "        <tr><td colspan='5' align='left'><BR></td></tr>\r";

print "        <tr><td colspan='5' align='left' valign='top'>&nbsp;&nbsp;&nbsp;Groups:</td></tr>\r";
print "        <tr><td colspan='1' align='left' valign='top'>\r";
print "              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "              <select name='slct_group_none' size='10' ".$notify_select_width.">\r";

// Code taken from Xoops /xoops/modules/system/admin/groups/groups.php and modified.
$group_list = array();

$group_list = $dms_groups->grp_list_all();
	
asort($group_list);
reset($group_list);
  
foreach ($group_list as $g_id => $g_name)
	{
	print "              <option value='".$g_id."'>".$g_name."</option>\r";
	}  
	  
print "              </select>\r";
print "            </td>\r";
print "            <td colspan='1' width='2%'><BR></td>\r";
print "            <td colspan='1' align='left' valign='top'>\r";
print "              <BR>";
print "              <select name='slct_group[]' size='10' multiple ".$notify_select_width.">\r";
      
foreach ($notify_group_name as $index => $g_name)
	{
	print "              <option value='".$notify_group_id[$index]."'>".$g_name."</option>\r";
	}  

print "              </select><BR>\r";
print "              <input type='button' value='Add' ".$notify_ar_button_width." onclick='add_group();'><BR>";
print "              <input type='button' value='Remove' ".$notify_ar_button_width." onclick='remove_group();'>";
print "            </td>\r";
print "        </tr>";

print "        <tr><td colspan='5'><BR></td></tr>\r";

$mlist= array();
$mlist = $dms_groups->usr_list_all();
	
// Sort $mlist alphabetically
asort($mlist);
reset($mlist);

print "        <tr><td colspan='5' align='left' valign='top'>&nbsp;&nbsp;&nbsp;" . _DMS_USERS . "</td></tr>\r";
print "        <tr><td colspan='1' valign='top' align='left'>\r";
print "              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "              <select name='slct_user_none' size='10' ".$notify_select_width.">\r";
    
foreach ($mlist as $u_id => $u_name)
	{
	print "        <option value='".$u_id."'>".$u_name."</option>\r";
	}  
	
print "              </select>\r";
/*
print "              <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "              <select name='slct_user_groups' onchange='notify_change_user_group();'>\r";

asort($group_list);
reset($group_list);

foreach ($group_list as $g_id => $g_name)
	{
	if ($g_id == $selected_group) $selected_text = "selected";
	else $selected_text = "";
	
	print "              <option value='".$g_id."' ".$selected_text.">".$g_name."</option>\r";
	}  
  
print "              </select>\r";
*/
print "            </td>\r";
print "            <td colspan='1' width='2%'><BR></td>\r";
print "            <td colspan='1' align='left' valign='top'>\r";
print "              <BR>";
print "              <select name='slct_user[]' size='10' multiple ".$notify_select_width.">\r";

foreach ($notify_user_name as $index => $u_name)
	{
	print "        <option value='".$notify_user_id[$index]."'>".$u_name."</option>\r";
	}  
  
print "              </select><BR>\r";
print "              <input type='button' value='Add' ".$notify_ar_button_width." onclick='add_user();'><BR>";
print "              <input type='button' value='Remove' ".$notify_ar_button_width." onclick='remove_user();'>\r";
print "            </td>\r";
print "        </tr>\r";

print "        <tr><td colspan=5><BR></td></tr>\r";
print "        <tr><td colspan=5 align='left'>&nbsp;&nbsp;&nbsp;<input type='button' name='btn_update_perms' value='Update Folder Subscriptions' onclick='update_notify();'></td></tr>\r";
print "        <input type='hidden' name='hdn_update_notify' value='FALSE'>\r";
print "        <input type='hidden' name='hdn_notify_change_users_group' value='FALSE'>\r";
print "        <input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
print "        </form>\r";

print "      </table>\r";

?>
