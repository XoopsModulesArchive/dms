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

include 'inc_pal_header.php';

//include XOOPS_ROOT_PATH.'/header.php';

//include_once 'inc_dynamic_menus.php';

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
		
		print "    <td ".$class." align='left' colspan='3'>".$level_offset."<a title='File'><img src='images/file.png'></a>&nbsp;&nbsp;&nbsp;\r";
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

		$active_folder = $new_active_folder;
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
	
//include_once XOOPS_ROOT_PATH.'/footer.php';
include_once 'inc_pal_footer.php';
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
