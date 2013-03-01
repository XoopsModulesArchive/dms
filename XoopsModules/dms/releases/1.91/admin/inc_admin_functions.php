<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 6/24/2003                                //
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

// DMS Admin Functions
// inc_admin_functions.php

// NOTE:  When releasing a new version with the version number > 0.98, both the version and time_stamp
//        need to be updated!!!!

include_once (XOOPS_ROOT_PATH."/modules/dms/inc_pal.php");

function dms_update_time_stamp()
{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='".time()."' ";
	$query .= "WHERE name='time_stamp'";
	$dmsdb->query($query);
}


?>
