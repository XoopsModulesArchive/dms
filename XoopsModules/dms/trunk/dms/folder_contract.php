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

// file_folder_contract.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

//if ($HTTP_GET_VARS["ret_location"]) $location = $HTTP_GET_VARS["ret_location"];
//else $location="index.php";

if(dms_get_var("ret_location") != FALSE) $location = dms_get_var("ret_location");

if(dms_get_var("obj_id") != FALSE) $location .= "?obj_id=".dms_get_var("obj_id");
else $location = "index.php";

//if ($HTTP_GET_VARS["obj_id"]) $location .= "?obj_id=".$HTTP_GET_VARS["obj_id"];
//else $location="index.php";

// Reset the location if the obj_id is flagged as being bad.
if (dms_get_var("obj_id") == "-1") $location = dms_get_var("ret_location");

if (dms_get_var("active") == "FALSE") $skip_change_active_folder = "FALSE";
else $change_active_folder = "TRUE";

$folder_id = dms_get_var("folder_id");
if ($folder_id != FALSE)
{
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_exp_folders");
	$query .= " WHERE user_id='".$dms_user_id."' and folder_id='".$folder_id."'";
	$dmsdb->query($query);

	dms_set_inbox_status($folder_id);

	// Make sure that this folder cannot be marked as active
	if ($change_active_folder == "TRUE")
		{
		$query  = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." ";
		$query .= "WHERE user_id='".$dms_user_id."' and folder_id='".$folder_id."'";
		$dmsdb->query($query);
		}
} 

//header("Location:".$location);

dms_header_redirect($location);
?>
