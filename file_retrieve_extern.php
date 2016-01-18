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

// file_retrieve_extern.php

// Used for retrieving everyone/read-only or greater documents from an external source.

include_once 'job_server_config.php';
include_once 'inc_defines.php';
include_once 'inc_extern_dmsdb_access.php';

function dms_auditing($obj_id, $descript)
{
	$query  = "INSERT INTO ".db_prefix("dms_audit_log")." ";
	$query .= "(time_stamp,user_id,obj_id,descript) VALUES ('";
	$query .= time()."','";
	$query .= "0','"; 
	$query .= $obj_id."','";
	$query .= $descript."')";
	
	db_query($query);
}


function dms_filename_plus_ext($filename, $filetype)
{
	switch ($filetype)
		{
		case "application/msword":		$ext = "doc";		break;
		case "msw8":				$ext = "doc";		break;
		case "application/vnd.ms-excel":	$ext = "xls";		break;
		case "application/xls":			$ext = "xls";		break;
		case "image/gif":			$ext = "gif";		break;
		case "jpeg":				$ext = "jpg";		break;
		case "text/plain":			$ext = "txt";		break;
		default:				$ext = "";		break;
		}

	$filename = $filename.".".$ext;

	return $filename;  
}


$dms_rep_file_props = array("file_name" => "", "file_type" => "", "file_size" => "", "file_path" => "");  

function dms_get_rep_file_props($file_id)
{
	global $dms_rep_file_props, $dms_config;
	
	// Get file information
	$query  = "SELECT obj_name,ptr_obj_id,obj_type,current_version_row_id from ".db_prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$file_id."'";  
	$first_result = db_query($query,"ROW");
	
	// If this object is a link, get the real information.
	if ($first_result->obj_type == 4)
		{
		$query  = "SELECT current_version_row_id from ".db_prefix('dms_objects')." ";
		$query .= "WHERE obj_id='".$first_result->ptr_obj_id."'";  
		$first_result = db_query($query,"ROW");
		}
	
	$query  = "SELECT file_name,file_type,file_size,file_path from ".db_prefix('dms_object_versions')." ";
	$query .= "WHERE row_id='".$first_result->current_version_row_id."'";  
	$second_result = db_query($query,"ROW");
	
	//$dms_rep_file_props['file_name'] = $first_result->obj_name;
	$dms_rep_file_props['file_name'] = $second_result->file_name;
	$dms_rep_file_props['file_type'] = $second_result->file_type;
	$dms_rep_file_props['file_size'] = $second_result->file_size;
	$dms_rep_file_props['file_path'] = $dms_config['doc_path']."/".$second_result->file_path;

	return($dms_rep_file_props);
}

// Check to see if this feature is enabled.  If it is not, terminate without an error message.
if($dms_config['extern_doc_access'] == 0) exit(0);

// Get the object id of the document.
$obj_id = $_GET["obj_id"];


//  Ensure that the document has, at least, everyone read only permissions.
$query =  "SELECT everyone_perms FROM ".db_prefix('dms_object_perms')." ";
$query .= "WHERE ptr_obj_id='".$obj_id."' AND user_id='0' AND group_id='0'";
$everyone_perms = db_query($query,"everyone_perms");

if(  !( ($everyone_perms == READONLY) || ($everyone_perms == EDIT) ) )
	{
	//  If everyone permissions are not as required, terminate this page without an error message.
	exit(0);
	}

// If the object type is a WEBPAGE, redirect to the web page.
$query = "SELECT obj_type FROM ".db_prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
$obj_type = db_query($query,"obj_type");

if($obj_type==WEBPAGE)
	{
	$query  = "SELECT data FROM ".db_prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id='".$obj_id."' AND data_type='".URL."'";
	$url = db_query($query,"data");
	
	// Audit
	dms_auditing($obj_id,"url/e-view");
	
	$url = "Location:".$url;
	header($url);
	exit(0);
	}


dms_auditing($obj_id,"document/e-view");

dms_get_rep_file_props($obj_id);
$cd = "inline";

	
// The following is for compatibility with documents migrated by the DMS migration program.
// If a document $dms_rep_file_props['file_name'] does not have an extension, one will have to be added based upon
// the $dms_rep_file_props['file_type']
if(!strrchr($dms_rep_file_props['file_name'],".")) 
  $dms_rep_file_props['file_name'] = dms_filename_plus_ext($dms_rep_file_props['file_name'],$dms_rep_file_props['file_type']);


// Debugging
/*
print "N:  ".$dms_rep_file_props['file_name']."<BR>\r";
print "S:  ".$dms_rep_file_props['file_size']."<BR>\r";
print "T:  ".$dms_rep_file_props['file_type']."<BR>\r";
print "P:  ".$dms_rep_file_props['file_path']."<BR>\r";
exit(0);
*/

// send headers to browser to initiate file download
header('Content-Length: '.$dms_rep_file_props['file_size']);
header('Cache-control: private');
header('Content-Type: ' . $dms_rep_file_props['file_type']);
header('Content-Disposition: '.$cd.'; filename="'.$dms_rep_file_props['file_name'].'"');
header('Pragma: public');   // Apache/IE/SSL download fix.

// Read the file
readfile($dms_rep_file_props['file_path']);
?>
