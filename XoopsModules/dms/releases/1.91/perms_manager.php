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
// perms_manager.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

//if($HTTP_POST_VARS["hdn_function"]) $function = $HTTP_POST_VARS["hdn_function"];
//else $function = $HTTP_GET_VARS["function"];
$function = dms_get_var("hdn_function");
if ($function == FALSE) $function = dms_get_var("function");


if ($function=="NEW")
	{
	// Create an object for the new permissions group
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type,obj_name) VALUES (";
	$query .= "'".PERMISSION."','New')";
	$dmsdb->query($query);
	$obj_id = $dmsdb->getid();
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_misc")." (obj_id,data_type,data) VALUES (";
	$query .= "'".$obj_id."','".PERMS_GROUP."','15')";
	$dmsdb->query($query);
	$obj_id = $dmsdb->getid();
	}

if ($function=="DELETE")
	{
	// Get the $obj_id of the permissions group.
	$obj_id=$HTTP_POST_VARS['hdn_obj_id'];
	
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_objects')." WHERE ";
	$query .= "obj_id='".$obj_id."'";
	$dmsdb->query($query);

	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_perms')." WHERE ";
	$query .= "ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_misc')." WHERE ";
	$query .= "obj_id='".$obj_id."'";
	$dmsdb->query($query);
	}
  

include XOOPS_ROOT_PATH.'/header.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  function new_perms_group()\r";
print "    {\r";
print "    document.frm_perms_mgr.hdn_function.value='NEW';\r";
print "    document.frm_perms_mgr.submit();\r";
print "    }\r";
print "  function delete_perms_group(perms_group_id)\r";
print "    {\r";
print "    document.frm_perms_mgr.hdn_function.value='DELETE';\r";
print "    document.frm_perms_mgr.hdn_obj_id.value=perms_group_id;\r";
print "    document.frm_perms_mgr.submit();\r";
print "    }\r";
print "</SCRIPT>\r";  

print "<form method='post' name='frm_perms_mgr' action='perms_manager.php'>\r";
print "<table width='100%'>\r";

//  display_dms_header();

print "  <tr>\r";
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>Permissions Group Management</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";

print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <input type='button' name='btn_new' value='New' onclick='new_perms_group();'>\r";
print "            &nbsp;&nbsp;\r";
print "            <input type='button' name='btn_exit' value='Exit' onclick='location=\"index.php\";'>\r";
print "          </td>\r";
print "        </tr>\r";

print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_subheader'].">\r";
print "            Permissions Groups:\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

$query = "SELECT obj_id,obj_name FROM ".$dmsdb->prefix('dms_objects')." WHERE obj_type='".PERMISSION."' ORDER BY obj_name";
$result = $dmsdb->query($query);

print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%'>\r";
print "            <u>" . _DMS_NAME . "</u>\r";
print "          </td>\r";

print "          <td width='20%' ".$dms_config['class_content'].">\r";
print "            <u>" . _DMS_OPTIONS . "</u>\r";
print "          </td>\r";

print "        </tr>\r";

while($result_data = $dmsdb->getarray($result))
	{
	print "        <tr>\r";
	print "          <td align='left'>\r";
	print "            ".$result_data['obj_name'];
	print "          </td>\r";
	print "          <td align='center'>\r";
	print "            <a href='perms_editor.php?obj_id=".$result_data['obj_id']."'>Edit</a>";
	print "            &nbsp;&nbsp;&nbsp;";
	print "            <a href='javascript:delete_perms_group(".$result_data['obj_id'].");'>Delete</a>\r"; 
	print "          </td>\r";
	
	print "        </tr>\r";
	}

print "      </table>\r";
print "    </td>\r";

print "  </tr>\r";
print "</table>\r";

print "<input type='hidden' name='hdn_function' value=''>\r";
print "<input type='hidden' name='hdn_obj_id' value=''>\r";

print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

  
?>
