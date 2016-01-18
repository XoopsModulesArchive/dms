<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 7/22/2003                                //
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

$lifecycle_id = "";
$lifecycle_stage = "";

if(dms_get_var("hdn_function") != FALSE) 
	{
	$function = dms_get_var("hdn_function");
	$lifecycle_id = dms_get_var("hdn_lifecycle_id");
	$lifecycle_stage = dms_get_var("hdn_lifecycle_stage");
	}
else 
	{
	$function = dms_get_var("function");
	$lifecycle_id = dms_get_var("lifecycle_id");
	$lifecycle_stage = dms_get_var("lifecycle_stage");
	}
 
if($lifecycle_id == "")
	{
	print _DMS_INVALID_LIFECYCLE_ID;
	exit(0);
	}  

if($lifecycle_stage == "")
	{
	print _DMS_INVALID_LIFECYCLE_STAGE;
	exit(0);
	}  

// S_IPS
import_request_variables("P","post_");
$this_file = "lifecycle_stage_editor.php?lifecycle_id=".$lifecycle_id."&lifecycle_stage=".$lifecycle_stage;

$obj_id = dms_get_var("hdn_obj_id");
if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");
// E_IPS
  
    
if ($function == "CONTRACT")
	{
	$sql_query  = "DELETE FROM ".$dmsdb->prefix("dms_exp_folders");
	$sql_query .= " WHERE user_id='".$dms_user_id."' and folder_id='".$HTTP_POST_VARS["hdn_folder_id"]."'";
	$dmsdb->query($sql_query);
	
	// Make sure that this folder cannot be marked as active
	$sql_query  = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." ";
	$sql_query .= "WHERE user_id='".$xoopsUser->getVar('uid')."' AND folder_id='".$HTTP_POST_VARS["hdn_folder_id"]."'";
	$dmsdb->query($sql_query);
	} 
  
if ($function == "EXPAND")
	{
	//Make sure that this folder is not marked as expanded in order to prevent multiple entries.
	$sql_query  = "DELETE FROM ".$dmsdb->prefix("dms_exp_folders");
	$sql_query .= " WHERE user_id='".$xoopsUser->getVar('uid')."' and folder_id='".$HTTP_POST_VARS["hdn_folder_id"]."'";
	$dmsdb->query($sql_query);
	
	// Make sure that this folder, or any other folder, is not marked as active.
	$sql_query = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";
	$dmsdb->query($sql_query);
		
	// Set the folder as expanded
	$sql_query  = "INSERT INTO ".$dmsdb->prefix("dms_exp_folders")." ";
	$sql_query .= "(user_id,folder_id) VALUES ('".$dms_user_id."','".$HTTP_POST_VARS["hdn_folder_id"]."')";
	$dmsdb->query($sql_query);
	
	// Set the folder as active
	$sql_query  = "INSERT INTO ".$dmsdb->prefix("dms_active_folder")." ";
	$sql_query .= "(user_id,folder_id) VALUES ('".$xoopsUser->getVar('uid')."','".$HTTP_POST_VARS["hdn_folder_id"]."')";
	$dmsdb->query($sql_query);
	}   
  
if ($function == "SELECT")
	{
	if($HTTP_POST_VARS['chk_change_perms_flag']) $change_perms_flag = 1;
	else $change_perms_flag = 0;
	
	if($HTTP_POST_VARS['chk_foo_copy_flag']) $foo_copy_flag = 1;
	else $foo_copy_flag = 0;
	
	$flags = $change_perms_flag + ($foo_copy_flag * 2);
	
	$sql_query  = "UPDATE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$sql_query .= "SET ";
	$sql_query .= "new_obj_location='".$HTTP_POST_VARS["hdn_folder_id"]."', ";
	$sql_query .= "lifecycle_stage='".$HTTP_POST_VARS["txt_lifecycle_stage"]."', ";
	$sql_query .= "lifecycle_stage_name='".$HTTP_POST_VARS["txt_lifecycle_stage_name"]."',";
	$sql_query .= "opt_obj_copy_location='".$HTTP_POST_VARS["txt_opt_obj_copy_location"]."',";
	$sql_query .= "flags='".$flags."',";
	$sql_query .= "perms_group_id='".$HTTP_POST_VARS["slct_perms_group_id"]."' ";
	$sql_query .= "WHERE row_id='".$HTTP_POST_VARS["hdn_row_id"]."'";
	$dmsdb->query($sql_query);

	$lifecycle_stage = $HTTP_POST_VARS["txt_lifecycle_stage"];
  
	$location  = "location='lifecycle_stage_editor.php?";
	$location .= "lifecycle_id=".$HTTP_POST_VARS["hdn_lifecycle_id"]."&";
	$location .= "lifecycle_stage=".$HTTP_POST_VARS["txt_lifecycle_stage"]."';";
	
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print $location;
	print "</SCRIPT>";  
	}
  
// Get the lifecycle_stage_name of the lifecycle_stage
$query  = "SELECT * FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
$query .= "lifecycle_id='".$lifecycle_id."' AND ";
$query .= "lifecycle_stage='".$lifecycle_stage."'";
$result = $dmsdb->query($query,'ROW');

$lifecycle_stage_name = $result->lifecycle_stage_name;
$opt_obj_copy_location = $result->opt_obj_copy_location;
$flags=$result->flags;
$perms_group_id = $result->perms_group_id;

$change_perms_flag = 0;
$foo_copy_flag = 0;

$mask = 1;
for($index = 0; $index < 4; $index++)
	{
	if( ($flags & $mask) == $mask)
		switch ($index)
			{
			case 0:		$change_perms_flag = 1;		break;
			case 1:		$foo_copy_flag = 1;		break;
			}
		
		$checked[$index] = "CHECKED";
	$mask *= 2;
	}

include XOOPS_ROOT_PATH.'/header.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";

print "function contract_folder(folder_id)\r";
print "  {\r";
print "  document.frm_lifecycle_stage_editor.hdn_function.value='CONTRACT';\r";
print "  document.frm_lifecycle_stage_editor.hdn_folder_id.value=folder_id;\r";
print "  document.frm_lifecycle_stage_editor.submit();\r";  
print "  }\r";
print "function expand_folder(folder_id)\r";
print "  {\r";
print "  document.frm_lifecycle_stage_editor.hdn_function.value='EXPAND';\r";
print "  document.frm_lifecycle_stage_editor.hdn_folder_id.value=folder_id;\r";
print "  document.frm_lifecycle_stage_editor.submit();\r";  
print "  }\r";
print "function select_dest_folder()\r";
print "  {\r";
print "  var value = 0;\r";
print "  for (var i=0; i < document.frm_lifecycle_stage_editor.rad_folder_id.length; i++)\r";
print "    {\r";
print "    if(document.frm_lifecycle_stage_editor.rad_folder_id[i].checked)\r";
print "     value = document.frm_lifecycle_stage_editor.rad_folder_id[i].value;\r";
print "    }\r";
print "    if(value > 0)\r";
print "      {\r";
print "      document.frm_lifecycle_stage_editor.hdn_function.value='SELECT';\r";
print "      document.frm_lifecycle_stage_editor.hdn_folder_id.value=value;\r";
print "      document.frm_lifecycle_stage_editor.submit();\r";  
print "      }\r";
print "    else\r";
print "      alert(\"" . _DMS_DESTINATION_FOLDER_ALERT . "\");\r";
print "  }\r";

print "</SCRIPT>\r";  

print "<form method='post' name='frm_lifecycle_stage_editor' action='lifecycle_stage_editor.php'>\r";
print "<table width='100%'>\r";

//  display_dms_header();

print "  <tr>\r";
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>" . _DMS_LIFECYCLE_STAGE_EDITOR . "</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "        <table>\r";
print "          <tr>\r";
print "            <td align='left' ".$dms_config['class_content'].">\r";
print "              <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"lifecycle_editor.php?lifecycle_id=".$lifecycle_id."\";'>\r";
print "            </td>\r";
print "          </tr>\r";
print "        </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_subheader'].">\r";
print "            Lifecycle Stage Properties:\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content']." width='30%'>\r";
print "            Stage Number:\r";
print "          </td>\r";

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='text' name='txt_lifecycle_stage' value='".$lifecycle_stage."'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            Name:\r";
print "          </td>\r";

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='text' name='txt_lifecycle_stage_name' value='".$lifecycle_stage_name."' size='50' maxlength='200'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            Optional Copy Destination Folder ID:\r";
print "          </td>\r";

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='text' name='txt_opt_obj_copy_location' value='".$opt_obj_copy_location."' size='15' maxlength='15'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            Leave a Copy in the Folder of Origin:\r";
print "          </td>\r";

$checked = $foo_copy_flag;
if ($checked == '0') $checked = "";
else $checked = " checked";

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='checkbox' name='chk_foo_copy_flag'".$checked.">\r";
print "          </td>\r";
print "        </tr>\r";


print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            Change Permissions:\r";
print "          </td>\r";

$checked = $change_perms_flag;
if ($checked == '0') $checked = "";
else $checked = " checked";

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='checkbox' name='chk_change_perms_flag'".$checked.">\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            Permissions Group:\r";
print "          </td>\r";

$query = "SELECT obj_id,obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_type = '".PERMISSION."' ORDER BY obj_name";
$result = $dmsdb->query($query);

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <select name='slct_perms_group_id' ".$dms_config['class_content'].">\r";
print "              <option value='0'>None</option>\r";

while($result_data = $dmsdb->getarray($result))
	{
print "loop";
	if($perms_group_id == $result_data['obj_id']) $selected = " SELECTED";
	else $selected = "";
	print "              <option value='".$result_data['obj_id']."' ".$selected.">".$result_data['obj_name']."</option>\r";
	}

print "            </select>\r";

print "          </td>\r";
print "        </tr>\r";


print "      </table>\r";


print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            " . _DMS_DESTINATION_FOLDER . ":\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";


$query  = "SELECT name ";
$query .= "FROM ".$dmsdb->prefix('dms_lifecycles')." ";
$query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
$lifecycle_result = $dmsdb->getarray($dmsdb->query($query));

$query  = "SELECT row_id, lifecycle_stage, new_obj_location ";
$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
$query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
$query .= "AND lifecycle_stage='".$lifecycle_stage."'";
$lifecycle_stage_result = $dmsdb->getarray($dmsdb->query($query));
  
//////////////////////   
// Folder Display Code
//////////////////////
  
//include_once 'inc_perms_check.php';
//include_once 'defines.php';

$level = 0;
function list_folder($folder_owner)
	{
	global $active_folder, $exp_folders, $file_id, $group_query, $level, $lifecycle_stage;
	global $lifecycle_stage_result, $location, $dmsdb, $xoopsUser, $dms_user_id;
	
	$bg_color="";
	$user_id = $dms_user_id;
	
	// Set up display offsets
	$index=0;
	$level_offset = "";
	while($index < $level)
		{
		$level_offset .= "&nbsp;&nbsp;&nbsp;";	
		$index++;
		}
	
	// If the user is an administrator, ignore the permissions entirely.
	if ($xoopsUser->isAdmin())
		{
		$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE (obj_owner='".$folder_owner."' AND obj_status < '2') ";
		$query .= "ORDER BY obj_type DESC, obj_name";
		}
	else
		{
		$query  = "SELECT obj_id, ".$dmsdb->prefix("dms_objects").".ptr_obj_id, obj_type, obj_name, obj_status, obj_owner, obj_checked_out_user_id, ";
		$query .= "user_id, group_id, user_perms, group_perms, everyone_perms ";
		$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ON ";
		$query .= $dmsdb->prefix("dms_object_perms").".ptr_obj_id = obj_id ";
		$query .= "WHERE (obj_owner='".$folder_owner."') ";
		$query .= " AND (";
		$query .= "    everyone_perms !='0'";
		$query .= $group_query;
		$query .= " OR user_id='".$user_id."'";
		$query .= ")";
		$query .= " AND (obj_status < '2') ";
		$query .= "GROUP BY obj_id ";
		$query .= "ORDER BY obj_type DESC, obj_name";
		}
		
	//print $query;
	//exit(0);
		
	
	//  $result = mysql_query($query) or die(mysql_error());
	
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			if($xoopsUser->isAdmin())  $perm = OWNER;
			else                       $perm = perms_level($result_data['obj_id']);
		
			// Set class to the active background color
			$class = "";
	
			// If this object is a folder, display it.
			if($result_data['obj_type'] == FOLDER)
				{
				if ($result_data['obj_id'] == $lifecycle_stage_result['new_obj_location']) $radio_btn_string = " checked";
				else $radio_btn_string = "";

				print "  <tr>\r";
		
				$index = 0;
				$exp_flag = 0;

				// Is folder expanded?
				while($exp_folders[$index] != -1)
					{ 
					if ($exp_folders[$index] == $result_data['obj_id']) $exp_flag = 1;
					$index++;
					}
			
				// Display standard folders
				if ($result_data['obj_type']==FOLDER)
					{
					if (($exp_flag==1) && ($perm > BROWSE))
						{
						print "    <td align='left' nowrap ".$class.">";
						print "&nbsp;&nbsp;<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'".$radio_btn_string.">";
						print $level_offset;
						print "<a href='javascript:contract_folder(".$result_data['obj_id'].");'>";  // contract folder
						print "<img src='images/folder_open.png'></a>&nbsp;&nbsp;&nbsp;\r";
						}
					else
						{
						if ($perm > BROWSE)
							{
							print "    <td align='left' nowrap ".$class.">";
							print "&nbsp;&nbsp;<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'".$radio_btn_string.">";
							print $level_offset;
							print "<a href='javascript:expand_folder(".$result_data['obj_id'].");'>"; // expand folder
							print "<img src='images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						else
							{
							print "    <td align='left' nowrap ".$class.">";
							print "&nbsp;&nbsp;<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'".$radio_btn_string.">";
							print $level_offset;
							print "<img src='images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						}
					} 
	
				// If folder is not active, display the name and link to make it active, otherwise just display the name.
				if (($result_data['obj_id'] == $active_folder) || ($perm == BROWSE))
					{
					print "    ".$result_data['obj_name']."</td>\r";
					}   
				else
					{
					print "    <a href='javascript:expand_folder(".$result_data['obj_id'].");'>"; // expand folder
					print $result_data['obj_name']."</a></td>\r";  
					}
					
				print "    </td>\r";
			
				if (($exp_flag == 1) && ($perm > BROWSE))
					{
					$level++;
					list_folder($result_data['obj_id']);
					$level--;
					}
				}
	
			}
		}
	}

  
// get list of groups that this user is a member of and create part of the query
// also, place these groups into an array for later use
$group_list = $xoopsUser->getGroups();
$group_array = array();
$index = 0;

$group_query = "";
do  
	{
	$group_query .= " OR group_id='".current($group_list)."'";
	$group_array[$index] = current($group_list);
	
	$index++;
	} while(next($group_list));
  
// Get list of expanded folders
$query = sprintf("SELECT * FROM %s WHERE user_id='%s'",$dmsdb->prefix("dms_exp_folders"),$dms_user_id);
$result = $dmsdb->query($query);

$index = 0;
while($result_data = $dmsdb->getarray($result))
	{
	$exp_folders[$index]=$result_data['folder_id'];  
	$index++;
	} 
$exp_folders[$index]=-1;

// Get active folder
$query = "SELECT folder_id from ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
$active_folder = $dmsdb->query($query,'folder_id');
if(!$active_folder>=1) $active_folder=0;

// Get the object type of the active folder, if applicable
if ($active_folder!=0)
	{
	$query = "SELECT obj_type from ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$active_folder."'";
	$active_folder_type = $dmsdb->query($query,'obj_type');
	}
else
	{
	$active_folder_type = 0;
	}
        
// List all folders

print "<table>\r";
list_folder(0);
print "</table>\r";



//////////////////////////  
// End Folder Display Code
//////////////////////////
    
print "      <BR>\r";

print "      <div align='left'><input type='button' name='btn_dest_update' value='" . _DMS_UPDATE . "' ".$class_content." onclick='select_dest_folder();'></div>\r";

print "    </td>\r";

print "  </tr>\r";

print "<input type='hidden' name='hdn_function' value=''>\r";
print "<input type='hidden' name='hdn_row_id' value='".$lifecycle_stage_result["row_id"]."'>\r";
print "<input type='hidden' name='hdn_folder_id' value=''>\r";
print "<input type='hidden' name='hdn_lifecycle_id' value='".$lifecycle_id."'>\r";
print "<input type='hidden' name='hdn_lifecycle_stage' value='".$lifecycle_stage."'>\r";

print "</form>\r";


// S_IPS  
  
  
// Get the $obj_id of the lifecycle.
$query  = "SELECT obj_id FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
$query .= "lifecycle_id='".$lifecycle_id."' AND ";
$query .= "lifecycle_stage='".$lifecycle_stage."'";
$obj_id = $dmsdb->query($query,'obj_id');
  
//print "oi:  ".$obj_id;

print "  <tr><td><BR></td></tr>\r";

print "        <tr>\r";

print "          <td colspan='2'>\r";
print "          <a name='perms_set'></a>\r";

include 'inc_perms_set.php';

print "          </td>\r";
print "        </tr>\r";
// E_IPS


print "</table>\r";


include_once XOOPS_ROOT_PATH.'/footer.php';
  
?>
