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

// folder_restore.php

include '../../mainfile.php';
include 'inc_dms_functions.php';

if ($HTTP_GET_VARS["folder_id"])
	{
	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "SET obj_status='0', time_stamp_delete='0' ";
	$query .= "WHERE obj_id='".$HTTP_GET_VARS["folder_id"]."'";
	$dmsdb->query($query);
  
	dms_auditing($HTTP_GET_VARS["folder_id"],"folder/restore");
	}  

//header("Location:index.php");
dms_header_redirect("index.php");

/*
print("<SCRIPT LANGUAGE='Javascript'>\r");
print("location='index.php';");
print("</SCRIPT>");  
*/
?>
