<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2003                                     //
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

// MMS Integration
// mms_create.php

include '../../mainfile.php';
include_once 'defines.php';
include_once 'inc_dms_functions.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_properties.php';
include_once 'inc_adn_system.php';
include_once 'inc_adv_system.php';

//$mms_id=$HTTP_GET_VARS['mmsid'];
$mms_id = dms_get_var("mmsid");
$mms_property_num = '3';   // This sets the property number that stores the MMS Number.

//if(strlen($mms_id) < 1) $mms_id = $HTTP_POST_VARS['hdn_mmsid'];


	include XOOPS_ROOT_PATH.'/header.php';
//	$location="mms_create.php"; 
//	$obj_id = -1;    // Fake object id....used to force folder_expand.php and folder_contract.php to return to this page.
	
	// Get active folder
//	$active_folder = dms_active_folder();

	//  Clear the $dms_var_cache;
	$dms_var_cache['mms_create_obj_name'] = "";
	$dms_var_cache['mms_create_templateid'] = "";
	$dms_var_cache['mms_create_function'] = "";
	$dms_var_cache['mms_create_destid'] = "";
	
	$dms_var_cache['mms_create_mmsid']=$HTTP_GET_VARS['mmsid'];
	
	dms_var_cache_save();
		
/*
	if($dms_admin_flag == 0)
			{  
			$active_folder_perms = dms_perms_level($active_folder);
			if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
				{
				print("<SCRIPT LANGUAGE='Javascript'>\r");
				print("location='index.php';");
				print("</SCRIPT>");  
				}
			}  
*/	
	//$file_id=$HTTP_GET_VARS["file_id"];
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_mms_create' action='mms_create_2.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Create or Import Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";

	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";
	dms_display_spaces(3);
	print "      <input type='radio' name='rad_function' value='CREATE' tabindex='".$dms_tab_index++."' CHECKED>  Create Document";
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";
	dms_display_spaces(3);
	print "      <input type='radio' name='rad_function' value='IMPORT' tabindex='".$dms_tab_index++."'>  Import Document";
	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><br></td></tr>\r";
		
	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
	dms_display_spaces(3);
	print "      Document Name:";
	print "      <input type='text' name='txt_obj_name' size='40' maxlength='250' class='".$dms_config['class_content']."' tabindex='".$dms_tab_index++."'>\r";
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  </tr><td colspan='2' align='left'>";
	//print "                                    <input type='button' name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex='".$dms_tab_index++."'>\r";
	print "                                    <input type='submit' name='btn_submit' value='Next' tabindex='".$dms_tab_index++."'>";
	print "  </td></tr>\r";
	print "</table>\r";
	
	print "<input type='hidden' name='hdn_mmsid' value='".$mms_id."'>\r";
	print "</form>\r";
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("  document.frm_mms_create.txt_obj_name.focus();");
	//print("  document.frm_mms_create.rad_function.focus();");
	print("</SCRIPT>");
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	
?>



