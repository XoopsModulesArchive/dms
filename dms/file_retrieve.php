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

// file_retrieve.php

// replaces file_export.php, file_open.php, file_view.php, version_view.php


include '../../mainfile.php';
include_once 'inc_dms_functions.php';

// Permissions required to access this page:
//  READONLY, EDIT, OWNER

$function = "";
$obj_id = "";
$ver_id = "";

$cd = "";

$function = dms_get_var("function");
$obj_id = dms_get_var("obj_id");
$ver_id = dms_get_var("ver_id");

$perms_level = dms_perms_level($obj_id);

if ( ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
	{
	dms_auditing($obj_id,"document/open--FAILED");
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}

if($function == "") $function="VIEW";              // If a function is not specified, default to "VIEW."
$function = strtoupper($function);

// If the object type is a WEB_PAGE, redirect to the web page.
$query = "SELECT obj_type FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
$obj_type = $dmsdb->query($query,"obj_type");

if($obj_type==WEBPAGE)
	{
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id='".$obj_id."' AND data_type='".URL."'";
	$url = $dmsdb->query($query,"data");
	
	// Add to the document history.
	//dms_doc_history($obj_id);
	
	// Audit
	dms_auditing($obj_id,"url/open");
	
	$url = "Location:".$url;
	header($url);
	exit(0);
	}


// Get the file properties so the browser can properly handle the file.
switch($function)
	{
	case "EXPORT":
		dms_auditing($obj_id,"document/export");

		dms_get_rep_file_props($obj_id);
		$cd = "attachment";
		break;
	case "OPEN":
		dms_auditing($obj_id,"document/open");
	
		dms_get_rep_file_props($obj_id);
		//$cd = "inline";
		$cd = "attachment";
		break;
	case "VIEW":
		dms_auditing($obj_id,"document/view");

		dms_get_rep_file_props($obj_id);
		$cd = "inline";
		//$cd = "attachment";
		break;
	case "VV":
		dms_auditing($obj_id,"document/view version/version_row_id=".$ver_id);
 
		$query  = "SELECT obj_id,file_name,file_type,file_size,file_path from ".$dmsdb->prefix('dms_object_versions')." ";
		$query .= "WHERE row_id='".$ver_id."'";  
		$result = $dmsdb->query($query,'ROW');
		
		$obj_id = $result->obj_id;
		$dms_rep_file_props['file_name'] = $result->file_name;
		$dms_rep_file_props['file_size'] = $result->file_size;
		$dms_rep_file_props['file_type'] = $result->file_type;
		$dms_rep_file_props['file_path'] = $dms_config['doc_path']."/".$result->file_path;
		
		$cd = "inline";
		break;
	}

// Add to the document history
dms_doc_history($obj_id);

	
	
// The following is for compatibility with documents migrated by the DMS migration program.
// If a document $dms_rep_file_props['file_name'] does not have an extension, one will have to be added based upon
// the $dms_rep_file_props['file_type']
if(!strrchr($dms_rep_file_props['file_name'],".")) 
  $dms_rep_file_props['file_name'] = dms_filename_plus_ext($dms_rep_file_props['file_name'],$dms_rep_file_props['file_type']);

/*
// Debugging
print "N:  ".$dms_rep_file_props['file_name']."<BR>\r";
print "S:  ".$dms_rep_file_props['file_size']."<BR>\r";
print "T:  ".$dms_rep_file_props['file_type']."<BR>\r";
print "P:  ".$dms_rep_file_props['file_path']."<BR>\r";
*/

// send headers to browser to initiate file download
header('Content-Length: '.$dms_rep_file_props['file_size']);
header('Cache-control: private');
header('Content-Type: ' . $dms_rep_file_props['file_type']);
header('Content-Disposition: '.$cd.'; filename="'.$dms_rep_file_props['file_name'].'"');
header('Pragma: public');   // Apache/IE/SSL download fix.
header('Content-Transfer-Encoding:  binary');

// Read the file
readfile($dms_rep_file_props['file_path']);

?>
