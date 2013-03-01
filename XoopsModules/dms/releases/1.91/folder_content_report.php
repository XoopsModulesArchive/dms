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

// folder_content_report.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'defines.php';

$doc_counter = 0;
$folder_counter = 0;

$obj_id = dms_get_var("obj_id");

// Permissions required to access this page:
//  EDIT, OWNER
$perms_level = dms_perms_level($obj_id);

if ( ($perms_level != 3) && ($perms_level != 4) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}


	
function count_objects($obj_id)
	{
	global $dmsdb,$doc_counter,$folder_counter;

	$folder_counter++;
	 
	// Get the objects in the specified folder.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_owner='".$obj_id."' AND (obj_type='0' OR obj_type = '1')";
	//$query .= "ORDER BY obj_type";
	
	//  $result = mysql_query($query) or die(mysql_error());
	
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			if($result_data['obj_type'] == FOLDER) 
				count_objects($result_data['obj_id']);
			else
				$doc_counter ++;
			}
		}
	}

count_objects($obj_id);
$folder_counter--;
	
include XOOPS_ROOT_PATH.'/header.php';

// Get file information
$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
$query .= "WHERE obj_id='".$obj_id."'";  
$folder_name = $dmsdb->query($query,'obj_name');

print "<table width='100%'>\r";

display_dms_header();

print "  <tr><td><BR></td></tr>\r";

print "  <tr>\r";
print "    <td align='left' ".$dms_config['class_content'].">\r";
print "      <b>Folder Content Report:</b>";
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td><BR></td></tr>\r";

print "  <tr>\r";
print "    <td align='left' ".$dms_config['class_content'].">\r";
print "      Folder:  ".$folder_name;
print "    </td>\r";
print "  </tr>\r";

print "  <tr>\r";
print "    <td align='left' ".$dms_config['class_content'].">\r";
print "      Documents:  ".$doc_counter;
print "    </td>\r";
print "  </tr>\r";

print "  <tr>\r";
print "    <td colspan='2' align='left' ".$dms_config['class_content'].">\r";
print "      Folders:  ".$folder_counter;
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td><BR></td></tr>\r";

print "  <tr><td ".$dms_config['class_content']."><input type=button name='btn_exit' value='Exit' onclick='location=\"folder_options.php?obj_id=".$obj_id."\";'></td></tr>\r";

print "</table>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';
?>
