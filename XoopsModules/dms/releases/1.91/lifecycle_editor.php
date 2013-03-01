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

$function="";
$lifecycle_id = "";
$lifecycle_stage_0_flag = "FALSE";

if(dms_get_var("hdn_function") != FALSE) 
	{
	$function = dms_get_var("hdn_function");
	$lifecycle_id = dms_get_var("hdn_lifecycle_id");
	}
else 
	{
	$function = dms_get_var("function");
	$lifecycle_id = dms_get_var("lifecycle_id");
	}
 
if($lifecycle_id == "")
	{
	print _DMS_INVALID_LIFECYCLE_ID;
	exit(0);
	}  

// S_IPS
import_request_variables("P","post_");
$this_file = "lifecycle_editor.php?lifecycle_id=".$lifecycle_id;  // Add the filename of this file here.

$obj_id = dms_get_var("hdn_obj_id");
if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");

// E_IPS
     
if ($function=="NEW")
	{
	// Create an object for the new lifecycle stage.
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type) VALUES (";
	$query .= "'21')";
	$dmsdb->query($query);
	$obj_id = $dmsdb->getid();
  
	// Create a new lifecycle stage.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_lifecycle_stages')." (lifecycle_id,obj_id)";
	$query .= " VALUES ('".$lifecycle_id."','".$obj_id."')";
	$dmsdb->query($query);
	
	$obj_id = 0;
	}

if ($function=="DELETE")
	{
	// Get the $obj_id of the lifecycle_stage
	$query  = "SELECT obj_id FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
	$query .= "lifecycle_id='".$HTTP_POST_VARS['hdn_lifecycle_id']."' AND ";
	$query .= "lifecycle_stage='".$HTTP_POST_VARS["hdn_lifecycle_stage"]."'";
	$obj_id = $dmsdb->query($query,'obj_id');
  
	// Delete the lifecycle stage
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
	$query .= "lifecycle_id='".$HTTP_POST_VARS["hdn_lifecycle_id"]."' AND ";
	$query .= "lifecycle_stage='".$HTTP_POST_VARS["hdn_lifecycle_stage"]."'";
	$dmsdb->query($query);

	// Delete the object for the stage
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_objects')." WHERE ";
	$query .= "obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	// Delete the perms for the stage
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_perms')." WHERE ";
	$query .= "ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	$obj_id = '';
	}
  
if ($function=="UPDATE")
  {
  // Update the lifecycle properties
  $query  = "UPDATE ".$dmsdb->prefix('dms_lifecycles')." SET ";
  $query .= "lifecycle_name='".$HTTP_POST_VARS["txt_lifecycle_name"]."', ";
  $query .= "lifecycle_descript='".$HTTP_POST_VARS["txt_lifecycle_descript"]."' ";
  $query .= "WHERE lifecycle_id='".$HTTP_POST_VARS["hdn_lifecycle_id"]."'";
  $dmsdb->query($query);
  }
  
if ( !($obj_id > 0) )
	{
	// Get the $obj_id of the lifecycle.
	$query  = "SELECT obj_id FROM ".$dmsdb->prefix('dms_lifecycles')." ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."'";
	$obj_id = $dmsdb->query($query,'obj_id');
	}
  
  include XOOPS_ROOT_PATH.'/header.php';

  print "<SCRIPT LANGUAGE='Javascript'>\r";
  print "  function new_lifecycle_stage()\r";
  print "    {\r";
  print "    if (document.frm_lifecycle_editor.hdn_lifecycle_stage_0_flag.value=='FALSE')\r";
  print "      {\r";
  print "      document.frm_lifecycle_editor.hdn_function.value='NEW';\r";
  print "      document.frm_lifecycle_editor.submit();\r";
  print "      }\r";
  print "    else alert(\"" . _DMS_DUP_LIFECYCLE_ALERT . "\");\r";
  print "    }\r";
  print "  function delete_lifecycle_stage(lifecycle_stage)\r";
  print "    {\r";
  print "    document.frm_lifecycle_editor.hdn_function.value='DELETE';\r";
  print "    document.frm_lifecycle_editor.hdn_lifecycle_stage.value=lifecycle_stage;\r";
  print "    document.frm_lifecycle_editor.submit();\r";
  print "    }\r";
  print "  function update_lifecycle_properties()\r";
  print "    {\r";
  print "    document.frm_lifecycle_editor.hdn_function.value='UPDATE';\r";
  print "    document.frm_lifecycle_editor.submit();\r";
  print "    }\r";
  print "</SCRIPT>\r";  
  
  print "<form method='post' name='frm_lifecycle_editor' action='lifecycle_editor.php'>\r";
  print "<table width='100%'>\r";
  
//  display_dms_header();
  
  print "  <tr>\r";
  print "    <td valign='top'>\r";
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td colspan='1' ".$class_header.">\r";
  print "            <center><b><font size='2'>" . _DMS_LIFECYCLE_EDITOR . "</font></b></center>\r";
  print "          </td>\r";
  print "        </tr>\r";
  print "      </table>\r";
  
  print "      <BR>\r";
  
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td align='left' ".$class_content.">\r";
  print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"lifecycle_manager.php\";'>\r";
  print "          </td>\r";
  print "        </tr>\r";

  print "      </table>\r";
  
  
  print "      <BR>\r";

  $query  = "SELECT lifecycle_name, lifecycle_descript FROM ".$dmsdb->prefix('dms_lifecycles')." ";
  $query .= "WHERE lifecycle_id='".$lifecycle_id."'";
  $result = $dmsdb->query($query,'ROW');
  
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td align='left' colspan='1' ".$class_subheader.">\r";
  print "            " . _DMS_LIFECYCLE_PROPERTIES . ":\r";
  print "          </td>\r";
  print "        </tr>\r";
  print "      </table>\r";

  print "      <table>\r";
  print "        <tr>\r";
  print "          <td align='left'>\r";
  print "            " . _DMS_NAME . ":&nbsp;&nbsp;&nbsp;\r";
  print "          </td>\r";
  
  print "          <td align='left' width='100%'>\r";
  print "            <input type='text' name='txt_lifecycle_name' value='".$result->lifecycle_name."' ".$class_content." size='20' maxlength='250'>\r";
  print "          </td>\r";
  print "        </tr>\r";
  
  print "        <tr>\r";
  print "          <td align='left'>\r";
  print "            " . _DMS_DESCRIPTION . ":&nbsp;&nbsp;&nbsp;\r";
  print "          </td>\r";
  
  print "          <td align='left'>\r";
  print "            <input type='text' name='txt_lifecycle_descript' value='".$result->lifecycle_descript."' ".$class_content." size='50' maxlength='250'>\r";
  print "          </td>\r";
  print "        </tr>\r";
  
  print "        <tr>\r";
  print "          <td align='left' colspan='2'>\r";
  print "            <input type='button' name='btn_update' value='" . _DMS_UPDATE . "' ".$class_content." onclick='update_lifecycle_properties();'>\r";
  print "          </td>\r";
  print "        </tr>\r";
  
  print "      </table>\r";
  
  print "      <BR>\r";
  
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td align='left' colspan='1' ".$class_subheader.">\r";
  print "            " . _DMS_LIFECYCLE_STAGES . "\r";
  print "          </td>\r";
  print "        </tr>\r";
  print "      </table>\r";

    
  $query  = "SELECT lifecycle_stage, lifecycle_stage_name ";
  $query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
  $query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
  $query .= "ORDER BY lifecycle_stage";
  $result = $dmsdb->query($query);

  print "      <BR><div align='left'><input type='button' name='btn_new' value='" . _DMS_NEW . "' onclick='new_lifecycle_stage();'></div><BR><BR>\r";
    
  print "      <table width='100%' border='1' ".$class_content.">\r";
 
  print "        <tr>\r";
  print "          <td align='left' ".$class_content." width='5%'>\r";
  print "            <u>" . _DMS_STAGE . "</u>\r";
  print "          </td>\r";

  print "          <td align='left' ".$class_content.">\r";
  print "            <u>Name</u>\r";
  print "          </td>\r";
  
  print "          <td align='left' width='20%' ".$class_content.">\r";
  print "            <u>" . _DMS_OPTIONS . "</u>\r";
  print "          </td>\r";
  
  print "        </tr>\r";
   
  while($result_data = $dmsdb->getarray($result))
    {
    if ($result_data['lifecycle_stage']==0) $lifecycle_stage_0_flag = "TRUE";
	
	print "        <tr>\r";
    print "          <td align='left'>\r";
    
	if ($result_data['lifecycle_stage'] == 0) 
	 print "            " . _DMS_NEW_UPPER;
	else
	 print "            ".$result_data['lifecycle_stage'];
    
	print "          </td>\r";

	print "          <td align='left'>\r";
	print "            ".$result_data['lifecycle_stage_name'];
	print "          </td>\r";
	
    print "          <td align='left'>\r";
    print "            <a href='lifecycle_stage_editor.php?lifecycle_id=".$lifecycle_id."&lifecycle_stage=".$result_data['lifecycle_stage']."'>" . _DMS_EDIT . "</a>";
	print "            &nbsp;&nbsp;&nbsp;";
	print "            <a href='javascript:delete_lifecycle_stage(".$result_data['lifecycle_stage'].");'>" . _DMS_DELETE . "</a>\r"; 
    print "          </td>\r";
  
    print "        </tr>\r";
	}
  
  print "      </table>\r";
  print "    </td>\r";
  
  print "  </tr>\r";


  print "<input type='hidden' name='hdn_function' value=''>\r";
  print "<input type='hidden' name='hdn_lifecycle_id' value='".$lifecycle_id."'>\r";
  print "<input type='hidden' name='hdn_lifecycle_stage' value=''>\r";
  print "<input type='hidden' name='hdn_lifecycle_stage_0_flag' value='".$lifecycle_stage_0_flag."'>\r";
  print "</form>\r";
 
  
  // S_IPS  
  print "  <tr><td><BR></td></tr>\r";
  
  print "        <tr>\r";

  print "          <td colspan='2'>\r";
  print "          <a name='perms_set'></a>\r";
  
  //include 'inc_perms_set.php';

  print "          </td>\r";
  print "        </tr>\r";
  // E_IPS
  
  print "</table>\r";
  
  include_once XOOPS_ROOT_PATH.'/footer.php';

  
?>
