<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                                                                           //
//                             Job Server                                    //
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

// file_options.php

include_once 'job_server_config.php';
include_once 'inc_defines.php';
include_once 'inc_extern_dmsdb_access.php';
include_once 'inc_job_server_functions.php';

/*

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
*/
function job_execute_script($script)
	{
	exec($script);
	}
	
function job_extern_pub($folder_id, $depth, $num_docs)
	{
	global $dms_config;
	
	$file = JS_XOOPS_ROOT_PATH."/modules/dms/published/external/".$folder_id;
	$fp = fopen($file,'w') or die("<BR><BR>Unable to open $file");

	//  Get the name of the top-level folder and write it to the file
	
	$query = "SELECT obj_name FROM ".db_prefix("dms_objects")." WHERE obj_id='".$folder_id."'";
	$obj_name = db_query($query,"obj_name");
	
	$line = "TOP 0 ".$folder_id." ".$obj_name."\n";
	fputs($fp,$line);

	$current_depth = 0;
	job_extern_pub_recurse_folder($folder_id, ($current_depth + 1), $depth, $num_docs, $fp);

	fclose($fp);
	}
	
function job_extern_pub_recurse_folder($folder_id, $current_depth, $max_depth, $num_docs, $file_pointer)
	{
	// Get the documents in the current folder.
	$query  = "SELECT obj_id,obj_name,obj_type FROM ".db_prefix("dms_object_perms")." AS dop ";
	$query .= "INNER JOIN ".db_prefix("dms_objects")." AS do ON do.obj_id = dop.ptr_obj_id ";
	$query .= "WHERE everyone_perms >= '2' AND (obj_type = '0' OR obj_type='40') AND obj_owner = '".$folder_id."' ";
	$query .= "ORDER BY time_stamp_create LIMIT ".$num_docs;
	$result = db_query($query);

	// Store the documents in a file.
	while($result_data = db_getarray($result))
		{
		$obj_type = "DOC ";
		if($result_data['obj_type'] == 40) $obj_type = "URL ";
		
		$line = $obj_type.$current_depth." ".$result_data['obj_id']." ".$result_data['obj_name']."\n";
		fputs($file_pointer,$line);
		}
	
	//  Get the next level of folders to recurse, if necessary
	if($current_depth >= $max_depth) return;
	
	$query  = "SELECT obj_id,obj_name FROM ".db_prefix("dms_object_perms")." AS dop ";
	$query .= "INNER JOIN ".db_prefix("dms_objects")." AS do ON do.obj_id = dop.ptr_obj_id ";
	$query .= "WHERE everyone_perms >= '2' AND obj_type = '1' AND obj_owner = '".$folder_id."' ";
	$query .= "ORDER BY obj_name";
	$result = db_query($query);

	while($result_data = db_getarray($result))
		{
		$line = "FOL ".$current_depth." ".$result_data['obj_id']." ".$result_data['obj_name']."\n";
		fputs($file_pointer,$line);

		job_extern_pub_recurse_folder($result_data['obj_id'],($current_depth + 1), $max_depth, $num_docs, $file_pointer);
		}
	}
	
	
	
	
	
function job_fts()
	{
	global $dms_config;
	// Create the full text search command string
	$command = $dms_config['swish-e_path']."/swish-e -c ".$dms_config['doc_path']."/swish-e.conf";
	exec($command);
	}

function job_obj_deletion()
	{
	}

function job_perms_change($obj_id,$pg_obj_id)
	{
	// Get the permissions categories to change     
	// 0 = OWNER, 1 = EVERYONE, 2 = GROUPS, 3 = USERS
	$query = "SELECT data FROM ".db_prefix("dms_object_misc")." WHERE obj_id='".$pg_obj_id."' AND data_type='".PERMS_GROUP."'";
	$change_perms_data = db_query($query,"data");

	$where_clause = "";
	$or_clause = FALSE;
		
	$mask = 1;
	for($index = 0; $index < 4; $index++)
		{
		if( ($change_perms_data & $mask) == $mask) 
			{
			if($or_clause == TRUE) $where_clause .= " OR ";
			if($mask == 1) $where_clause .= "(user_id > 0 AND user_perms = 4)";
			if($mask == 2) $where_clause .= "( (user_id = 0) && (group_id = 0) )";
			if($mask == 4) $where_clause .= "(group_id > 0)";
			if($mask == 8) $where_clause .= "(user_id > 0 AND user_perms < 4)";
			$or_clause = TRUE;
			}
		$mask *= 2;
		}

	if($or_clause == TRUE) $where_clause = " AND (".$where_clause.")";
	else return(0);   // If there aren't any permissions selected to change, exit this function.

	// Delete the permissions for the object.
	$query  = "DELETE FROM ".db_prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."' ".$where_clause;
	db_query($query);

	// Copy the lifecycle stage permissions to the object.
	$query  = "SELECT * FROM ".db_prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$pg_obj_id."' ".$where_clause;
	$result = db_query($query);
	
	while($result_data = db_getarray($result))
		{
		$query  = "INSERT INTO ".db_prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,group_id,user_perms,group_perms,everyone_perms) VALUES (";
		$query .= "'".$obj_id."',";
		$query .= "'".$result_data['user_id']."',";
		$query .= "'".$result_data['group_id']."',";
		$query .= "'".$result_data['user_perms']."',";
		$query .= "'".$result_data['group_perms']."',";
		$query .= "'".$result_data['everyone_perms']."')";
		db_query($query);
		}
	}

//  *************************
//  Beginning of Main Routine
//  *************************



print "\nDMS Module Job Server\n";
print "Version:  ".$dms_config['version']."\n";

// Get all job information from database
$query = "SELECT * FROM ".db_prefix("dms_job_services");
$jobs = db_query($query);

// Loop through each individual job
while($individual_job = db_getarray($jobs))
	{
	// Extract the flags
	$run_flag = FALSE;
	$time_flag = AT;
	$day_flag = ON;
	//$recurring_flag = FALSE;
	if( ($individual_job['flags'] & 1) == 1) $run_flag = TRUE;
	if( ($individual_job['flags'] & 2) == 2) $time_flag = EVERY;
	if( ($individual_job['flags'] & 4) == 4) $day_flag = EVERY;
	
	// If enabled and the scheduled time has passed, execute function for job.
	if( ($run_flag == TRUE) && (date('U') >= $individual_job['next_run_time']) && ($individual_job['next_run_time'] > 0) )
		{
		switch($individual_job['job_type'])
			{
			case FTS_INDEX:		job_fts();										break;
			case OBJ_DELETION:	job_obj_deletion();									break;
			case PERM_CHANGE:	job_perms_change($individual_job['obj_id_a'],$individual_job['obj_id_b']);		break;
			case EXTERN_PUB:	
				job_extern_pub($individual_job['obj_id_a'],$individual_job['obj_id_b'],$individual_job['obj_id_c']);	break;
			case EXEC_SCRIPT:	job_execute_script($individual_job['text']);						break;
			}
		
		// Re-schedule the job, if necessary

		if($day_flag == EVERY)
			{
			$next_run_time = js_next_run_time($day_flag,$time_flag,
			  $individual_job['sched_day'],$individual_job['sched_hour'],$individual_job['sched_minute']);
			$run_flag = 1;
			}
		else 
			{
			$next_run_time = 0;
			$run_flag = 0;
			}

		// Update the job with either new scheduling or turn off the run_flag
		
		// Determine the flags value
		$flags = 0;
		$flags += ($run_flag * 1);
		$flags += ($time_flag * 2);
		$flags += ($day_flag * 4);
		
		$query  = "UPDATE ".db_prefix('dms_job_services')." SET ";
		$query .= "next_run_time = '".$next_run_time."',";
		$query .= "flags = '".$flags."' ";
		$query .= "WHERE row_id='".$individual_job['row_id']."'";
		db_query($query);
		}
	}

	
print "\n";
?>