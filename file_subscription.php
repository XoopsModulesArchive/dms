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

// file_subscription.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

//if ($HTTP_GET_VARS["ret_url"]) $location=$HTTP_GET_VARS["ret_url"];
//else $location="index.php";

$location = dms_get_var("ret_url");
if($location == FALSE) $location = "index.php";

//if ($HTTP_GET_VARS["obj_id"]) $location .= "?obj_id=".$HTTP_GET_VARS["obj_id"];
//else $location="index.php";

$obj_id = 0;
if(dms_get_var("obj_id") != FALSE) 
	{
	$obj_id = dms_get_var("obj_id");
	$location .= "?obj_id=".$obj_id;
	}
else $location = "index.php";

if ($obj_id != 0)
	{
	if (dms_get_var("funct") == "subscribe")
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_subscriptions")." ";
		$query .= "(obj_id,user_id) VALUES ('".$obj_id."','".$dms_user_id."');";
		$dmsdb->query($query);
		
		dms_message("You are subscribed to this document.");
		}
	else
		{
		$query  = "DELETE FROM ".$dmsdb->prefix("dms_subscriptions")." ";
		$query .= "WHERE obj_id='".$obj_id."' and user_id='".$dms_user_id."'";  
		$dmsdb->query($query);
		
		dms_message("Your subscription to this document has been cancelled.");
		}
	}

//print $query;

//header("Location:".$location);

dms_header_redirect($location);

/*
print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  location='".$location."';";
print "</SCRIPT>";  
*/
?>
