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


import_request_variables("P","post_");
$this_file = "perms_editor.php";  // Add the filename of this file here.

//if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
//else $obj_id = $HTTP_GET_VARS['obj_id'];

$obj_id = dms_get_var("hdn_obj_id");
if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");

/*
$lifecycle_id = "";
$lifecycle_stage = "";
*/

include XOOPS_ROOT_PATH.'/header.php';

if(dms_get_var('hdn_prop_update') == "TRUE")
	{
	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." SET obj_name = '".$HTTP_POST_VARS['txt_obj_name']."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	$value = 0;
	if ($HTTP_POST_VARS['chk_owner']) 	$value += 1;
	if ($HTTP_POST_VARS['chk_everyone']) 	$value += 2;
	if ($HTTP_POST_VARS['chk_groups']) 	$value += 4;
	if ($HTTP_POST_VARS['chk_users']) 	$value += 8;

	$query  = "UPDATE ".$dmsdb->prefix("dms_object_misc")." SET data = '".$value."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);
	}

print "<form method='post' name='frm_perms_editor' action='perms_editor.php'>\r";
print "<table width='100%'>\r";

//  display_dms_header();

print "  <tr>\r";
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>Permissions Group Management Editor</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "        <table>\r";
print "          <tr>\r";
print "            <td align='left' ".$dms_config['class_content'].">\r";
print "              <input type='button' name='btn_exit' value='Exit' onclick='location=\"perms_manager.php\";'>\r";
print "            </td>\r";
print "          </tr>\r";
print "        </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_subheader'].">\r";
print "            &nbsp;Permissions Group Properties:\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content']." width='30%'>\r";
print "            Name:\r";
print "          </td>\r";

$query = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
$obj_name = $dmsdb->query($query,"obj_name");

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            <input type='text' name='txt_obj_name' value='".$obj_name."'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' colspan='1' ".$dms_config['class_content']." width='30%'>\r";
print "            Permissions to Change:\r";
print "          </td>\r";

$query = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." WHERE obj_id='".$obj_id."' AND data_type='".PERMS_GROUP."'";
$change_perms_data = $dmsdb->query($query,"data");

$mask = 1;
for($index = 0; $index < 4; $index++)
	{
	if( ($change_perms_data & $mask) == $mask) $checked[$index] = "CHECKED";
	else $checked[$index] = "";
	$mask *= 2;
	}

print "          <td align='left' colspan='1' ".$dms_config['class_content'].">\r";
print "            O<input type='checkbox' name='chk_owner' ".$checked[0].">&nbsp\r";
print "            E<input type='checkbox' name='chk_everyone' ".$checked[1].">&nbsp;\r";
print "            G<input type='checkbox' name='chk_groups' ".$checked[2].">&nbsp;\r";
print "            U<input type='checkbox' name='chk_users' ".$checked[3].">&nbsp;\r";
print "          </td>\r";
print "        </tr>\r";

print "      </table>\r";

print "      <BR>\r";

print "      <div align='left'><input type='button' name='btn_prop_update' value='Update' ".$dms_config['class_content']." onclick='frm_perms_editor.submit();'></div>\r";

print "    </td>\r";

print "  </tr>\r";

print "<input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
print "<input type='hidden' name='hdn_prop_update' value='TRUE'>\r";
print "</form>\r";


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
