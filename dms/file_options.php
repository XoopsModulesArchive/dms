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

// file_options.php

include '../../mainfile.php';
include_once 'inc_defines.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_properties.php';

$option_button_width=" style='width: 6em;' ";

import_request_variables("P","post_");
$this_file = "file_options.php";
$obj_path = array();


//if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
//else $obj_id = $HTTP_GET_VARS['obj_id'];

$obj_id = dms_get_var("hdn_obj_id");
if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");

// Permissions required to access this page:
//  BROWSE, READONLY, EDIT, OWNER
$perms_level = dms_perms_level($obj_id);
//$perms_level = dms_determine_admin_perms($perms_level)
if ($dms_admin_flag == 1) $perms_level = OWNER;

if ( ($perms_level != 1) && ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
	{
	//header("Location:index.php");
	
	dms_header_redirect("index.php");
	
	end();
	}


function dms_get_date($var_name, $current_value = -1)
	{
// If there is a current_value, convert it into the appropriate information
	if($current_value != -1)
		{
		$month  = (int)strftime("%m",$current_value);
		$day    = (int)strftime("%d",$current_value);
		$year   = (int)strftime("%Y",$current_value);
		}
		
// Get Month
	print "<select name='slct_".$var_name."_month'>\r";
	for($index = 1;$index <= 12; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $month) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";

	print "/&nbsp;";
	
// Get Day
	print "<select name='slct_".$var_name."_day'>\r";
	for($index = 1;$index <= 31; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $day) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";

	print "/&nbsp;";
	
// Get Year
	print "<select name='slct_".$var_name."_year'>\r";
	for($index = 2007;$index <= 2030; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $year) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";
	}

	
function dms_get_obj_path($obj_id)
	{
	global $dmsdb;
	
	$obj_path = array();
	
	// First get the obj_owner (parent folder) of the object.
	$query = "SELECT obj_owner FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$obj_owner = $dmsdb->query($query,"obj_owner");
	
	$loop_flag = TRUE;
	$index = 0;
	
	while($loop_flag == TRUE)
		{
		$query  = "SELECT obj_owner,obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_owner."'";
		$result = $dmsdb->query($query,"ROW");
		if($dmsdb->getnumrows() == 0) break;
		
		$obj_path["obj_name"][$index] = $result->obj_name;
		$obj_path["obj_id"][$index] = $obj_owner;

		$obj_owner = $result->obj_owner;
		
		if($result->obj_owner == 0) $loop_flag = FALSE;
		
		$index++;
		}
	
	$obj_path["total_num_objects"] = $index;
	
	return $obj_path;
	}
	
	
if (dms_get_var("hdn_update_comments") == "confirm")
	{
	dms_auditing($obj_id,"document/update comments");

	$query = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$current_version_row_id = $dmsdb->query($query,"current_version_row_id");

/*	
	$query = "DELETE FROM ".$dmsdb->prefix("dms_object_version_comments")." WHERE row_id='".$HTTP_POST_VARS['hdn_dovc_row_id']."'";
	$dmsdb->query($query);
*/
	if(dms_get_var("hdn_dovc_row_id") > 0)
		{
		$query  = "UPDATE ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= "SET comment='".dms_strprep(dms_get_var("txt_comments"))."' ";
		$query .= "WHERE dov_row_id='".$current_version_row_id."'";
		$dmsdb->query($query);
		}
	else
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= "(dov_row_id,comment) ";
		$query .= "VALUES ('".$current_version_row_id."','".dms_strprep(dms_get_var("txt_comments"))."')";
		$dmsdb->query($query);
		}
	
	dms_message("The document comments have been updated.");
	}

if(dms_get_var("hdn_update_doc_exp") == "confirm")
	{
	$obj_id=dms_get_var("hdn_obj_id_doc_exp");
	
	$expire_month = dms_get_var("slct_time_stamp_expire_month");
	$expire_day = dms_get_var("slct_time_stamp_expire_day");
	$expire_year = dms_get_var("slct_time_stamp_expire_year");
	$time_stamp_expire = mktime(0,0,0,$expire_month,$expire_day,$expire_year);
	
	$enable_doc_expiration = dms_get_var_chk("chk_document_expiration_enable");
	if($enable_doc_expiration == 0) $time_stamp_expire = '0';
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET ";
	$query .= "time_stamp_expire='".$time_stamp_expire."' WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);

	dms_auditing($obj_id,"document/update expiration:".$time_stamp_expire);
	
	dms_message("The document expiration settings have been updated.");
	}

	
	  
if (dms_get_var("hdn_update_options") == "confirm")
	{
	dms_auditing($obj_id,"document/update properties");
  
	$obj_name = dms_strprep(dms_get_var("txt_obj_name"));

	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET ";
	$query .= "obj_name='".$obj_name."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);

	dms_document_name_sync($obj_id);
	
	update_file_properties($obj_id);

	dms_message("The document properties have been updated.");
	
	//header("Location:file_options.php?obj_id=".$obj_id);   
	
	dms_header_redirect("file_options.php?obj_id=".$obj_id);
	}
else
	{  
	// Get object information
	$query  = "SELECT template_obj_id,obj_name,obj_status,obj_checked_out_user_id,lifecycle_id, lifecycle_stage, ";
	$query .= "time_stamp_create,time_stamp_expire,current_version_row_id ";
	$query .= "FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$object = $dmsdb->query($query,'ROW');   
	
	// Get current version information
	$query  = "SELECT major_version,minor_version,sub_minor_version,file_type,file_size,time_stamp ";
	$query .= "FROM ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "WHERE row_id='".$object->current_version_row_id."'";
	$current_version = $dmsdb->query($query,'ROW'); 
	//mysql_fetch_object(mysql_query($query));
  
	if ($object->obj_status == CHECKEDOUT) 
		{
		$checked_out = TRUE;
		}
	else
		{
		$checked_out = FALSE;
		}
	
	// Determine if the user is subscribed to this document.
	$query  = "SELECT count(row_id) as num from ".$dmsdb->prefix("dms_subscriptions")." ";
	$query .= "WHERE obj_id='".$obj_id."' and user_id='".$dms_user_id."'";  
	$subscribed = $dmsdb->query($query,'num');
	//mysql_result(mysql_query($query),'num');
	
	if($subscribed > 0) $subscribed = TRUE;
	else $subscribed = FALSE;

	//include XOOPS_ROOT_PATH.'/header.php';
	include 'inc_pal_header.php';
	
// Message Box
include_once 'inc_message_box.php';
dms_message_box();
dms_dhtml_mb_functions();


// Options Menu

print "<script type='text/javascript'>\r";
print "<!--\r";
print "var thresholdY = 15; // in pixels; threshold for vertical repositioning of layer\r";
print "var ordinata_margin = 20; // to start the layer a bit above the mouse vertical coordinate\r";
print "// -->\r";
print "</script>\r";

print "<script type='text/javascript' src='".XOOPS_URL."/modules/dms/layersmenu.js'></script>\r";

print "<script language='JavaScript'>\r";
print "<!--\r";
print "currentX = -1;\r";
print "function fo_grabMouseX(e) {\r";
print "  if ((DOM && !IE4) || Opera5) {\r";
print "    currentX = e.clientX;\r";
print "    } else if (NS4) {\r";
print "    currentX = e.pageX;\r";
print "    } else {\r";
print "    currentX = event.x;\r";
print "    }\r";
/*
print "  if (DOM && !IE4 && !Opera5 && !Konqueror) {\r";
print "    currentX += window.pageXoffset;\r";
print "      } else if (IE4 && DOM && !Opera5 && !Konqueror) {\r";
print "      currentX += document.body.scrollLeft;\r";
print "    }\r";
*/
print "  }\r";

print "// -->\r";
print "</script>\r";

print "<script type='text/javascript'>\r";
print "<!--\r";

print "function fo_popUpMenu() {\r";
print "fo_shutdown();\r";
print "setleft('div_menu',currentX);\r";
print "popUp(\"div_menu\",true);\r";
print "}\r";

print "function fo_moveLayers() {\r";
print "fo_grabMouseX;\r";
print "setleft('div_menu',currentX);\r";
print "settop('div_menu',currentY);\r";
print "}\r";

print "function fo_shutdown() {\r";
print "popUp('div_menu',false);\r";
print "}\r";

print "if (NS4) {\r";
print "document.onmousedown = function() { fo_shutdown(); }\r";
print "} else {\r";
print "document.onclick = function() { fo_shutdown(); }\r";
print "}\r";

print "// -->\r";
print "</script>\r";

print "<div id='div_menu' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

print "<table ".$dms_config['class_narrow_header']." width='150' cellspacing='1' style='width: 6em;'>\r";

print "<th nowrap='nowrap' align='center'>Options</th>\r";

print "<tr><td ".$dms_config['class_narrow_content']." nowrap='nowrap' align='center'>\r";
//

if ( (($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER)) && ($dms_config['routing_enable'] == 1) )
	{
	// Route Button
	if ($checked_out==FALSE)
		{
		print "<a href='file_route.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>" . _DMS_ROUTE . "</a>&nbsp;\r";
		print "<a href='file_recall.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>Recall</a><BR>\r";
		}
	}

if ( ($perms_level == EDIT) || ($perms_level == OWNER) ) 
	{
	// Checkin/Checkout/Cancel Checkout Buttons
	if ( ($checked_out==FALSE) && ($dms_config['checkinout_enable'] == 1) )
		{
		print "<a href='file_checkout.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>" . _DMS_CHECKOUT . "</a><BR>\r";
		}

	if ( ($checked_out==TRUE) && ($dms_config['checkinout_enable'] == 1) )
		{
		print "<a href='file_checkin.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>". _DMS_CHECKIN ."</a><BR>\r";
		
		if (($checked_out==TRUE) && ( ($dms_admin_flag == 1) || ($object->obj_checked_out_user_id == $dms_user_id) ) )
			{
			print "<a href='file_checkout_cancel.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>" . _DMS_CANCEL_CHECKOUT . "</a><BR>\r";
			}
		}

	if ( ($checked_out==FALSE) && ($dms_config['checkinout_enable'] == 1) )
		{
		print "<a href='file_revert.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>Revert</a><BR>\r";
		}
		
	if ($dms_config['sub_email_enable']=='1')
		{
		if($subscribed == TRUE)
			{
			print "<a href='file_subscription.php?funct=unsub&obj_id=".$obj_id."&ret_url=file_options.php'>Unsubscribe</a><BR>\r";
			}
		else
			{
			print "<a href='file_subscription.php?funct=subscribe&obj_id=".$obj_id."&ret_url=file_options.php'>Subscribe</a><BR>\r";
			}
		}
		
	// Lifecycle/Promote/Demote Buttons 
	if (( $checked_out==FALSE) && ($object->lifecycle_id == 0) && ($dms_config['lifecycle_enable'] == 1))
		{
		print "<a href='lifecycle_apply.php?obj_id=".$obj_id."'>" . _DMS_LIFECYCLE . "</a><BR>\r";
		}

	if (( $checked_out==FALSE) && ($object->lifecycle_id >0) )
		{
		print "<a href='lifecycle_promote.php?obj_id=".$obj_id."'>" . _DMS_PROMOTE ."</a>\r";
		print "<a href='lifecycle_demote.php?obj_id=".$obj_id."'>" . _DMS_DEMOTE ."</a><BR>\r";
		}

	// Copy/Move/Delete Buttons
	if ( ( $checked_out==FALSE) || ($dms_admin_flag == 1) )
		{  
		//if (($checked_out==FALSE) && ($object->lifecycle_id == 0))
		if ( ($checked_out==FALSE) && ( ($object->lifecycle_id == 0) || ($dms_admin_flag == 1) ) )
			{
			print "<a href='file_copy.php?obj_id=".$obj_id."'>" . _DMS_COPY . "</a>&nbsp;\r";
			print "<a href='file_move.php?obj_id=".$obj_id."'>" . _DMS_MOVE . "</a>&nbsp;\r";
			}

		if (( $checked_out==FALSE) || ($dms_admin_flag == 1) )
			{
			print "<a href='obj_delete.php?obj_id=".$obj_id."'>" . _DMS_DELETE . "</a><BR>\r";
			}
		}
	}

if ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) )
	{
	// E-mail button and Export buttons
	if($dms_config['document_email_enable']=='1')
		{
		print "<a href='obj_email.php?obj_id=".$obj_id."&return_url=file_options.php?obj_id=".$obj_id."'>". _DMS_EMAIL. "</a>&nbsp;&nbsp;";
		}
	
	print "<a href='file_retrieve.php?function=export&obj_id=".$obj_id."'>" . _DMS_EXPORT . "</a>\r";
	}
// Exit Button
//print "<BR><a href='#' onclick='exit_to_main_page();'>" . _DMS_EXIT . "</a><BR>\r";

print "  <BR><BR>";
print "  <a href='#' onmouseover='fo_shutdown();'>[Close]</a>\r";


print "</td></tr>\r";
/*
print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
print "<a href='#' onmouseover='fo_shutdown();'>[Close]</a>\r";
print "</td></tr>\r";
*/
print "</table>\r";

print "</div>\r";

print "<script language='JavaScript'>\r";
print "<!--\r";
print "fo_moveLayers();\r";
print "loaded = 1;\r";
print "// -->\r";
print "</script>\r";



	  
	// Javascript to check if a Document Name Exists
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "function exit_to_main_page()\r";
	print "  {\r";
	print "  var exit_page = 1;\r";
	if ( ($perms_level == EDIT) || ($perms_level == OWNER) )  
		{
		print "  //var frm_options = document.forms.namedItem(\"frm_options\");\r";
		print "  if ( document.frm_options.txt_obj_name.value == \"\" )\r";
		print "    {\r";
		print "    alert('Please enter a document name.');\r";
		print "    document.frm_options.txt_obj_name.focus();\r";
		print "    exit_page = 0;\r";
		print "    }\r";
		}
	print "  if (exit_page == 1) location=\"index.php\";\r";
	print "  }\r";
	print "</SCRIPT>\r";
    
	// Add the version_view() javascript function
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "function version_view()\r";
	print "  {\r";
	print "  if (document.frm_ver_view.slct_version_view.value == 0) return;\r";
	print "  var url = 'file_retrieve.php?function=vv&obj_id=".$obj_id."&ver_id=';\r";
	print "  url = url + document.frm_ver_view.slct_version_view.value;\r";
	print "  window.open(url);\r";
	print "  }\r";
	print "</SCRIPT>\r";
	
	// Add to the document history
	dms_doc_history($obj_id);
	
	print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>\r";
  
	print "  <tr>\r";
  
	print "    <td valign='top'>\r";
	print "      <table border='0' valign='top'>\r";

	print "      <tr><td><table valign='top' cellpadding='0' cellspacing='0'>\r";
  
	dms_display_header(2);
   
	print "        <tr><td colspan='2'><BR></td></tr>\r";

	// Display the document name (linked to view it) and display the version view drop-down box.
	print "        <form name='frm_ver_view'>\r";
	
	print "        <tr>\r";
	print "          <td align='left' style='text-align: left' width='65%'>\r";
		
	if ($perms_level > BROWSE)
		print "<a href='#' title='View Document' onclick='javascript:void(window.open(\"file_retrieve.php?function=view&obj_id=".$obj_id."\"))'><font size='3'><b>".$object->obj_name."</b></font></a>\r";
	else
		print "<font size='3'><b>".$object->obj_name."</b></font>\r";
		  
	print "          </td>\r";
	
	print "          <td align='right'>\r";
		
	if ( ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) ) && ($dms_config['checkinout_enable'] == 1) )  
		{
	
		//print "        <form name='frm_ver_view'>\r";
		
		print "        View older version:  ";
		
		print "            <select name='slct_version_view' onchange='version_view();'>\r";
		print "              <option value='0'>" . _DMS_NONE . "</option>\r";
  
		$query  = "SELECT row_id,major_version,minor_version,sub_minor_version FROM ".$dmsdb->prefix('dms_object_versions')." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$result = $dmsdb->query($query);
  
		while($result_data = $dmsdb->getarray($result))
			{
			print "              <option value='".$result_data['row_id']."'>";
			print $result_data['major_version'].".".$result_data['minor_version'].$result_data['sub_minor_version'];
			print "</option>\r";
			}
    
		print "            </select>\r";
		}

	print "          </td>\r";
	print "        </tr>\r";
	
	print "        </form>\r";
	
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	
	print "    <td align='left' valign='top' colspan='2' ".$dms_config["class_content"].">\r";
	if ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) ) 
		{
		// Options Menu
		print "      <input type='button' name='btn_options' value='"._DMS_OPTIONS."' onmouseover='fo_grabMouseX(event); moveLayerY(\"div_menu\", currentY, event); fo_popUpMenu();'>";
		print "&nbsp;&nbsp;";
		// Options Menu End
		}
		
	// Information Button
	print "      <input type='button' name='btn_info' value='Information' onclick='location=\"file_options.php?obj_id=".$obj_id."#info\";'>&nbsp;&nbsp;";
	
	// Permissions Button
	if ( $perms_level == OWNER )
		print "      <input type='button' name='btn_perms' value='Permissions' onclick='location=\"file_options.php?obj_id=".$obj_id."#perms_set\";'>&nbsp;&nbsp;";
	
	// Optional Help Button
	dms_help_system("file_options",10);
		
	// Exit Button
	print "      <input type='button' name='btn_exit' value='"._DMS_EXIT."' onclick='location=\"index.php\";'>";
	print "    </td>\r";
	// Exit Button End

	print "        <tr><td colspan='2'><BR></td></tr>\r";

	
	print "     </table></td></tr>\r";
	
	
	
	print "      <tr><td><table border='0' valign='top' cellpadding='0' cellspacing='0'>\r";
	
	// Display the properties
	print "        <form method='post' name='frm_options' action='file_options.php'>\r";

	print "        <tr>\r";
	print "          <td colspan='1' align='left' ".$dms_config['class_subheader'].">&nbsp;" . _DMS_PROPERTIES . "</td>\r";
	print "          <td align='right' ".$dms_config['class_subheader'].">";
	dms_help_system("file_options_properties");
	print "          </td>\r";
	print "        </tr>\r";
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	print "        <tr>\r";
	print "          <td align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_NAME_DOT . "</td>";
  
	if ( ($perms_level == EDIT) || ($perms_level == OWNER) )  
		{
		print "          <td align='left'><input type='text' name=txt_obj_name value=\"".$object->obj_name."\" size='40' maxlength='250' tabindex='100'></td>\r";
		}
	else
		{
		print "          <td align='left'>".$object->obj_name."</td>\r";
		}
	
	print "        </tr>\r";
  
	if ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) )
		{
		display_file_properties($obj_id,3);
    
		print "        <tr><td colspan='2'><BR></td></tr>\r";
 
		if ( ($perms_level == EDIT) || ($perms_level == OWNER) )
			{
			print "        <tr>\r";
			print "          <td colspan='2' align='left'>\r";
			print "            &nbsp;&nbsp;&nbsp;<input type=submit name='btn_update' value='" . _DMS_UPDATE_PROPERTIES . "' tabindex='200'>";
			print "          </td>\r";
			print "        </tr>\r";
			print "        <tr><td colspan='2'><BR></td></tr>\r";
			}
  
		print "        <input type='hidden' name='hdn_update_options' value='confirm'>\r";
		print "        <input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
		print "        <input type='hidden' name='hdn_cancel_checkout' value='false'>\r";
		}
	print "        <tr><td colspan='2'><BR></td></tr>\r";

	print "        </form>\r";

	// Display comments
	if ( ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) ) && ($dms_config['comments_enable'] == 1) )
		{
		$query = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
		$current_version_row_id = $dmsdb->query($query,"current_version_row_id");

		$query  = "SELECT row_id, comment FROM ".$dmsdb->prefix("dms_object_version_comments")." ";
		$query .= " WHERE dov_row_id='".$current_version_row_id."'";
		$result = $dmsdb->query($query,"ROW");
		$num_rows = $dmsdb->getnumrows();

		if($perms_level == READONLY) $readonly = "READONLY";
		else $readonly = "";
		
		print "        <form method='post' name='frm_options' action='file_options.php'>\r";
		print "        <tr><td colspan='1' align='left' ".$dms_config['class_subheader'].">&nbsp;Comments:</td>\r";
		
		print "          <td align='right' ".$dms_config['class_subheader'].">";
		dms_help_system("file_options_comments");
		print "          </td>\r";
		print "        </tr>\r";
		
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		
		print "        <tr><td colspan='2' align='left' ".$dms_config['class_content'].">\r";
		print "          &nbsp;&nbsp;&nbsp;<textarea name='txt_comments' rows='4' cols='80' ".$readonly." tabindex='300'>";
		if($num_rows > 0) print $result->comment;
		print "</textarea>\r";
		print "        </td></tr>\r";
	
		if($perms_level != READONLY)
			{
			print "        <tr><td colspan='2'><BR></td></tr>\r";
			print "        <tr>\r";
			print "          <td colspan='2' align='left'>\r";
			print "            &nbsp;&nbsp;&nbsp;<input type=submit name='btn_update' value='Update Comments' tabindex='310'>";
			print "          </td>\r";
			print "        </tr>\r";
			}
		
		if($num_rows > 0) $dovc_row_id = $result->row_id;
		else $dovc_row_id = 0;
			
		print "        <input type='hidden' name='hdn_update_comments' value='confirm'>\r";
		print "        <input type='hidden' name='hdn_dovc_row_id' value='".$dovc_row_id."'>\r";
		print "        <input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		print "        </form>\r";
		}
		
	
	//  Document Expiration
	if ($dms_config['doc_expiration_enable'] == TRUE)
		{
		print "        <form method='post' name='frm_document_expiration' action='file_options.php'>\r";
		print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;Document Expiration:</td></tr>\r";

		print "        <tr><td colspan='2'><BR></td></tr>\r";
		print "        <tr><td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;Enable Document Expiration:&nbsp;&nbsp;&nbsp;";
		
		$chk_document_expiration_enable = "";
		if($object->time_stamp_expire > 0) $chk_document_expiration_enable = " CHECKED";
		print "<input type='checkbox' name='chk_document_expiration_enable'".$chk_document_expiration_enable.">";
		print "        </td></tr>\r";

		print "        <tr><td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;Expire Document On:&nbsp;&nbsp;&nbsp;";
		dms_get_date("time_stamp_expire",$object->time_stamp_expire);
		print "</td></tr>\r";
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		
		print "        <tr><td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;";
		print "<input type='submit' name='btn_document_expiration_update' value='Update'>";
		print "        </td></tr>\r";
		
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		
		print "        <input type='hidden' name='hdn_obj_id_doc_exp' value='".$obj_id."'>\r";
		print "        <input type='hidden' name='hdn_update_doc_exp' value='confirm'>\r";
		print "        </form>\r";
		}
	
	// Document has been checked-out
		     
	if ($checked_out == TRUE)
		{
		print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;" . _DMS_CHECKED_OUT_BY . "</td></tr>\r";

		$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." WHERE uid='".$object->obj_checked_out_user_id."'";
		$result = $dmsdb->query($query,'ROW');
		//mysql_fetch_object(mysql_query($query));
	
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		print "        <tr><td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;".$result->uname."</td></tr>\r";
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		}
  
	// Display document information
	print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;";
	print "<a name='info'></a>\r";
	print "Information:</td></tr>\r";
	print "        <tr><td colspan='2'><BR></td></tr>\r";

	//if ( ($perms_level == BROWSE) || ($perms_level == READONLY) )
		//{
	print "        <tr>\r";
	print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;" . _DMS_DOC_OWNER . "</td>";
	print "          <td align='left'>".$dms_users->get_username(dms_perms_owner_user_id($obj_id))."</td>\r";
		//print "          <td align='left'>".$xoopsUser->getUnameFromId(dms_perms_owner_user_id($obj_id))."</td>\r";
	print "        </tr>\r";
		//}

	
	print "        <tr>\r";
	print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;Location:</td>";
	print "          <td align='left'>";
	
	$obj_path = dms_get_obj_path($obj_id);
	
	$index = $obj_path['total_num_objects'];
	$index--;
	
	while($index >= 0)
		{
		print $obj_path['obj_name'][$index];
		if($index != 0) print ", ";
		
		$index--;
		}
	
	

	print "          </td>\r";
	print "        </tr>\r";
	    
	print "        <tr>\r";
	print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;" . _DMS_PERMISSION_LEVEL . "</td>";
	print "          <td align='left'>";
  
	switch ($perms_level)
		{
		case BROWSE:
			print _DMS_BROWSE;
			break;
		case READONLY:
			print _DMS_READ_ONLY;
			break;
		case EDIT:
			print _DMS_EDIT;
			break;
		case OWNER:
			print _DMS_OWNER;
			break;
		}
  
	print "          </td>\r";
	print "        </tr>\r";

	if($dms_config['checkinout_enable'] == 1)
		{
		print "        <tr>\r";
		print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;" . _DMS_CURRENT_VERSION . "</td>";
		print "          <td align='left'>".$current_version->major_version.".".$current_version->minor_version.$current_version->sub_minor_version."</td>\r";
		print "        </tr>\r";
		}
	
	if($object->lifecycle_id >0)
		{
		$query  = "SELECT lifecycle_name FROM ".$dmsdb->prefix("dms_lifecycles")." WHERE ";
		$query .= "lifecycle_id = '".$object->lifecycle_id."'";
		$lifecycle_name = $dmsdb->query($query,'lifecycle_name');
		
		$query  = "SELECT lifecycle_stage_name FROM ".$dmsdb->prefix("dms_lifecycle_stages")." WHERE ";
		$query .= "lifecycle_id = '".$object->lifecycle_id."' AND lifecycle_stage = '".$object->lifecycle_stage."'";
		$lifecycle_stage_name = $dmsdb->query($query,'lifecycle_stage_name');
		
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;Lifecycle Name:</td>\r";
		print "          <td align='left'>".$lifecycle_name."</td>\r";
		print "        </tr>\r";
	
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;Lifecycle Stage:</td>\r";
		print "          <td align='left'>".$lifecycle_stage_name."</td>\r";
		print "        </tr>\r";
		}
		
	if ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) )
		{
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_CREATED . "</td>";
    
		if ($object->time_stamp_create == 0) 
			{
			print "          <td align='left'>" . _DMS_NA . "</td>\r";
			}
		else
			{  
			print "          <td align='left'>".strftime("%d-%B-%Y %I:%M%p",$object->time_stamp_create)."</td>\r";
    			}
    
		print "        </tr>\r";
		
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_MODIFIED . "</td>";
    
		if ($current_version->time_stamp == 0) 
			{
			print "          <td align='left'>" . _DMS_NA . "</td>\r";
			}
		else
			{  
    		print "          <td align='left'>".strftime("%d-%B-%Y %I:%M%p",$current_version->time_stamp)."</td>\r";
			}
	  
		print "        </tr>\r";
  
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_SIZE . "</td>";
		print "          <td align='left'>".$current_version->file_size." bytes</td>\r";
		print "        </tr>\r";

		// If this document was created with a template, display the name of the template    
		if ($object->template_obj_id > 0)
			{
			// Get object information
			$query  = "SELECT obj_name ";
			$query .= "FROM ".$dmsdb->prefix("dms_objects")." ";
			$query .= "WHERE obj_id='".$object->template_obj_id."'";  
			$template_object = $dmsdb->query($query,'obj_name'); //mysql_fetch_object(mysql_query($query));
		
			print "        <tr>\r";
			print "          <td align='left'>&nbsp;&nbsp;&nbsp;" . _DMS_TEMPLATE_NAME . "</td>";
			print "          <td align='left'>".$template_object."</td>\r";
			print "        </tr>\r";
			}
	
		// If subscriptions are enabled and the user is subscribed, print "Subscribed"
		if( ($dms_config['sub_email_enable']=='1') && ( ($perms_level == EDIT) ||  ($perms_level == OWNER) ) )
			{
			//print "       <tr><td colspan='2'><BR></td></tr>\r";
			print "        <tr>\r";
			print "          <td align='left'>&nbsp;&nbsp;&nbsp;Subscribed:</td>\r";
			if($subscribed==TRUE) 	print "          <td align='left'>Yes</td>\r";
			else 					print "          <td align='left'>No</td>\r";
			print "        </tr>\r";
			}
			
		// Display link to the audit log for admins only
		if ($dms_admin_flag == 1)
			{
			print "       <tr><td colspan='2'><BR></td></tr>\r";
			print "        <tr>\r";
			print "          <td align='left'>&nbsp;&nbsp;&nbsp;<a href='audit_log_obj.php?obj_id=".$obj_id."'>" . _DMS_AUDIT_LOG . "</a></td>\r";
			print "          <td align='left'></td>\r";
			print "        </tr>\r";
			}
  
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		}

  
	print "        </table></td></tr>\r";

	if ( $perms_level == OWNER )
		{ 
		print "        <tr>\r";
		print "          <td colspan='2'>\r";
		
		include 'inc_perms_set.php';
 
		print "          </td>\r";
		print "        </tr>\r";
		}
  
	print "      </table>\r";

	print "    </td>\r";
	print "  </tr>\r";
	print "</table>\r";


/* 
foreach ($GLOBALS as $key=>$value)
	{
	print "\$GLOBALS[\"$key\"]==$value<br>";
	}
*/  
	include 'inc_pal_footer.php';
	//include_once XOOPS_ROOT_PATH.'/footer.php';
	}

dms_show_mb();
	
?>
