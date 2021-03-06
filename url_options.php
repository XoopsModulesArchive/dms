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

// url_options.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

$option_button_width=" style='width: 6em;' ";
//$folder_flag = "TRUE";

import_request_variables("P","post_");
$this_file = "url_options.php";  // Add the filename of this file here.

//if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
//else $obj_id = $HTTP_GET_VARS['obj_id'];

$obj_id = dms_get_var("hdn_obj_id");
if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");

$perms_level = dms_perms_level($obj_id);

if (dms_get_var("hdn_update_options") == "confirm" )
	{
	$obj_name = dms_strprep(dms_get_var("txt_obj_name") );
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET ";
	$query .= "obj_name='".$obj_name."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	dms_auditing($obj_id,"url/update properties");
	
	//print "<SCRIPT LANGUAGE='Javascript'>\r";
	//header("Location:url_options.php?obj_id=".$obj_id);
	
	dms_header_redirect("url_options.php?obj_id=".$obj_id);
	//print "</SCRIPT>";  
	}
else
	{
	//include XOOPS_ROOT_PATH.'/header.php';
	include 'inc_pal_header.php';
	
	// Get object information
	$query  = "SELECT obj_status, obj_type, obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$result = $dmsdb->query($query,'ROW');
	
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id='".$obj_id."' AND data_type='".URL."'";
	$url = $dmsdb->query($query,"data");
	
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
	print "function uo_grabMouseX(e) {\r";
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
	
	print "function uo_popUpMenu() {\r";
	print "uo_shutdown();\r";
	print "setleft('div_menu',currentX);\r";
	print "popUp(\"div_menu\",true);\r";
	print "}\r";
	
	print "function uo_moveLayers() {\r";
	print "uo_grabMouseX;\r";
	print "setleft('div_menu',currentX);\r";
	print "settop('div_menu',currentY);\r";
	print "}\r";
	
	print "function uo_shutdown() {\r";
	print "popUp('div_menu',false);\r";
	print "}\r";
	
	print "if (NS4) {\r";
	print "document.onmousedown = function() { uo_shutdown(); }\r";
	print "} else {\r";
	print "document.onclick = function() { uo_shutdown(); }\r";
	print "}\r";
	
	print "// -->\r";
	print "</script>\r";
	
	print "<div id='div_menu' style='position: absolute; visibility: hidden; z-index:1000;'>\r";
	
	print "<table ".$dms_config['class_narrow_header']." width='150' cellspacing='1' style='width: 6em;'>\r";
	
	print "<th nowrap='nowrap' align='center'>Options</th>\r";
	
	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";
	

	if ($result->obj_status == DELETED)
		{
		print "<a href='obj_restore.php?obj_id=".$obj_id."'>" . _DMS_RESTORE . "</a><BR>\r";
		}
	else
		{
		print "<a href='url_copy.php?obj_id=".$obj_id."'>Copy</a>&nbsp;\r";
		print "<a href='url_move.php?obj_id=".$obj_id."'>Move</a>&nbsp;\r";
		print "<a href='obj_delete.php?obj_id=".$obj_id."'>Delete</a><BR>\r";
		}
		
	print "  <BR>";
	print "  <a href='#' onmouseover='uo_shutdown();'>[Close]</a>\r";
	
	print "</td></tr>\r";
	/*
	print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	print "</td></tr>\r";
	*/
	print "</table>\r";
	
	print "</div>\r";
	
	print "<script language='JavaScript'>\r";
	print "<!--\r";
	print "uo_moveLayers();\r";
	print "loaded = 1;\r";
	print "// -->\r";
	print "</script>\r";
	
	// END TEST
	
	
	
	print "<table width='100%' border='0'>\r";
	
	print "  <tr>\r";
	
	print "    <td>\r";
	print "      <table border='0' cellpadding='0' cellspacing='0'>\r";
	
	display_dms_header(2);
	
	print "      <tr><td colspan='2'><BR></td></tr>\r";

	// Options Menu 
	print "      <tr>\r";
	print "        <td align='left' valign='top' colspan='2' ".$dms_config['class_content']." >\r";
	
	if($perms_level == OWNER || $dms_admin_flag == 1)
		{
		print "          <input type='button' name='btn_options' value='"._DMS_OPTIONS."' onmouseover='uo_grabMouseX(event); moveLayerY(\"div_menu\", currentY, event); uo_popUpMenu();'>&nbsp;&nbsp;";
		}
			
	// Permissions Button
	if($perms_level == OWNER || $dms_admin_flag == 1)
	  print "      <input type='button' name='btn_perms' value='Permissions' onclick='location=\"url_options.php?obj_id=".$obj_id."#perms_set\";'>&nbsp;&nbsp;";
	
	// Optional Help Button
	dms_help_system("folder_options",10);
	  
	print "          <input type='button' name='btn_exit' value='"._DMS_EXIT."' onclick='location=\"index.php\";'>";
	print "        </td>\r";
	print "      </tr>\r";
	
		
	// Options Menu End
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	// Display properties
	print "        <form method='post' name='frm_properties' action='url_options.php'>\r";
	
	print "        <tr><td colspan='1' align='left' ".$dms_config['class_subheader'].">&nbsp;Properties:</td>\r";
	print "          <td align='right' ".$dms_config['class_subheader'].">";
	dms_help_system("file_options_properties");
	print "          </td>\r";
	print "        </tr>\r";
	
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	print "        <tr>\r";
	print "          <td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;" . _DMS_NAME_DOT . "";
	dms_display_spaces(5);
	//print '          <td align="left">
	print '          <input type="text" name=txt_obj_name value="'.$result->obj_name.'" size="40" maxlength="250">'."\r";
	print "          </td>\r";
	print "        </tr>\r";
	
	if($perms_level == EDIT || $perms_level == OWNER)
		{
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		print "        <tr>\r";
		print "          <td colspan='2' align='left'>\r";
		print "            &nbsp;&nbsp;&nbsp;<input type=submit name='btn_submit' value='" . _DMS_UPDATE_PROPERTIES . "'>";
		print "          </td>\r";
		print "        </tr>\r";
		}
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	
	print "        <input type='hidden' name='hdn_update_options' value='confirm'>\r";
	print "        <input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
	print "        <input type='hidden' name='hdn_cancel_checkout' value='false'>\r";
	print "        </form>\r";
	
	if ($dms_admin_flag == 1)
		{
		// Display information
		print "        <tr><td colspan='2' align='left' ".$class_subheader.">&nbsp;" . _DMS_INFORMATION . "</td></tr>\r";
		print "        <tr><td colspan='2'><BR></td></tr>\r";
			
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;";
		
		print "<a href='audit_log_obj.php?obj_id=".$obj_id."'>" . _DMS_AUDIT_LOG . "</a>";
			
		print "          </td>\r";
		
		print "          <td align='left'></td>\r";
		print "        </tr>\r";
		
		print "        <tr><td colspan='2'><BR></td></tr>\r";
		}
	
	if($perms_level == OWNER || $dms_admin_flag == 1)  // Only allow changes to the permissions if the user is the owner of the url entry.
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
	
	//include_once XOOPS_ROOT_PATH.'/footer.php';
	include 'inc_pal_footer.php';
	}

dms_show_mb();
	
/*
foreach ($GLOBALS as $key=>$value)
	{
	print "\$GLOBALS[\"$key\"]==$value<br>";
	}
*/

/*
foreach ($post_slct_group as $key=>$value)
	{
	print "\$post_slct_group[\"$key\"]==$value<br>";
	}

foreach ($post_slct_user as $key=>$value)
	{
	print "\$post_slct_user[\"$key\"]==$value<br>";
	}
	
*/

?>
