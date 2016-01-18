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
// lifecycle_apply.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';

$function="";
$lifecycle_id = "";
$lifecycle_stage_0_flag = "FALSE";

if(dms_get_var("hdn_function") != FALSE) 
	{
	$function = dms_get_var("hdn_function");
	$file_id = dms_get_var("hdn_file_id");
	$lifecycle_id = dms_get_var("rad_lifecycle_id");
	}
else 
	{
	$file_id = dms_get_var("obj_id");
	}
 
if(dms_get_var("hdn_function") == "APPLY")
	{
	$dest_folder = dms_apply_lifecycle($file_id,$lifecycle_id);

	dms_auditing($file_id,"document/lifecycle/apply id=".$lifecycle_id.",dest folder=".$dest_folder);
	
	dms_message("A Lifecycle has been applied to the selected document.");

	// Return to the options screen
	//header("Location:file_options.php?obj_id=".$file_id);
	
	dms_header_redirect("file_options.php?obj_id=".$file_id);
	}

  include XOOPS_ROOT_PATH.'/header.php';

  print "<SCRIPT LANGUAGE='Javascript'>\r";
  print "  function apply_lifecycle()\r";
  print "    {\r";
  print "    document.frm_lifecycle_apply.submit();\r";
  print "    }\r";
  print "</SCRIPT>\r";  
  
  print "<form method='post' name='frm_lifecycle_apply' action='lifecycle_apply.php'>\r";
  print "<table width='100%'>\r";
  
//  display_dms_header();
  
  print "  <tr>\r";
  
  // Content
  print "    <td valign='top' align='left'>\r";
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td colspan='1' ".$dms_config['class_header'].">\r";
  print "            <center><b><font size='2'>" . _DMS_APPLY_LIFECYCLE . "</font></b></center>\r";
  print "          </td>\r";
  print "        </tr>\r";
  print "      </table>\r";
  
  print "      <BR>\r";

  print "      <input type='button' name='btn_apply' value='" . _DMS_APPLY . "' onclick='apply_lifecycle();'>\r";
  print "      &nbsp;&nbsp;\r";
  print "      <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"file_options.php?obj_id=".$file_id."\";'>\r";
  
  print "      <BR><BR>\r";
  
  print "      <table>\r";
  print "        <tr>\r";
  print "          <td align='left' colspan='1' ".$dms_config['class_subheader'].">\r";
  print "            <b>" . _DMS_SELECT_LIFECYCLE . "</b>\r";
  print "          </td>\r";
  print "        </tr>\r";
  print "      </table>\r";
    
  $query =  "SELECT lifecycle_id, lifecycle_name, lifecycle_descript FROM ".$dmsdb->prefix('dms_lifecycles')." ";
  $query .= "ORDER BY lifecycle_name";
  $result = $dmsdb->query($query);
  
  print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";
 
  print "        <tr>\r";
  
  print "          <td width='10%' ".$dms_config['class_content'].">\r";
  print "            <u>" . _DMS_SELECTION . "</u>\r";
  print "          </td>\r";
  
  print "          <td ".$dms_config['class_content'].">\r";
  print "            <u>" . _DMS_NAME . "</u>\r";
  print "          </td>\r";
    
  print "          <td ".$dms_config['class_content'].">\r";
  print "            <u>" . _DMS_DESCRIPTION . "</u>\r";
  print "          </td>\r";
  
  print "        </tr>\r";
   
  while($result_data = $dmsdb->getarray($result))
    {
    print "        <tr>\r";
    
	print "          <td>\r";
	print "            <input type='radio' name='rad_lifecycle_id' value='".$result_data['lifecycle_id']."'>\r";
	print "          </td>\r";	
	
	print "          <td>\r";
    print "            ".$result_data['lifecycle_name'];
    print "          </td>\r";
    
    print "          <td>\r";
    print "            ".$result_data['lifecycle_name'];
    print "          </td>\r";
  
    print "        </tr>\r";
	}
  
  print "      </table>\r";
  print "    </td>\r";
  
  print "  </tr>\r";
  print "</table>\r";
  
  print "<input type='hidden' name='hdn_function' value='APPLY'>\r";
  print "<input type='hidden' name='hdn_file_id' value='".$file_id."'>\r";
    
  print "</form>\r";
  
  include_once XOOPS_ROOT_PATH.'/footer.php';

  
?>
