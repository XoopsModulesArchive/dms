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
include_once 'inc_admin_functions.php';

// NOTE:  DO NOT USE THE inc_dms_function.php FILE BECAUSE THE $dms_config['version'] VALUE MAY BE UPDATED 
//        MULTIPLE TIMES!  USE THE dms_get_old_version() FUNCTION, INSTEAD.

global $db;

include_once 'inc_update_manager.php';

$module_obj_id="";
$old_version = dms_get_old_version();

// Update the database to the current version.
if ($_POST["hdn_update_database"])
	{
	dms_update_tables($old_version,$dms_current_version);
	$old_version = dms_get_old_version();
	
	dms_update_time_stamp();
	}

// Update the updates_root_obj_id in the dms_config table
if ($_POST["hdn_update_updates_root_obj_id"])
	{
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='".$HTTP_POST_VARS["hdn_updates_root_obj_id"]."' ";
	$query .= "WHERE name='updates_root_obj_id'";
	$dmsdb->query($query);
	
	dms_update_time_stamp();
	}

// Get $module_obj_id, if selected by user
if ( ($_POST["hdn_select_module_id"]) && ($_POST['rad_file_id'] > 0) )
	{
	$module_obj_id = $HTTP_POST_VARS['rad_file_id'];
	}

// Get $module_obj_id, if passed from index.php
if ($_GET["module_id"])
	{
	$module_obj_id = $_GET["module_id"];
	}

if ($_POST["hdn_install_module"])
	{
	// Get the path to the docbase
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='doc_path'";
	$docbase_path = $dmsdb->query($query,'data');
	
	// Get the path to the source file
	$query  = "SELECT file_path, file_type FROM ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "WHERE obj_id='".$_POST["txt_new_module_obj_id"]."'";

	$result = $dmsdb->getarray($dmsdb->query($query));
	$source_file_path = $docbase_path."/".$result['file_path'];
	$file_type = $result['file_type'];
	
	switch($file_type)
		{
		case "application/octet-stream":		$file_type="bzip2";		break;
		case "application/x-bzip":			$file_type="bzip2";		break;
		case "application/x-tbz":			$file_type="bzip2";		break;
		case "application/zip":				$file_type="zip";		break;
		default:
			print "Error:  Invalid file type detected, operation aborted.<BR>";
			print "File Type:  ".$file_type."<BR>";
			exit(0);
			break;
		}
	
	// Get xoops modules path
	$xoops_modules_path = XOOPS_ROOT_PATH."/modules";
	
	// Create the command to extract the new version
	if($file_type == "bzip2") $command = "tar -x -j -f ".$source_file_path." -C ".$xoops_modules_path;
	if($file_type == "zip")   $command = "unzip ".$source_file_path." -d ".$xoops_modules_path;
	
	exec($command);
//print $command; exit(0);
	
	dms_update_time_stamp();
	
	// Reload this page to ensure that the versions are correct.
	print "<SCRIPT LANGUAGE=\"Javascript\">\r";
	print "  location=\"update_manager.php\";\r";
	print "</SCRIPT>\r";
	}

xoops_cp_header();

print "<SCRIPT LANGUAGE=\"Javascript\">\r";
print "  function Update_Module()\r";
print "    {\r";
print "    if(document.frm_module_update.txt_new_module_obj_id.value.length < 1)\r";
print "      {\r";
print "      alert(\"Invalid Object ID for Next Release.\");\r";
print "      }\r";
print "    else\r";
print "      {\r";
print "      if(confirm(\"Install New Module?\"))\r";
print "        {\r";
print "        document.frm_module_update.submit();\r";
print "        }\r";
print "      }\r";
print "    }\r";

print "</SCRIPT>\r";



print '<b>DMS Configuration</b><BR><BR>';


print 'Update Manager:<BR><BR>';

// Beginning of Database update section.  
print "<form name=\"frm_database_update\"method=\"post\" action=\"update_manager.php\">\r";

print "&nbsp;&nbsp;&nbsp;Database:<BR>\r";

print "<BR>\r";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Current Version:  ".$dms_current_version."<BR>\r";

if($old_version == $dms_current_version)
	{
	print "&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;No update is required.\r";
	}
else
	{
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Old Version:  ".$old_version."<BR>\r";
	print "<BR>\r";
	
	print "<input type='hidden' name='hdn_update_database' value='TRUE'>";
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' value='Update'>";
	}

print "</form>\r";

//  Beginning of Job Server update section
if($dms_config['write_job_server_config'] == '1')
	{
	print "<BR>&nbsp;&nbsp;&nbsp;Job Server:<BR><BR>\r";
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='btn_update_js_config' value='Update Configuration' onclick=\"location='write_js_config.php';\">";
	}
	
// Beginning of Module update section
$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='updates_root_obj_id'";
$updates_root_obj_id = $dmsdb->query($query,'data');

if ($new_release_folder > 0) $updates_root_obj_id = $new_release_folder;

print "<form name=\"frm_module_update\" method=\"post\" action=\"update_manager.php\">\r";
print "<BR>\r";
print "&nbsp;&nbsp;&nbsp;Module:<BR>\r";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Use this feature with *nix servers only.)<BR>\r";
print "<BR>\r";

print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Object ID of Next Release:  ";
print "<input type='text' name='txt_new_module_obj_id' value='".$module_obj_id."' maxlength='8' size='8'>\r";

if($updates_root_obj_id > 0) print "<input type='button' value='Select' onclick='location=\"update_manager_slct_release.php\";'>\r";

print "<BR><BR>\r";
print "<input type='hidden' name='hdn_install_module' value='TRUE'>";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='btn_update_module' value='Install' onclick=\"Update_Module();\">";
print "&nbsp;&nbsp;<input type='button' value='Configure' onclick='location=\"update_manager_config.php\";'>\r";

print "</form>\r";

xoops_cp_footer();
?>
