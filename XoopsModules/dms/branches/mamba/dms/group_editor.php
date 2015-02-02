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

// file_route.php

include '../../mainfile.php';

include_once 'inc_defines.php';
include_once 'inc_dms_functions.php';

$group_select_width = " style='width: 60mm;' ";

import_request_variables("P","post_");

if ( $dms_admin_flag!=1 )
	{
	//header("Location:index.php");
	
	dms_header_redirect("index.php");
	exit(0);
	}

if(dms_get_var("slct_group") != FALSE)
	{
	$selected_group_id = dms_get_var("slct_group");
	}
else
	{
	$selected_group_id = 0;
	}

if(dms_get_var("hdn_update_group") != FALSE)
	{
	$selected_group_id = $post_hdn_group_id;
	$dms_groups->usr_delete_all($post_hdn_group_id);
	
	$index = 0;
	//while ( strlen($post_slct_group_list[$index]) > 0)
	while(isset($post_slct_group_list[$index]))
		{
		$dms_groups->usr_add($post_hdn_group_id,$post_slct_group_list[$index]);
			
		$index++;
		}
	}


	
include XOOPS_ROOT_PATH.'/header.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";

print "  function select_group()\r";
print "    {\r";
print "    if(document.frm_select_group.slct_group.value > 0) document.frm_select_group.submit();\r";
print "    }\r";

print "  function update_group()\r";
print "    {\r";
print "    var index;\r";
print "    for ( index = 0; index < document.frm_edit_group.elements['slct_group_list[]'].length; index++)\r";
print "      {\r";
print "      document.frm_edit_group.elements['slct_group_list[]'].options[index].selected = 'TRUE';\r";
print "      }\r";
print "    for ( index = 0; index < document.frm_edit_group.elements['slct_group_list[]'].length; index++)\r";
print "      {\r";
print "      document.frm_edit_group.elements['slct_group_list[]'].options[index].selected = 'TRUE';\r";
print "      }\r";
//print "    document.frm_edit_group.hdn_route_file_confirm.value = 'TRUE';\r";
print "    document.frm_edit_group.submit();\r";
print "    }\r";

print "  function add_user()\r";
print "    {\r";
print "    var index, item, new_flag;\r";
print "    new_flag = \"TRUE\";\r";
print "    item = document.frm_edit_group.slct_user_list.options[document.frm_edit_group.slct_user_list.selectedIndex].text;\r";
print "    value = document.frm_edit_group.slct_user_list.options[document.frm_edit_group.slct_user_list.selectedIndex].value;\r";
print "    for ( index = 0; index < document.frm_edit_group.elements['slct_group_list[]'].length; index++)\r";
print "      {\r";
print "      if (item == document.frm_edit_group.elements['slct_group_list[]'].options[index].text) new_flag = \"FALSE\";\r";
print "      }\r";
print "    if (new_flag == \"TRUE\")\r";
print "     document.frm_edit_group.elements['slct_group_list[]'].options[document.frm_edit_group.elements['slct_group_list[]'].length]\r";
print "      = new Option (item,value);\r";
print "    }\r";

print "  function remove_user()\r";
print "    {\r";
print "    if (document.frm_edit_group.elements['slct_group_list[]'].selectedIndex >= 0)\r";
print "     document.frm_edit_group.elements['slct_group_list[]'].options[document.frm_edit_group.elements['slct_group_list[]'].selectedIndex] = null;\r";
print "    }\r";

print "</SCRIPT>\r";  

print "<table width='100%' border='0'>\r";
display_dms_header();

print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <tr><td colspan='2' align='left'><b>Group Editor</b></td></tr>\r";
print "  <tr><td colspan='2'><BR></td></tr>\r";

// Get a list of all the groups
$group_list = array();
$group_list = $dms_groups->grp_list_all();
asort($group_list);
reset($group_list);

print "  <form name='frm_select_group' action='group_editor.php' method='post'>\r";
print "  <tr>\r";
print "    <td align='left'>\r";
print "      Group:&nbsp;&nbsp;&nbsp;";
print "      <select name='slct_group' onchange=select_group();>\r";
//print "        <option value='0'>New</option>\r";

foreach($group_list as $g_id => $g_name)
	{
	print "        <option value='".$g_id."'";
	if($selected_group_id == 0) $selected_group_id = $g_id;
	if($g_id == $selected_group_id) print " selected";
	print ">".$g_name."</option>\r";
	}
	
print "      </select>\r";
print "  </tr>\r";
print "  </form>\r";

//print "  <tr><td colspan='2'><BR></td></tr>\r";

print "  <tr><td colspan='2'><table>\r";

// Get a list of users in Registered Users
$user_list = array();
$user_list = $dms_groups->usr_list_all();
	
// Sort $user_list alphabetically
asort($user_list);
reset($user_list);

print "<form name='frm_edit_group' action='group_editor.php' method='post'>\r";   

print "  <tr><td colspan='4'><BR></td></tr>\r";

print "  <tr>\r";
// Display list of users based upon the group selected in the drop-down box below.
print "    <td style='vertical-align: top;' align='left'>\r";
print "      All Users:<BR><BR>&nbsp;&nbsp;&nbsp;";
print "      <select name='slct_user_list' size='10' ".$group_select_width.">\r";

foreach ($user_list as $u_id => $u_name)
	{
	print "        <option value='".$u_id."'>".$u_name."</option>\r";
	}  

print "      </select>\r";
print "    </td>\r";

print "    <td align='center' width='25%' style='vertical-align: middle;'>\r";
print "      <input type='button' name='btn_add_user' value='" . _DMS_ADD . "&nbsp;&gt;&gt;' onclick='add_user();'> <BR><BR><BR>\r";
print "      <input type='button' name='btn_remove_user' value='&lt;&lt;&nbsp;" . _DMS_REMOVE . "' onclick='remove_user();'>\r";
print "    </td>\r";

$users_in_group = array();

// Display list of users in the group
if($selected_group_id > 0)
	{
	$users_in_group = $dms_groups->usr_list($selected_group_id);
	
	// Sort $user_list alphabetically
	asort($users_in_group);
	reset($users_in_group);
	}

print "    <td style='vertical-align: top;' align='left'>\r";
print "      Users in Group:<BR><BR><BR>\r";
print "      <select name='slct_group_list[]' size='10' multiple ".$group_select_width.">\r";

foreach ($users_in_group as $u_id => $u_name)
	{
	print "        <option value='".$u_id."'>".$u_name."</option>\r";
	}  

print "      </select>\r";
print "    </td>\r";
print "    <td width='100%'></td>\r";
print "  </tr>\r";

print "  </table></td></tr>\r";

print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <tr><td colspan='2' align='left'>";
print "                      <input type=button name='btn_route' value='Update' onclick='update_group();'>";
print "                      <input type=button name='btn_cancel' value='Exit' onclick='location=\"index.php\";'></td></tr>\r";
print "                      <input type=hidden name='hdn_update_group' value='TRUE'>";
print "                      <input type=hidden name='hdn_group_id' value='".$selected_group_id."'>";
print "  </table>\r";

print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

/*

 
  foreach ($GLOBALS as $key=>$value)
    {
	print "\$GLOBALS[\"$key\"]==$value<br>";
	}
  
print "<BR>SG<BR>";

 foreach ($post_slct_group_list as $key=>$value)
    {
	print "\$post_slct_group_list[\"$key\"]==$value<br>";
	}
*/

?>
