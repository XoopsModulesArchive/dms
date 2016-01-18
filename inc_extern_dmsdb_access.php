<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                                                                           //
//                    External DMS DB Access System                          //
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

// inc_extern_dmsdb_access.php



$dms_config = array();

function db_connect()
	{
	$conn = mysql_connect(JS_XOOPS_DB_HOST,JS_XOOPS_DB_USER,JS_XOOPS_DB_PASS);
	mysql_select_db(JS_XOOPS_DB_NAME,$conn);
	}

function db_getarray($result)
	{
	return mysql_fetch_array($result);
	}

function db_prefix($table)
	{
	return (JS_XOOPS_DB_PREFIX."_".$table);
	}
	
function db_query($query, $instruct = "")
	{
	$result = mysql_query($query);
	
	if(stristr($query, "SELECT") != FALSE) $num_rows = mysql_num_rows($result);
	else $num_rows = 0;
	
	if ( ($num_rows == 1) && (strlen($instruct) > 0 ) ) 
		{
		$result = mysql_fetch_object($result);
		if($instruct == "ROW") return $result;
		$result = $result->$instruct;
		}
	
	return $result;
	}

function get_config()
	{
	global $dms_config;
	
	$query = "SELECT * from ".db_prefix("dms_config");
	$result = db_query($query);

	while($result_data = db_getarray($result))
		{
		$dms_config[$result_data['name']] = $result_data['data'];
		}
	}


// Establish a connection to the database.
db_connect();

// Get the DMS module configuration
get_config();

?>