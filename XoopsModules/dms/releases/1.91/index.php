<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 5/13/2003                                //
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

// index.php

include '../../mainfile.php';


include_once 'inc_current_version.php';
include_once 'inc_dms_functions.php';

include XOOPS_ROOT_PATH.'/header.php';

// Main interface functions

function display_db_version_diff()
	{
	global $dms_current_version, $dms_config, $dms_admin_flag;
	
	print "<tr><td>\r";
	print "WARNING:  The Document Management System module has been updated but is still configured for a previous version.<BR>\r";
	print "&nbsp;&nbsp;&nbsp;Please run the Update Manager to bring the system up-to-date.\r";
	print "<BR><BR>\r";
	print "Current Version:  ".$dms_current_version."<BR>\r";
	print "Previous Version:  ".$dms_config['version']."\r";;
	
	if($dms_admin_flag == 1)
		{
		print "<BR><BR>\r";
		print "<input name='btn_update_manager' type='button' value='Update Manager' onclick='location=\"./admin/update_manager.php\";'>\r";
		}
	
	print "</td></tr>\r";
	}

function display_main_interface_options()
	{
	global $active_folder_type, $active_folder, $active_folder_perms, $dms_admin_flag, $template_root_folder, $dms_config;

	print "  <tr><td colspan='3'><BR></td></tr>\r";
	print "  <tr>\r";
	//print "    <td width='60%'><img src='images/help.gif' title='Help'><BR></td>\r";
	print "    <td width='40%' style='text-align: left;'>";
	dms_help_system("index",3);
	print "    </td>\r";
	
	if( ( ($active_folder_type == FOLDER) 
	&& ( ( ($active_folder!=0) && ( ($active_folder_perms == EDIT) || ($active_folder_perms == OWNER) ) ) ) 
	&& ($active_folder_type != DISKDIR) )
	|| ( ($active_folder == 0) && ($dms_admin_flag == 1) )
	)
		{
		print "  <td width='35%' align='right' valign='top'>";
	
		if ($template_root_folder != 0)
			print "    <a href='file_new.php'><img src='images/menu/filenew.gif' title='Create Document'></a>&nbsp;&nbsp;";
	
		print "    <a href='file_import.php'><img src='images/menu/fileimport.gif' title='Import Document'></a>&nbsp;&nbsp;";

		if ($dms_config['OS'] == "Linux") 
			{
			print "    <a href='file_batch_import.php' title='Import Multiple Documents'><img src='images/menu/batch_import.gif'></a>&nbsp;&nbsp;";
			}

		print "    <a href='url_add.php'><img src='images/menu/www.gif' title='Add Web Page'></a>&nbsp;&nbsp;";
		print "    <a href='folder_new.php'><img src='images/menu/foldernew.gif' title='Create Folder'></a>";
		print "  </td>\r";
		}
	else
		{
		print "    <td width='25%' align='left'><BR></td>";
		}

	if ($dms_config['full_text_search'] == '1')
		{
		print "    <td width='25%' align='right' valign='top'><a href='#' onmouseover='grabMouseX(event); moveLayerY(\"div_menu_search\", currentY, event); popUpSearchMenu();'><img src='images/menu/filefind.gif' title='Search'></a>&nbsp;&nbsp;";
		}
	else 
		{
		print "    <td width='25%' align='right' valign='top'><a href='search_prop.php'><img src='images/menu/filefind.gif' title ='Search'></a>&nbsp;&nbsp;";
		}
	
	if ($dms_admin_flag == 1) 
		{
		print "<a href='#' onmouseover='grabMouseX(event); moveLayerY(\"div_menu_admin\", currentY, event); popUpAdminMenu();'><img src='images/menu/configure.gif'></a";
		}
	
	print "      </td></tr>\r";
	
	print "    <tr><td></td></tr>\r";
	//print "  <tr><td colspan='3' align='left'><a href='folder_close_all.php'>" . _DMS_CLOSE_ALL_FOLDERS  . "</a></td></tr>\r";
	}

function dms_admin_menu()
	{
	global $dms_config;

	print "<div id='div_menu_admin' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

	print "<table ".$dms_config['class_narrow_header']." width='120' cellspacing='1' style='width: 12em;'>\r";

	print "<th nowrap='nowrap' align='center'>Administration</th>\r";

	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";

	print "<a href='audit_log_select_user.php'>Auditing</a><BR>";
	print "<a href='group_editor.php'>Group Editor</a><BR>";
	print "<a href='job_server_manager.php'>Job Server</a><BR>";
	print "<a href='lifecycle_manager.php'>Lifecycles</a><BR>";
	print "<a href='perms_manager.php'>Permissions Groups</a><BR>";
	print "<a href='statistics.php'>Statistics</a><BR>";
	print "<BR>";
	print "<a href='config_main.php'>Configuration</a><BR>";
	
	print "<BR><a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	
	print "</td></tr>\r";

	/*
	print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	print "</td></tr>\r";
*/
	print "</table>\r";

	print "</div>\r";
	}

	
function dms_search_menu()
	{
	global $dms_config;

	print "<div id='div_menu_search' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

	print "<table ".$dms_config['class_narrow_header']." width='120' cellspacing='1' style='width: 12em;'>\r";

	print "<th nowrap='nowrap' align='center'>Search</th>\r";

	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";

	print "<a href='search_ft.php'>Full Text</a><BR>";
	print "<a href='search_prop.php'>Properties</a><BR>";

	
	print "<BR>";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	
	print "</td></tr>\r";
/*
	print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	print "</td></tr>\r";
*/
	print "</table>\r";

	print "</div>\r";
	}

	
function dms_dhtml_menu_functions()
	{
	global $dms_config;

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
	print "function grabMouseX(e) {\r";
	print "  if ((DOM && !IE4) || Opera5) {\r";
	print "    currentX = e.clientX;\r";
	print "    } else if (NS4) {\r";
	print "    currentX = e.pageX;\r";
	print "    } else {\r";
	print "    currentX = event.x;\r";
	print "    }\r";
	
	print "currentX = currentX - 120;\r";
	/*
	print "  if (DOM && !IE4 && !Opera5 && !Konqueror) {\r";
	print "    currentX += window.pageXoffset;\r";
	print "      } else if (IE4 && DOM && !Opera5 && !Konqueror) {\r";
	print "      currentX += document.body.scrollLeft;\r";
	print "    }\r";
	*/
	print "  }\r";


	print "function popUpAdminMenu() {\r";
	print "shutdown();\r";
	print "setleft(\"div_menu_admin\",currentX);\r";
	print "popUp(\"div_menu_admin\",true);\r";
	print "}\r";

	print "function popUpSearchMenu() {\r";
	print "shutdown();\r";
	print "setleft(\"div_menu_search\",currentX);\r";
	print "popUp(\"div_menu_search\",true);\r";
	print "}\r";
	
	print "function moveLayers() {\r";
	print "grabMouseX;\r";
	print "setleft(\"div_menu_admin\",currentX);\r";
	print "settop(\"div_menu_admin\",currentY);\r";
	
	print "setleft(\"div_menu_search\",currentX);\r";
	print "settop(\"div_menu_search\",currentY);\r";
	
	print "}\r";

	print "function shutdown() {\r";
	print "popUp(\"div_menu_admin\",false);\r";
	print "popUp(\"div_menu_search\",false);\r";
	print "}\r";

	print "if (NS4) {\r";
	print "document.onmousedown = function() { shutdown(); }\r";
	print "} else {\r";
	print "document.onclick = function() { shutdown(); }\r";
	print "}\r";

	print "moveLayers();\r";
	print "loaded = 1;\r";
	print "// -->\r";
	print "</script>\r";
	}
	
define('SEPARATOR_LIMIT',3);
$separator_counter = 0;
function display_separator()
	{
	global $separator_counter;
	
	$bg_image="images/line.png";
	
	//$separator_counter ++;
	if ($separator_counter > SEPARATOR_LIMIT)
		{
		print "  <tr>\r";
		print "    <td height='1' background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "  <td background='".$bg_image."' nowrap></td>\r";
		print "  </tr>\r";
		
		$separator_counter = 1;
		}      
	}

function list_disk_dir($obj_id, $interface_type = "MULTIPLE")
	{
	global $active_folder,$dms_config,$level,$separator_counter,$dmsdb;
	
	// If this folder is not active, exit out of the function. 
	if($obj_id != $active_folder) return(0);
	
	$bg_color="";
	$bg_image="images/line.png";
    
	// Set up display offsets
	$level_offset="";
	$index=0;
	while($index < $level)
		{
		$level_offset .= "&nbsp;&nbsp;&nbsp;";	
		$index++;
		}

	if  ( $active_folder != 0  && ($obj_id == $active_folder) )
		$class = $dms_config['class_subheader']; //"class='cSubHeader'";
	else $class = "";

	if($interface_type = "SINGLE") $class = "";
	
	if($dms_config['default_interface'] == 2) $class="";
	
	// Get the directory to display
	$query  = "SELECT data from ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id=".$obj_id." AND data_type='".PATH."'";
	$dir = $dmsdb->query($query,'data');

	$file_list = array();
	
	$counter = 0;
	$handle = opendir($dir);
	while( ($file = readdir($handle) ) != false)
		{
		if($file =='.' || $file =='..') continue;

		if("file" == filetype($dir."/".$file))
			{
			$file_list[$counter] = $file."\n";
			$counter++;
			}
		}
	
	closedir($handle);
	
	sort($file_list);
	
	$counter = 0;
	while($file_list[$counter])
		{
		$separator_counter++;
		display_separator();
		
		print "<tr>\r";
		
		print "    <td ".$class." align='left' colspan='2'>".$level_offset."<a title='File'><img src='images/file.png'></a>&nbsp;&nbsp;&nbsp;\r";
		print "<a title='Click to import.' href='file_dir_import.php?obj_id=".$obj_id."&obj_num=".$counter."'>".$file_list[$counter]."</a>\r";
		print "    </td>\r";
		
		print "  <td></td>\r";
		print "  <td></td>\r";
		print "  <td></td>\r";
		print "  <td></td>\r";
		
		print "</tr>\r";
		
		$counter++;
		}
	}

// Automatically create inboxes, if enabled.
if($dms_config['routing_auto_inbox'] == 1)
	{
	//  See if the user has an inbox, if there isn't an inbox, create one.
	if(dms_inbox_id($dms_user_id) == 0) dms_folder_create($dms_users->get_username($dms_user_id),0,INBOXEMPTY);
	}

$new_active_folder = dms_get_var("folder_id");
if($new_active_folder != FALSE) 
	{
	//Ensure that the user has the permission to enter this folder.  If not, break.
	$perms_level = dms_perms_level($new_active_folder);
	if($perms_level >= BROWSE)
		{
		//Make sure that this folder is not marked as expanded in order to prevent multiple entries.
		$query  = "DELETE FROM ".$dmsdb->prefix("dms_exp_folders");
		$query .= " WHERE user_id='".$dms_user_id."' and folder_id='".$new_active_folder."'";
		$dmsdb->query($query);
	
		dms_set_inbox_status($new_active_folder);
	
		// Make sure that this folder, or any other folder, is not marked as active.
		$query = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";
		$dmsdb->query($query);
	
		// Set the folder as expanded
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_exp_folders")." (user_id,folder_id) VALUES ('".$dms_user_id."','".$new_active_folder."')";
		$dmsdb->query($query);
	
		// Set the folder as active
		$query = "INSERT INTO ".$xoopsDB->prefix("dms_active_folder")." (user_id,folder_id) VALUES ('".$dms_user_id."','".$new_active_folder."')";
		$dmsdb->query($query);
		}
	}

	
// IF DMS display is permitted, load User Interface based upon dms_config.default_interface setting.
if($dms_disp_flag == "TRUE")
	{
	// Message Box
	include_once 'inc_message_box.php';
	dms_message_box();
	dms_dhtml_mb_functions(0);
	
	//include_once 'inc_message_box.php';
	
	if($dms_config['default_interface'] == 1) include_once 'inc_main_ui_1.php';
	if($dms_config['default_interface'] == 2) include_once 'inc_main_ui_2.php';
	if($dms_config['default_interface'] == 3) include_once 'inc_main_ui_3.php';
	if($dms_config['default_interface'] == 4) include_once 'inc_main_ui_4.php';
	
	dms_show_mb();
	}
	
include_once XOOPS_ROOT_PATH.'/footer.php';

/*
  foreach ($GLOBALS as $key=>$value)
    {
	print "\$GLOBALS[\"$key\"]==$value<br>";
	}

print "--------------------------<BR>";
*/
 
//print $HTTP_SERVER_VARS['SCRIPT_FILENAME'];

/*
  foreach ($HTTP_SERVER_VARS as $key=>$value)
    {
	print "\$HTTP_SERVER_VARS[\"$key\"]==$value<br>";
	}
*/

/*
  foreach ($_SESSION as $key=>$value)
    {
	print "\$_SESSION[\"$key\"]==$value<br>";
	}
*/  
  
/*
  foreach ($dms_config as $key=>$value)
    {
	print "\$dms_config[\"$key\"]==$value<br>";
	}
*/  

/*
  foreach ($group_list as $key=>$value)
    {
	print "\$group_list[\"$key\"]==$value<br>";
	}
*/
	
?>
