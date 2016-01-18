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

$function = dms_get_var("function");
if($function == FALSE) $function = dms_get_var("hdn_function");

if ($function=="NEW")
	{
	// Create a new lifecycle.

	// Create an object for the new lifecycle
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type) VALUES (";
	$query .= "'20')";
	$dmsdb->query($query);
	$obj_id = $dmsdb->getid();

	// Create the new lifecycle
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_lifecycles')." (obj_id,lifecycle_name,lifecycle_descript)";
	$query .= " VALUES ('".$obj_id."','__New','__New')";
	$dmsdb->query($query);
	}

if ($function=="DELETE")
	{
	$lifecycle_id = dms_get_var("hdn_lifecycle_id");
	
	// Get the $obj_id of the lifecycle.
	$query  = "SELECT obj_id FROM ".$dmsdb->prefix('dms_lifecycles')." ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."'";
	$obj_id = $dmsdb->query($query,'obj_id');

	// Find all of the stags of the lifecycle and delete them
	
	// ADD CODE HERE!!!!!
	
	// Delete the lifecycle
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_lifecycles')." WHERE ";
	$query .= "lifecycle_id='".$lifecycle_id."'";
	$dmsdb->query($query);

	$query  = "DELETE FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
	$query .= "lifecycle_id='".$lifecycle_id."'";
	$dmsdb->query($query);

	$query  = "DELETE FROM ".$dmsdb->prefix('dms_objects')." WHERE ";
	$query .= "obj_id='".$obj_id."'";
	$dmsdb->query($query);

	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_perms')." WHERE ";
	$query .= "ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET ";
	$query .= "lifecycle_id='0', ";
	$query .= "lifecycle_stage='0', ";
	$query .= "lifecycle_suspend_flag='0' ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."'";
	$dmsdb->query($query);
	}
  

include XOOPS_ROOT_PATH.'/header.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  function new_lifecycle()\r";
print "    {\r";
print "    document.frm_lifecycle_mgr.hdn_function.value='NEW';\r";
print "    document.frm_lifecycle_mgr.submit();\r";
print "    }\r";
print "  function delete_lifecycle(lifecycle_id)\r";
print "    {\r";
print "    document.frm_lifecycle_mgr.hdn_function.value='DELETE';\r";
print "    document.frm_lifecycle_mgr.hdn_lifecycle_id.value=lifecycle_id;\r";
print "    document.frm_lifecycle_mgr.submit();\r";
print "    }\r";
print "</SCRIPT>\r";  

print "<form method='post' name='frm_lifecycle_mgr' action='lifecycle_manager.php'>\r";
print "<table width='100%'>\r";

//  display_dms_header();

print "  <tr>\r";
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>" . _DMS_LIFECYCLE_MANAGEMENT . "</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";

print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <input type='button' name='btn_new' value='" . _DMS_NEW . "' onclick='new_lifecycle();'>\r";
print "            &nbsp;&nbsp;\r";
print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"index.php\";'>\r";
print "          </td>\r";
print "        </tr>\r";

print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_subheader'].">\r";
print "            " . _DMS_LIFECYCLES  . ":\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

$query = "SELECT lifecycle_id, lifecycle_name, lifecycle_descript FROM ".$dmsdb->prefix('dms_lifecycles')." ORDER BY lifecycle_name";
$result = $dmsdb->query($query);

print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%'>\r";
print "            <u>" . _DMS_NAME . "</u>\r";
print "          </td>\r";

print "          <td ".$dms_config['class_content'].">\r";
print "            <u>" . _DMS_DESCRIPTION . "</u>\r";
print "          </td>\r";

print "          <td width='20%' ".$dms_config['class_content'].">\r";
print "            <u>" . _DMS_OPTIONS . "</u>\r";
print "          </td>\r";

print "        </tr>\r";

while($result_data = $dmsdb->getarray($result))
	{
	print "        <tr>\r";
	print "          <td align='left' style='text-align: left;'>\r";
	print "            ".$result_data['lifecycle_name'];
	print "          </td>\r";
	
	print "          <td align='left' style='text-align:  left;'>\r";
	print "            ".$result_data['lifecycle_descript'];
	print "          </td>\r";
		
	print "          <td align='center' style='text-align:  center;'>\r";
	print "            <a href='lifecycle_editor.php?lifecycle_id=".$result_data['lifecycle_id']."'>" . _DMS_EDIT . "</a>";
	print "            &nbsp;&nbsp;&nbsp;";
	print "            <a href='javascript:delete_lifecycle(".$result_data['lifecycle_id'].");'>" . _DMS_DELETE . "</a>\r"; 
	print "          </td>\r";
	
	print "        </tr>\r";
	}

print "      </table>\r";
print "    </td>\r";

print "  </tr>\r";
print "</table>\r";

print "<input type='hidden' name='hdn_function' value=''>\r";
print "<input type='hidden' name='hdn_lifecycle_id' value=''>\r";

print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

  
?>
