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

// inc_perms_set.php

include_once 'inc_defines.php';

/*
//The following lines must be at the beginning of any file using inc_permissions.php:

import_request_variables("P","post_");
$this_file = "";  // Add the filename of this file here.

if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
else $obj_id = $HTTP_GET_VARS['obj_id'];

*/

$ar_button_width = " style='width: 5em;' ";
$perms_select_width = " style='width: 45mm;' ";

print "<SCRIPT LANGUAGE='Javascript'>\r";

print "  function add_group_ro()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_perms.slct_group_none.options[document.frm_perms.slct_group_none.selectedIndex].text;\r";
print "    value = document.frm_perms.slct_group_none.options[document.frm_perms.slct_group_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_group_ro[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_perms.elements['slct_group_ro[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_perms.elements['slct_group_ro[]'].options[document.frm_perms.elements['slct_group_ro[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_group_ro()\r";
print "    {\r";
print "    if (document.frm_perms.elements['slct_group_ro[]'].selectedIndex >= 0)\r";
print "     document.frm_perms.elements['slct_group_ro[]'].options[document.frm_perms.elements['slct_group_ro[]'].selectedIndex] = null;\r";
print "    }\r";

print "  function add_group_e()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_perms.slct_group_none.options[document.frm_perms.slct_group_none.selectedIndex].text;\r";
print "    value = document.frm_perms.slct_group_none.options[document.frm_perms.slct_group_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_group_e[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_perms.elements['slct_group_e[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_perms.elements['slct_group_e[]'].options[document.frm_perms.elements['slct_group_e[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_group_e()\r";
print "    {\r";
print "    if (document.frm_perms.elements['slct_group_e[]'].selectedIndex >= 0)\r";
print "     document.frm_perms.elements['slct_group_e[]'].options[document.frm_perms.elements['slct_group_e[]'].selectedIndex] = null;\r";
print "    }\r";

print "  function add_user_ro()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_perms.slct_user_none.options[document.frm_perms.slct_user_none.selectedIndex].text;\r";
print "    value = document.frm_perms.slct_user_none.options[document.frm_perms.slct_user_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_user_ro[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_perms.elements['slct_user_ro[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_perms.elements['slct_user_ro[]'].options[document.frm_perms.elements['slct_user_ro[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_user_ro()\r";
print "    {\r";
print "    if (document.frm_perms.elements['slct_user_ro[]'].selectedIndex >= 0)\r";
print "     document.frm_perms.elements['slct_user_ro[]'].options[document.frm_perms.elements['slct_user_ro[]'].selectedIndex] = null;\r";
print "    }\r";

print "  function add_user_e()\r";
print "    {\r";
print "    var index, item, new_flag, value;\r";
print "    new_flag = \"TRUE\";\r";
print "    item  = document.frm_perms.slct_user_none.options[document.frm_perms.slct_user_none.selectedIndex].text;\r";
print "    value = document.frm_perms.slct_user_none.options[document.frm_perms.slct_user_none.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_user_e[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_perms.elements['slct_user_e[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_perms.elements['slct_user_e[]'].options[document.frm_perms.elements['slct_user_e[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_user_e()\r";
print "    {\r";
print "    if (document.frm_perms.elements['slct_user_e[]'].selectedIndex >= 0)\r";
print "     document.frm_perms.elements['slct_user_e[]'].options[document.frm_perms.elements['slct_user_e[]'].selectedIndex] = null;\r";
print "    }\r";

/*
print "  function change_user_group()\r";
print "    {\r";
print "    document.frm_perms.hdn_change_users_group.value='TRUE';\r";
print "    update_perms();\r";
print "    }\r";
*/
print "  function update_perms()\r";
print "    {\r";
print "    var index;\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_group_ro[]'].length; index++)\r";
print "      {\r";
print "      document.frm_perms.elements['slct_group_ro[]'].options[index].selected = 'TRUE';\r";
print "      }\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_group_e[]'].length; index++)\r";
print "      {\r";
print "      document.frm_perms.elements['slct_group_e[]'].options[index].selected = 'TRUE';\r";
print "      }\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_user_ro[]'].length; index++)\r";
print "      {\r";
print "      document.frm_perms.elements['slct_user_ro[]'].options[index].selected = 'TRUE';\r";
print "      }\r";
print "    for ( index = 0; index < document.frm_perms.elements['slct_user_e[]'].length; index++)\r";
print "      {\r";
print "      document.frm_perms.elements['slct_user_e[]'].options[index].selected = 'TRUE';\r";
print "      }\r";

print "    document.frm_perms.hdn_update_perms.value = 'TRUE';\r";
print "    document.frm_perms.submit();\r";
print "    }\r";

print "</SCRIPT>\r";  

function change_owner_perms($current_owner_id)
	{
	global $dmsdb;
  
	$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." ORDER BY uname";
	$result = $dmsdb->query($query);
  
	print "<select name='hdn_owner_id'>\r";
	while($result_data = $dmsdb->getarray($result))
		{
		print "<option value='".$result_data['uid']."' ";
		if ($current_owner_id == $result_data['uid']) print "selected";
		print ">".$result_data['uname']."</option>";
		}
	print "</select>\r";
	}
	
if( !($obj_id > 0)  )   // If a valid $obj_id doesn't exist, then look for one.
	{
	if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
	else $obj_id = $HTTP_GET_VARS['obj_id'];
	}
	
if (dms_get_var("hdn_update_perms") == "TRUE")
	{
	// Delete all permissions for this object
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);

	// Add owner permission, if applicable 
	if ($post_hdn_owner_id > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,user_perms) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_hdn_owner_id."','";
		$query .= OWNER."')";
		$dmsdb->query($query);
		}
	
	// Add everyone permissions
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "(ptr_obj_id,everyone_perms) VALUES ('";
	$query .= $obj_id."','";
	$query .= $post_slct_everyone."')";
	$dmsdb->query($query);
  
	// Add groups permissions
	$index = 0;
	while ( strlen($post_slct_group_ro[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,group_id,group_perms) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_group_ro[$index]."','";
		$query .= READONLY."')";
		$dmsdb->query($query);
	
		$index++;
		}

	$index = 0;
	while ( strlen($post_slct_group_e[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,group_id,group_perms) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_group_e[$index]."','";
		$query .= EDIT."')";
		$dmsdb->query($query);
	
		$index++;
		}
	  
	// Add users permissions
	$index = 0;
	while ( strlen($post_slct_user_ro[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,user_perms) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_user_ro[$index]."','";
		$query .= READONLY."')";
		$dmsdb->query($query);
	
		$index++;
		}
 
	$index = 0;
	while ( strlen($post_slct_user_e[$index]) > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,user_perms) VALUES ('";
		$query .= $obj_id."','";
		$query .= $post_slct_user_e[$index]."','";
		$query .= EDIT."')";
		$dmsdb->query($query);
	
		$index++;
		}
  
	dms_auditing($obj_id,"document or folder/update permissions");
	}

if (dms_get_var("hdn_change_users_group") == "TRUE")
	{
	$selected_group = dms_get_var("slct_user_groups");
	}
else $selected_group = 1;

// Get object permissions
$query  = "SELECT user_id, group_id, user_perms, group_perms, everyone_perms FROM ".$dmsdb->prefix("dms_object_perms")." ";
$query .= "WHERE ptr_obj_id='".$obj_id."'";
$perms = $dmsdb->query($query);
  
$perms_row_count = $dmsdb->getnumrows();

$perms_user_id        = array();
$perms_user_name      = array();
$perms_group_id       = array();
$perms_group_name     = array();
$perms_user_perms     = array();
$perms_group_perms    = array();
$perms_everyone_perms = array();

$perms_owner = '0';

$slct_everyone_perms = array(" selected","","","");

$group_array_index = 0;
$user_array_index = 0;
    
while($perms_data = $dmsdb->getarray($perms))
	{
	// Determine Owner (there is, at most, one entry)
	if ($perms_data['user_perms'] == OWNER) $perms_owner = $perms_data['user_id'];
	else
		{
		// Determine User Permissions
		if ($perms_data['user_id'] > NONE)
			{
			$perms_user_id[$user_array_index] = $perms_data['user_id'];
			$perms_user_name[$user_array_index] = $xoopsUser->getUnameFromID($perms_user_id[$user_array_index]);
			$perms_user_perms[$user_array_index] = $perms_data['user_perms'];
			$user_array_index++;
			}
		}
  
	// Determine Group Permissions
	if ($perms_data['group_id'] > NONE)
		{
		$perms_group_id[$group_array_index] = $perms_data['group_id'];

		$group_details = $dms_groups->grp_details($perms_group_id[$group_array_index]);
		$perms_group_name[$group_array_index] = $group_details['name']; 
		  
		$perms_group_perms[$group_array_index] = $perms_data['group_perms'];
		$group_array_index++;
		}
	  
	// Determine Everyone permissions (there is, at most, one entry)
	if ($perms_data['everyone_perms'] > NONE)
		{
		$slct_everyone_perms[$perms_data['everyone_perms']] = " selected";
		$slct_everyone_perms[0] = "";
		}
	}

// Sort the user and group permissions by name
asort($perms_user_name);
asort($perms_group_name);
reset($perms_user_name);
reset($perms_group_name);

print "      <a name='perms_set'></a>\r";

print "      <table width='100%' border='0' cellpadding='0' cellspacing='0'>\r";
  
print "      <form name='frm_perms' action='".$this_file."#perms_set' method='post'>\r";

print "        <tr><td colspan='4' align='left' ".$class_subheader.">&nbsp;" . _DMS_PERMISSIONS . "</td>\r";
print "          <td align='right' ".$dms_config['class_subheader'].">";
dms_help_system("inc_perms_set");
print "          </td>\r";
print "        </tr>\r";

print "        <tr><td colspan='5' align='left'><BR></td></tr>\r";
print "        <tr><td colspan='5' align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_OWNER . "</td></tr>\r";
print "        <tr>\r";
print "          <td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r";

if ($perms_owner > '0') 
	{
	if ($dms_admin_flag == 1)
		{
		change_owner_perms($perms_owner);
		}
	else
		{
		print $xoopsUser->getUnameFromId($perms_owner);
		print "<input type='hidden' name='hdn_owner_id' value='".$perms_owner."'>\r";
		}
	}
else 
	{
	if ($dms_admin_flag == 1)
		{
		change_owner_perms(1);
		}  
	else
		{
		print "None";
		print "<input type='hidden' name='hdn_owner_id' value='0'>\r";
		}
	}
	
print "          </td>\r";
print "        </tr>\r";    
print "        </td></tr>\r";

print "        <tr><td colspan='5' align='left'><BR></td></tr>\r";

print "        <tr>\r";
print "          <td colspan='5' align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_EVERYONE . "</td>";
print "        </tr>\r";
print "        <tr>\r";
print "          <td colspan='5' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "            <select name='slct_everyone'>\r";
print "              <option value=0 ".$slct_everyone_perms[0].">" . _DMS_NONE . "</option>\r";
print "              <option value=1 ".$slct_everyone_perms[1].">" . _DMS_BROWSE . "</option>\r";
print "              <option value=2 ".$slct_everyone_perms[2].">" . _DMS_READ_ONLY . "</option>\r";
print "              <option value=3 ".$slct_everyone_perms[3].">" . _DMS_EDIT . "</option>\r";
print "            </select>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr><td colspan='5' align='left'><BR></td></tr>\r";

print "        <tr><td colspan='5' align='left' valign='top'>&nbsp;&nbsp;&nbsp;" . _DMS_GROUPS . "</td></tr>\r";
print "        <tr><td colspan='1' align='left' valign='top'>\r";
print "              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "              <select name='slct_group_none' size='10' ".$perms_select_width.">\r";

// Get a list of all groups
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
print "              " . _DMS_READ_ONLY_DOT . "<BR>";
print "              <select name='slct_group_ro[]' size='10' multiple ".$perms_select_width.">\r";
      
foreach ($perms_group_name as $index => $g_name)
	{
	if ($perms_group_perms[$index]==READONLY)
		{
		print "              <option value='".$perms_group_id[$index]."'>".$g_name."</option>\r";
		}
	}  

print "              </select><BR>\r";
print "              <input type='button' value='" . _DMS_ADD . "' ".$ar_button_width." onclick='add_group_ro();'><BR>";
print "              <input type='button' value='" . _DMS_REMOVE . "' ".$ar_button_width." onclick='remove_group_ro();'>";
print "            </td>\r";
print "            <td colspan='1' width='2%'><BR></td>\r";
print "            <td colspan='1' align='left' valign='top'>\r";
print "              " . _DMS_EDIT_DOT . "<BR>";
print "              <select name='slct_group_e[]' size='10' multiple ".$perms_select_width.">\r";

foreach ($perms_group_name as $index => $g_name)
	{
	if ($perms_group_perms[$index]==EDIT)
		{
		print "              <option value='".$perms_group_id[$index]."'>".$g_name."</option>\r";
		}
	}  

print "              </select>&nbsp;&nbsp;<BR>\r";
print "              <input type='button' value='" . _DMS_ADD . "' ".$ar_button_width." onclick='add_group_e();'><BR>";
print "              <input type='button' value='" . _DMS_REMOVE . "' ".$ar_button_width." onclick='remove_group_e();'>";
print "            </td>\r";
print "        </tr>";

print "        <tr><td colspan='5'><BR></td></tr>\r";




$mlist= array();
$mlist = $dms_groups->usr_list_all(); //($selected_group);
	
// Sort $mlist alphabetically
asort($mlist);
reset($mlist);

print "        <tr><td colspan='5' align='left' valign='top'>&nbsp;&nbsp;&nbsp;" . _DMS_USERS . "</td></tr>\r";
print "        <tr><td colspan='1' valign='top' align='left'>\r";
print "              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
print "              <select name='slct_user_none' size='10' ".$perms_select_width.">\r";
    
foreach ($mlist as $u_id => $u_name)
	{
	print "        <option value='".$u_id."'>".$u_name."</option>\r";
	}  
	
print "              </select>\r";

print "            </td>\r";

print "            <td colspan='1' width='2%'><BR></td>\r";
print "            <td colspan='1' align='left' valign='top'>\r";
print "              " . _DMS_READ_ONLY_DOT . "<BR>";
print "              <select name='slct_user_ro[]' size='10' multiple ".$perms_select_width.">\r";

foreach ($perms_user_name as $index => $u_name)
	{
	if ($perms_user_perms[$index]==READONLY)
		{
		print "        <option value='".$perms_user_id[$index]."'>".$u_name."</option>\r";
		}
	}  
  
print "              </select><BR>\r";
print "              <input type='button' value='" . _DMS_ADD . "'' ".$ar_button_width." onclick='add_user_ro();'><BR>";
print "              <input type='button' value='" . _DMS_REMOVE . "'' ".$ar_button_width." onclick='remove_user_ro();'>\r";
print "            </td>\r";
print "            <td colspan='1' width='2%'><BR></td>\r";
print "            <td colspan='1' align='left' valign='top'>\r";
print "              " . _DMS_EDIT_DOT . "<BR>";
print "              <select name='slct_user_e[]' size='10' multiple ".$perms_select_width.">\r";
  
foreach ($perms_user_name as $index => $u_name)
	{
	if ($perms_user_perms[$index]==EDIT)
		{
		print "        <option value='".$perms_user_id[$index]."'>".$u_name."</option>\r";
		}
	}  

print "              </select>&nbsp;&nbsp;<BR>\r";
print "              <input type='button' value='" . _DMS_ADD . "' ".$ar_button_width." onclick='add_user_e();'><BR>";
print "              <input type='button' value='" . _DMS_REMOVE . "' ".$ar_button_width." onclick='remove_user_e();'>";
print "            </td>\r";
print "        </tr>\r";

print "        <tr><td colspan=5><BR></td></tr>\r";
print "        <tr><td colspan=5 align='left'>&nbsp;&nbsp;&nbsp;<input type='button' name='btn_update_perms' value='" . _DMS_UPDATE_PERMISSIONS . "' onclick='update_perms();'></td></tr>\r";
print "        <input type='hidden' name='hdn_update_perms' value='FALSE'>\r";
print "        <input type='hidden' name='hdn_change_users_group' value='FALSE'>\r";
print "        <input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
print "        </form>\r";

print "      </table>\r";

?>
