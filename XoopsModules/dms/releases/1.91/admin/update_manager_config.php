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

// index.php
// Administration Page

include_once '../../../mainfile.php';
include_once (XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once (XOOPS_ROOT_PATH."/include/cp_functions.php");
include_once '../inc_current_version.php';
include_once (XOOPS_ROOT_PATH."/modules/dms/inc_pal.php");

// NOTE:  DO NOT USE THE inc_dms_function.php FILE BECAUSE THE $dms_config['version'] VALUE MAY BE UPDATED 
//        MULTIPLE TIMES!  USER THE dms_get_old_version() FUNCTION, INSTEAD.

global $db, $HTTP_POST_VARS;

include_once 'inc_update_manager.php';

$module_obj_id="";
$old_version = dms_get_old_version();

if ($HTTP_POST_VARS["hdn_select_release_folder_id"])
	{
	$new_release_folder=$HTTP_POST_VARS['rad_folder_id'];
	}
else $new_release_folder = 0;

xoops_cp_header();

print "<SCRIPT LANGUAGE=\"Javascript\">\r";

print "  function update_updates_root_obj_id()\r";
print "    {\r";
print "    document.frm_updates_root_obj_id.hdn_updates_root_obj_id.value = document.frm_module_update.txt_updates_root_obj_id.value;\r";
print "    document.frm_updates_root_obj_id.submit();\r";
print "    }\r";

print "</SCRIPT>\r";

print '<b>DMS Configuration</b><BR><BR>';

print 'Update Manager Configuration:<BR><BR>';

// Beginning of Module update section
$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='updates_root_obj_id'";
$updates_root_obj_id = $dmsdb->query($query,'data');

if ($new_release_folder > 0) $updates_root_obj_id = $new_release_folder;

print "<form name=\"frm_module_update\" method=\"post\" action=\"update_manager.php\">\r";
print "&nbsp;&nbsp;&nbsp;Object ID of Folder for New DMS Module Releases:  ";
print "<input type='text' name='txt_updates_root_obj_id' value='".$updates_root_obj_id."' maxlength='8' size='8'>\r";
print "<input type='button' name='btn_slct_template_root_dir' value='Select' onclick='location=\"update_manager_slct_root_dir.php\";'>\r";

print "<BR><BR>\r";
print "<input type='hidden' name='hdn_install_module' value='TRUE'>";
print "&nbsp;&nbsp;&nbsp;<input type='button' name='btn_exit' value='Exit' onclick='update_updates_root_obj_id();'>\r";

print "</form>\r";

// Form for updating the Object ID of the folder for New DMS Module Releases
print "<form name=\"frm_updates_root_obj_id\" method=\"post\" action=\"update_manager.php\">\r";
print "<input type='hidden' name='hdn_updates_root_obj_id' value=''>\r";
print "<input type='hidden' name='hdn_update_updates_root_obj_id' value='TRUE'>\r";
print "</form>\r";

xoops_cp_footer();
?>
