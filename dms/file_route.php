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

$route_select_width = " style='width: 50mm;' ";

import_request_variables("P","post_");

// Determine which web page to return to.
/*
$return_url = "";
if ($HTTP_GET_VARS["return_url"])      $return_url = $HTTP_GET_VARS["return_url"];
if ($HTTP_POST_VARS["hdn_return_url"]) $return_url = $HTTP_POST_VARS["hdn_return_url"];
if (strlen($return_url) <= 1)          $return_url = "index.php"; 
*/

$return_url = dms_get_var("return_url");
if ($return_url == FALSE) $return_url = dms_get_var("hdn_return_url");
if ($return_url == FALSE) $return_url = "index.php";

$file_id = dms_get_var("obj_id");
if ($file_id == FALSE) $file_id = dms_get_var("hdn_file_id");

// Permissions required to access this page:
//  BROWSE, READONLY, EDIT, OWNER
$perms_level = dms_perms_level($file_id);

if ( ($perms_level != 1) && ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}

if (dms_get_var("hdn_route_file_confirm") == "TRUE")
	{
	include 'inc_pal_header.php';

	// Get the name of the document
	$query  = "SELECT obj_name FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id = '".$file_id."'";
	$doc_name = $dmsdb->query($query,'obj_name');
	
	// Find the number of individual users to route documents to.
	$index = 0;
	while ( strlen($post_slct_routing_list[$index]) > 0)
		{
		$index++;
		}
	$total_individual_user_count = $index;
	
	// Extract user names from groups
	$index = 0;
	$user_index = $total_individual_user_count;
	while ( strlen($post_slct_group_routing_list[$index]) > 0 )
		{
		// Get the dest_group_id by using the user name in $post_slct_routing_list[]
		$query  = "SELECT groupid FROM ".$dmsdb->prefix('groups')." ";
		$query .= "WHERE name='".$post_slct_group_routing_list[$index]."'";
		//print "<BR>".$query."<BR>";
		$dest_group_id = $dmsdb->query($query,'groupid');
		
		// Add the list of users, in the selected group to the total list of users to route the document to.
		$members = $dms_groups->usr_list($dest_group_id);
		
		$mlist= array();
		//$mcount = count($members);
		
		foreach ($members as $u_id => $u_name)
			{
			$post_slct_routing_list[$user_index] = $u_name;
			$user_index++;
			}
			
		$index++;
		}
  
	$unroutable_users_index = 0;
  
	print "<table width='100%'>\r";
      
	// Route to users
	$index = 0;
	$successful_route_flag = 0;
	while ( strlen($post_slct_routing_list[$index]) > 0)
		{
		// Get the dest_user_id by using the user name in $post_slct_routing_list[]
		$query  = "SELECT uid,email FROM ".$dmsdb->prefix('users')." ";
		$query .= "WHERE uname='".$post_slct_routing_list[$index]."'";
    
		//print $query."<BR>";
	
		$result = $dmsdb->query($query,'ROW');
		
		$dest_user_id = $result->uid;
		$dest_user_email = $result->email;
		
		// Get Destination Inbox obj_id (this will be the object_owner of the new object)

		$dest_inbox = dms_inbox_id($dest_user_id);
		
		// Only route the document if an inbox actually exists.
		if ($dest_inbox > 0)
			{
			// Display the success statement for only the first successful routing.
			if ($successful_route_flag == 0)
				{
				print "  <tr><td align='left'><b>The document has been successfully routed to the following user(s):</b></td></tr>\r";
				$successful_route_flag = 1;
				}
			
			// Create an object in the destination inbox that links to the source object
			$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." (obj_type,ptr_obj_id,obj_owner)";
			$query .= " VALUES ('";
			$query .= DOCLINK."','";
			$query .= $file_id."','";
			$query .= $dest_inbox."')";
			$dmsdb->query($query);

			$link_obj_id = $dmsdb->getid();
	  
			// Create a permissions entry (this is unused and is only used to satisfy the main SQL query in index.php)
			$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." (ptr_obj_id, user_id, user_perms)";
			$query .= " VALUES ('";
			$query .= $link_obj_id."','";
			$query .= $dest_user_id."','";
			$query .= OWNER."')";
			$dmsdb->query($query);
	    
			// Set the destination inbox status to full
			$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET obj_type='".INBOXFULL."' ";
			$query .= "WHERE obj_id='".$dest_inbox."'";  
			$dmsdb->query($query);
  
			// Record the time stamp.
			$query  = "INSERT INTO ".$dmsdb->prefix('dms_routing_data')." ";
			$query .= "(obj_id,source_user_id,time_stamp) ";
			$query .= "VALUES ('";
			$query .= $link_obj_id."','";
			$query .= $dms_user_id."','";
			$query .= time()."')";
			$dmsdb->query($query);  
			
			// If enabled, e-mail the destination user
			
			if($dms_config['routing_email_enable'] == '1')
				{
				$message  = "Document Name:  ".$doc_name."<BR>";
				$message .= "Routed By:  ".$dms_users->get_username($dms_user_id)."<BR>";
				dms_send_email($dest_user_email,$dms_config['routing_email_from'],$dms_config['routing_email_subject'],$message);
				}
			
			// Display the destination user on the screen to inform of a sucessful document routing function.

			print "  <tr><td align='left'>&nbsp;&nbsp;&nbsp;".$dms_users->get_username($dest_user_id)."</td></tr>\r";
			}    
		else
			{
			$unroutable_users[$unroutable_users_index] = $dms_users->get_username($dest_user_id);
			$unroutable_users_index++;
			}
	   
		$index++;
		}
 
 	if($unroutable_users_index > 0)
 		{
		print "  <tr><td><BR></td></tr>\r";
		print "  <tr><td align='left'><b>" . _DMS_UNABLE_TO_ROUTE . "</b></td></tr>\r"; 
	
		for($i=0; $i<$unroutable_users_index; $i++)
			{
			print "  <tr><td align='left'>&nbsp;&nbsp;&nbsp;".$unroutable_users[$i]."</td></tr>\r";
			}
		}

	dms_auditing($file_id,"document/route");
	
	print "  <tr><td><BR></td></tr>\r";
	print "  <tr><td align='left'><input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"index.php\";'></td></tr>\r";
	print "</table>\r";
 
  //print_r ($unroutable_users);
  
	include 'inc_pal_footer.php';
	}
else
	{
	include 'inc_pal_header.php';
  
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$file_id."'";  
	$doc_name = $dmsdb->query($query,'obj_name');

	print "<SCRIPT LANGUAGE='Javascript'>\r";

	print "  function route_file()\r";
	print "    {\r";
	print "    var index;\r";
	print "    for ( index = 0; index < document.frm_routing_select.elements['slct_routing_list[]'].length; index++)\r";
	print "      {\r";
	print "      document.frm_routing_select.elements['slct_routing_list[]'].options[index].selected = 'TRUE';\r";
	print "      }\r";
	print "    for ( index = 0; index < document.frm_routing_select.elements['slct_group_routing_list[]'].length; index++)\r";
	print "      {\r";
	print "      document.frm_routing_select.elements['slct_group_routing_list[]'].options[index].selected = 'TRUE';\r";
	print "      }\r";
	print "    document.frm_routing_select.hdn_route_file_confirm.value = 'TRUE';\r";
	print "    document.frm_routing_select.submit();\r";
	print "    }\r";
	
	print "  function add_group()\r";
	print "    {\r";
	print "    var index, item, new_flag;\r";
	print "    new_flag = \"TRUE\";\r";
	print "    item = document.frm_routing_select.slct_group_list.options[document.frm_routing_select.slct_group_list.selectedIndex].text;\r";
	print "    for ( index = 0; index < document.frm_routing_select.elements['slct_group_routing_list[]'].length; index++)\r";
	print "      {\r";
	print "      if (item == document.frm_routing_select.elements['slct_group_routing_list[]'].options[index].text) new_flag = \"FALSE\";\r";
	print "      }\r";
	print "    if (new_flag == \"TRUE\")\r";
	print "     document.frm_routing_select.elements['slct_group_routing_list[]'].options[document.frm_routing_select.elements['slct_group_routing_list[]'].length]\r";
	print "      = new Option (item);\r";
	print "    }\r";

	print "  function remove_group()\r";
	print "    {\r";
	print "    if (document.frm_routing_select.elements['slct_group_routing_list[]'].selectedIndex >= 0)\r";
	print "     document.frm_routing_select.elements['slct_group_routing_list[]'].options[document.frm_routing_select.elements['slct_group_routing_list[]'].selectedIndex] = null;\r";
	print "    }\r";


	print "  function add_user()\r";
	print "    {\r";
	print "    var index, item, new_flag;\r";
	print "    new_flag = \"TRUE\";\r";
	print "    item = document.frm_routing_select.slct_user_list.options[document.frm_routing_select.slct_user_list.selectedIndex].text;\r";
	print "    for ( index = 0; index < document.frm_routing_select.elements['slct_routing_list[]'].length; index++)\r";
	print "      {\r";
	print "      if (item == document.frm_routing_select.elements['slct_routing_list[]'].options[index].text) new_flag = \"FALSE\";\r";
	print "      }\r";
	print "    if (new_flag == \"TRUE\")\r";
	print "     document.frm_routing_select.elements['slct_routing_list[]'].options[document.frm_routing_select.elements['slct_routing_list[]'].length]\r";
	print "      = new Option (item);\r";
	print "    }\r";

	print "  function remove_user()\r";
	print "    {\r";
	print "    if (document.frm_routing_select.elements['slct_routing_list[]'].selectedIndex >= 0)\r";
	print "     document.frm_routing_select.elements['slct_routing_list[]'].options[document.frm_routing_select.elements['slct_routing_list[]'].selectedIndex] = null;\r";
	print "    }\r";

	print "</SCRIPT>\r";  
	
	print "<table width='100%' border='0'>\r";
	display_dms_header();
  
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>" . _DMS_ROUTE_DOCUMENT . "</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>" . _DMS_FILE_NAME . "&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
  
	print "  <tr><td colspan='2'><BR></td></tr>\r";

	print "  <tr><td colspan='2'><table>\r";
    
	print "  <tr>";
	print "    <td colspan='4'>";
	print "    </td>\r";
	print "  </tr>\r";

	// Get a list of the available groups.
	$group_list = array();
	$group_list = $dms_groups->grp_list_all();

	asort($group_list);
	reset($group_list);
	
	// Get a list of all users.
	$mlist = $dms_groups->usr_list_all();
	
	// Sort $mlist alphabetically
	asort($mlist);
	reset($mlist);
	
	print "<form name='frm_routing_select' action=file_route.php method=post>\r";   
  
  
  
	print "  <tr>\r";
	// Display list of groups that may be selected to be routed.
	print "    <td style='vertical-align: top;'>\r";
	print "      " . _DMS_GROUPS . "<BR><BR>&nbsp;&nbsp;&nbsp;";
	print "      <select name='slct_group_list' size='10' ".$route_select_width.">\r";
 
	reset($group_list);     
	foreach ($group_list as $g_id => $g_name)
		{
		print "        <option value='".$g_id."'>".$g_name."</option>\r";
		}  
	
	print "      </select>\r";
	print "    </td>\r";
  
	print "    <td align='center' width='25%' style='vertical-align: middle;'>\r";
	print "      <input type='button' name='btn_add_group' value='" . _DMS_ADD . "&nbsp;&gt;&gt;' onclick='add_group();'> <BR><BR><BR>\r";
	print "      <input type='button' name='btn_remove_group' value='&lt;&lt;&nbsp;" . _DMS_REMOVE . "' onclick='remove_group();'>\r";
	print "    </td>\r";
    
	// Display list of groups to be routed.
	print "    <td style='vertical-align: top;'><BR><BR><BR>\r";
	print "      <select name='slct_group_routing_list[]' size='10' multiple ".$route_select_width.">\r";
  
	$index=0;
	//while ( strlen($post_slct_group_routing_list[$index]) > 0)
	while(isset($post_slct_group_routing_list))
		{
		print "<option>".$post_slct_group_routing_list[$index]."</option>";
		$index++;
		}

	print "      </select>\r";
	print "    </td>\r";
	print "    <td width='100%'></td>\r";
	print "  </tr>\r";
 
  
	print "  <tr><td colspan='4'><BR></td></tr>\r";
  
  
	print "  <tr>\r";
	// Display list of users based upon the group selected in the drop-down box below.
	print "    <td style='vertical-align: top;'>\r";
	print "      " . _DMS_USERS . "<BR><BR>&nbsp;&nbsp;&nbsp;";
	print "      <select name='slct_user_list' size='10' ".$route_select_width.">\r";
    
	foreach ($mlist as $u_id => $u_name)
		{
		print "        <option value='".$u_id."'>".$u_name."</option>\r";
		}  
  
	print "      </select><BR>\r";
	
	print "    </td>\r";
  
	print "    <td align='center' width='25%' style='vertical-align: middle;'>\r";
	print "      <input type='button' name='btn_add_user' value='" . _DMS_ADD . "&nbsp;&gt;&gt;' onclick='add_user();'> <BR><BR><BR>\r";
	print "      <input type='button' name='btn_remove_user' value='&lt;&lt;&nbsp;" . _DMS_REMOVE . "' onclick='remove_user();'>\r";
	print "    </td>\r";
    
	// Display list of users to be routed
	print "    <td style='vertical-align: top;'><BR><BR><BR>\r";
	print "      <select name='slct_routing_list[]' size='10' multiple ".$route_select_width.">\r";
  
	$index=0;
	//while ( strlen($post_slct_routing_list[$index]) > 0)
	while (isset($post_slct_group_routing_list))
		{
		print "<option>".$post_slct_routing_list[$index]."</option>";
		$index++;
		}

	print "      </select>\r";
	print "    </td>\r";
	print "    <td width='100%'></td>\r";
	print "  </tr>\r";
  
	print "  </table></td></tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'>";
	print "                      <input type=button name='btn_route' value='" . _DMS_ROUTE . "' onclick='route_file();'>";
	print "                      <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"".$return_url."\";'></td></tr>\r";
  
	print "  </table>\r";
  
	print "  <input type='hidden' name='hdn_route_file_confirm' value=''>\r";
	print "  <input type='hidden' name='hdn_file_id' value='".$file_id."'>\r";
	print "  <input type='hidden' name='hdn_selected_group' value=''>\r";
	print "  <input type='hidden' name='hdn_stored_user_names' value=''>\r";
	print "  <input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "</form>\r";

	include 'inc_pal_footer.php';
	}



/* 
  foreach ($GLOBALS as $key=>$value)
    {
	print "\$GLOBALS[\"$key\"]==$value<br>";
	}
  
print "<BR>GRL<BR>";

 foreach ($post_slct_group_routing_list as $key=>$value)
    {
	print "\$post_slct_group_routing_list[\"$key\"]==$value<br>";
	}


print "<BR>URL<BR>";

 foreach ($post_slct_routing_list as $key=>$value)
    {
	print "\$post_slct_routing_list[\"$key\"]==$value<br>";
	}
*/
	
?>
