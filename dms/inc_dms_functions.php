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

// DMS Functions
// inc_dms_functions.php

include_once XOOPS_ROOT_PATH."/modules/dms/inc_defines.php";
include_once XOOPS_ROOT_PATH."/modules/dms/inc_pal.php";

//$dms_groups->$group_source = $dms_config['group_source'];

//$admin_flag = "";
//$user_id = "";
$dms_config = array();
$dms_var_cache = array();

$dms_admin_flag = "";
$dms_anon_flag = "";
$dms_user_id = "";
$dms_disp_flag = "TRUE";


$class_content = "";
$class_header = "";
$class_subheader = "";
$class_narrow_header = "";
$class_narrow_content = "";
$dms_tab_index = 1;

dms_get_config();
dms_var_cache_load();
dms_document_deletion();

$dms_groups->group_source = $dms_config['group_source'];

$file_type_update_counter = 0;

// Temporary depreciated functions
function display_dms_header($number_of_columns=3)
{
	dms_display_header($number_of_columns);
}

function display_spaces($number_of_spaces=1)
{
	dms_display_spaces($number_of_spaces);
}

function dms_notify($doc_obj_id)
{
	dms_folder_subscriptions($doc_obj_id);
}

function dms_header_redirect($url)
	{
	dms_redirect($url);
	}




// Permanent functions
function dms_active_folder()
{
	global $dmsdb,$dms_user_id;

	// Get active folder
	$query = "SELECT folder_id FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
	$active_folder = $dmsdb->query($query,'folder_id');

	if($dmsdb->getnumrows() < 1) return 0;
	
	// If the user doesn't have access, return them to the top level.
	$perms_level = dms_perms_level($active_folder);
	if($perms_level < BROWSE) return 0;
	
	return $active_folder;
}

function dms_alpha_move($obj_id)
{
	global $dms_config, $dmsdb;
	
	if($dms_config['lifecycle_alpha_move'] == 1)
		{
		//  Get the first character of the name of the document
		$query  = "SELECT obj_name,obj_owner FROM ".$dmsdb->prefix('dms_objects')." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$result = $dmsdb->query($query,"ROW");
		
		$first_char = $result->obj_name;
		$first_char = substr($first_char,0,1);
		$first_char_upper = strtoupper($first_char);
		$first_char_lower = strtolower($first_char);
		
		$obj_location = $result->obj_owner;
		//  Look for the folder name with the same first character.
		$query  = "SELECT obj_id FROM ".$dmsdb->prefix('dms_objects')." ";
		$query .= "WHERE (obj_name = '".$first_char_upper."' OR obj_name = '".$first_char_lower."') AND obj_status = '0'";
		$query .= "AND obj_owner = '".$obj_location."' ";
		$query .= "AND obj_type = '1'";
		$char_dest_folder = $dmsdb->query($query,"obj_id");
		
		//  If the folder is found, move the document into this folder.
		if($dmsdb->getnumrows() > 0)
			{
			$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
			$query .= "SET ";
			$query .= "obj_owner = '".$char_dest_folder."' ";
			$query .= "WHERE obj_id='".$obj_id."'";
			$dmsdb->query($query);
			}
		}
}

function dms_auditing($obj_id, $descript, $obj_name = "")
{
	global $dmsdb, $dms_user_id;
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_audit_log")." ";
	$query .= "(time_stamp,user_id,obj_id,descript,obj_name) VALUES ('";
	$query .= time()."','";
	$query .= $dms_user_id."','"; // $xoopsUser->getVar('uid')."','";
	$query .= $obj_id."','";
	$query .= $descript."','";
	$query .= $obj_name."')";
	
	$dmsdb->query($query);
}

function dms_display_header($number_of_columns=3,$pre_options="",$post_options="")
	{
	global $dms_config;
	
	if(strlen($dms_config['dms_title']) > 0)
		{
		print "  <tr><td colspan='".$number_of_columns."'><table cellpadding='0' cellspacing='0' border='0' width='100%'>\r";
		print "    <tr>\r";
		print "      <td ".$dms_config['class_header']." width='100'>&nbsp;&nbsp;".$pre_options."</td>\r";
		print "      <td ".$dms_config['class_header']."><center><b><font size='2'><div title='Version ".$dms_config['version']."'>".$dms_config['dms_title']."</div></font></b></center></td>\r";
		print "      <td ".$dms_config['class_header']." width='100'>".$post_options."&nbsp;&nbsp;</td>\r";
		print "    </tr>\r";
		print "  </table></td></tr>\r";
		}
	}

function dms_display_document_icon($obj_id,$file_type = 0,$status = 0)
	{
	global $dmsdb,$file_type_update_counter;

	$update_flag = FALSE;
	
	if( ($file_type == "unchecked") && ($file_type_update_counter < 50) )              //  Update 50 documents, at a time.
		{
		$query  = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
		$cv_row_id = $dmsdb->query($query,"current_version_row_id");
	
		$query  = "SELECT file_type FROM ".$dmsdb->prefix("dms_object_versions")." WHERE row_id='".$cv_row_id."'";
		$file_type = $dmsdb->query($query,"file_type");
		
		$update_flag = TRUE;
		$file_type_update_counter++;
		}
		
	switch($file_type)
		{
		case "application/msword":
			$image = "wordprocessing.png";
			break;
		
		case "application/pdf":
			$image = "pdf.png";
			break;
		
		case "application/rtf":
			$image = "wordprocessing.png";
			break;
			
		case "application/vnd.ms-excel":
			$image = "spreadsheet.png";
			break;
		
		case "application/x-zip-compresssed":
			$image = "tar.png";
			break;
		
		case "image/bmp":
			$image = "image2.png";
			break;
		
		case "image/gif":
			$image = "image2.png";
			break;
			
		case "image/jpeg":
			$image = "image2.png";
			break;
		
		case "text/plain":
			$image = "txt2.png";
			break;
			
		default:
			$image = "unknown.png";
			$file_type = "unknown";
		}
	
	if($status == CHECKEDOUT) $image = "file_locked.png";
		
	$image = "images/doc_types/".$image;
	
	print "<img src='".$image."'>";

	if($update_flag == TRUE)
		{
		$query = "UPDATE ".$dmsdb->prefix("dms_objects")." SET file_type='".$file_type."' WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
	}

function dms_display_spaces($number_of_spaces=1)
{
	$index=0;

	while($index < $number_of_spaces)
		{
		print "&nbsp;";
		$index++;
		}
}

function dms_determine_admin_perms($current_perm)
{
	global $dms_admin_flag;
  
	if ($dms_admin_flag == 1)
		{
		$current_perm = OWNER;
		}
	 
	return($current_perm);
}

/*
function dms_doc_query($admin_display,$folder_owner,$group_query)
{
}
*/

function dms_doc_history($obj_id)
{
	global $dms_config, $dms_user_id, $dmsdb;

	// If the object is already in the history table, delete it.
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_user_doc_history")." WHERE ";
	$query .= "user_id='".$dms_user_id."' AND obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	// If there are more than $dms_config['doc_hist_block_rows'] - 1 objects in the history table, delete any past the ninth.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_user_doc_history")." WHERE user_id='".$dms_user_id."' ORDER BY time_stamp DESC";
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if($num_rows > ($dms_config['doc_hist_block_rows'] - 1) )
		{
		$counter = 0;
		while($result_data = $dmsdb->getarray($result))
			{
			if($counter >= ($dms_config['doc_hist_block_rows'] - 1) )
				{
				$query  = "DELETE FROM ".$dmsdb->prefix("dms_user_doc_history")." WHERE ";
				$query .= "user_id='".$dms_user_id."' AND obj_id='".$result_data['obj_id']."'";
				$dmsdb->query($query);
				}
			$counter++;
			}
		}
	
	// Get the name of the object.
	$query  = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$obj_name = $dmsdb->query($query,"obj_name");

	
	// If the object name is longer than 25 characters, truncate it.
	if(strlen($obj_name) > 25)
		{
		$obj_name = substr($obj_name,0,25);
		$obj_name = $obj_name."...";
		}

	// Insert the object information in the history table.
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_user_doc_history")." (user_id,obj_id,time_stamp,obj_name) VALUES ('";
	$query .= $dms_user_id."','";
	$query .= $obj_id."','";
	$query .= time()."','";
	$query .= $obj_name."')";
	$dmsdb->query($query);
}

function dms_doc_history_delete($obj_id)
{
	global $dmsdb;
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_user_doc_history")." WHERE ";
	$query .= "obj_id='".$obj_id."'";
	$dmsdb->query($query);
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


function dms_folder_query($admin_display,$folder_owner,$group_query)
{
	global $dmsdb,$dms_admin_flag,$dms_user_id;
	
	// If the user is an administrator, ignore the permissions entirely.
	if ( ($dms_admin_flag == 1) && ($admin_display=='1') )
		{
		$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE ( (obj_owner='".$folder_owner."') ";
		$query .= " AND (obj_status < 3) )";
//		$query .= " AND (obj_type='1' OR obj_type='2' OR obj_type='3') ) ";
		$query .= "ORDER BY obj_type desc,obj_name ";
		//$query .= "LIMIT ".$dms_config['doc_display_limit'];
		}
	else
		{
		$query  = "SELECT obj_id, ".$dmsdb->prefix("dms_objects").".ptr_obj_id, obj_type, obj_name, ";
		$query .= "obj_status, obj_owner, obj_checked_out_user_id, lifecycle_id, ";
		$query .= "user_id, group_id, user_perms, group_perms, everyone_perms ";
		$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ON ";
		$query .= $dmsdb->prefix("dms_object_perms").".ptr_obj_id = obj_id ";
		$query .= "WHERE (obj_owner='".$folder_owner."') ";
		$query .= " AND (";
		$query .= "    everyone_perms !='0'";
		$query .= $group_query;
		$query .= " OR user_id='".$dms_user_id."'";
		$query .= ")";
		$query .= " AND ( obj_status < 2 ) ";
		//$query .= " AND (obj_type='".FOLDER."' OR obj_type='".INBOXEMPTY."' OR obj_type='".INBOXFULL."' OR obj_type='".DISKDIR."') ";
		$query .= "GROUP BY obj_id ";
		$query .= "ORDER BY obj_type desc,obj_name ";
//		$query .= "LIMIT ".$dms_config['doc_display_limit'];
		//print "<BR>".$query."<BR>";
		//exit(0);
		}

//	print "<BR>".$query."<BR>";
	return($query);
}

function dms_fts_doc_maintenance($obj_id)               // Changes the file names for full text search.
{
	global $dms_config, $dmsdb;

	if($dms_config['full_text_search_cdo'] == 0) return;

	$query = "SELECT current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$current_version_row_id = $dmsdb->query($query,"current_version_row_id");
	
	$query  = "SELECT row_id,file_path FROM ".$dmsdb->prefix("dms_object_versions")." WHERE ";
	$query .= "obj_id='".$obj_id."' AND ";
	$query .= "file_path LIKE '%.dat' AND ";
	$query .= "row_id !='".$current_version_row_id."'";
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			$new_partial_path_and_file =str_replace(".dat",".old",$result_data['file_path']); 
			$old_file = $dms_config['doc_path']."/".$result_data['file_path'];
			$new_file = $dms_config['doc_path']."/".$new_partial_path_and_file;
			
			$query  = "UPDATE ".$dmsdb->prefix("dms_object_versions")." SET ";
			$query .= "file_path='".$new_partial_path_and_file."' WHERE row_id='".$result_data['row_id']."'";
			$dmsdb->query($query);
			
			rename($old_file,$new_file);
			}
		}

}

function dms_document_deletion($purge_limit = 0)
{
	global $dms_config, $HTTP_SERVER_VARS, $dmsdb;

	// If document purging is not enabled, then exit.
	if($dms_config['purge_enable'] == 0) return;
	
	// If the page being displayed is the main page (index.php) exit.
	if(strpos($HTTP_SERVER_VARS['SCRIPT_FILENAME'],"index.php")) return;
	
	// Determine the time_stamp_delete parameter to query before
	$time_stamp_delete = time() - ($dms_config['purge_delay'] * 86400);  // Where 86400 is the number of seconds in a day
	
	// Determine where clause for dms_objects.status query
	$where_clause = "( ";
	switch ($dms_config['purge_level'])
		{
		case TOTAL:
			$where_clause .= " (obj_status = '".PURGED_FD."') OR ";
		case FILES:
			$where_clause .= " (obj_status = '".PURGED_FS."') OR ";
		case FLAGGING:
			$where_clause .= " (obj_status = '".DELETED."') )";
			break;
		default:
			return;
			break;
		}

	if($purge_limit == 0) $purge_limit = $dms_config['purge_limit'];
		
	// Get a list of all deleted documents limited by $dms_config['purge_limit']
	// Only a small number of documents will be purged at any one time to prevent the server from hanging.
	$query  = "SELECT obj_id,obj_type from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE ".$where_clause." ";
	$query .= "AND (time_stamp_delete < ".$time_stamp_delete.") ";
	$query .= "LIMIT ".$purge_limit;
	$result = $dmsdb->query($query);
	
	while($result_data = $dmsdb->getarray($result))
		{
		// At this time, only purge files.
		if($result_data['obj_type'] == FILE) dms_purge_document($result_data['obj_id']);
		}
}

function dms_document_name_sync($obj_id)
{
	global $dms_config, $dmsdb;
	
	if($dms_config['doc_name_sync'] == 0) return(0);

	$query = "SELECT obj_owner FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$obj_id."'";
	$parent_folder_id = $dmsdb->query($query,"obj_owner");
	
	$doc_name_sync_flag = FALSE;
	$query = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." WHERE obj_id='".$parent_folder_id."' AND data_type='".FLAGS."'";
	$flags = $dmsdb->query($query,"data");
	if ( ($flags & 2) == 2 ) $doc_name_sync_flag = TRUE;
	
	if($doc_name_sync_flag == FALSE) return(0);

	//  Get the name of the document
	$query = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id = '".$obj_id."'";
	$obj_name = $dmsdb->query($query,"obj_name");
	
	//  Look for a valid file extension
	$ext_flag = 0;
	$ext_array = array(0 => "doc",1 => "xls", 2=> "jpg", 3 => "txt");
	
	// Check for a valid file extension as listed in $ext_array.  If one is found, $flag will be > 0.
	$index = 0;
	
	while($ext_array[$index])
		{
		$search_ext = ".".$ext_array[$index];
		if (!(stristr($obj_name,$search_ext) == FALSE)) $ext_flag++;
		
		$index++;
		}

	//  If the $obj_name does not have a valid filename extension, add one.
	if($ext_flag == 0)
		{
		$file_props = dms_get_rep_file_props($obj_id);
		
		$file_name = dms_filename_plus_ext($obj_name,$file_props['file_type']);
		}
	else $file_name = $obj_name;
	
	//  Update the file name(s)
	$query  = "UPDATE ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "SET file_name='".$file_name."' WHERE obj_id='".$obj_id."'"; 
	$dmsdb->query($query);
}

function dms_email_subscribers($obj_id,$message)
	{
	global $dms_config, $dmsdb;

	// If the subscriptions system is enabled, get a list of users subscribed to this document and e-mail them.
	if($dms_config['sub_email_enable']=='1')
		{
		// Get a list of users subscribed to this document
		$query  = "SELECT user_id FROM ".$dmsdb->prefix('dms_subscriptions')." ";
		$query .= "WHERE obj_id = '".$obj_id."'";
		$result = $dmsdb->query($query);
		$num_rows = $dmsdb->getnumrows($result);
		if($num_rows > 0)
			{
			while($result_data = $dmsdb->getarray($result))
				{
				// Get e-mail address
				$query  = "SELECT email FROM ".$dmsdb->prefix('users')." ";
				$query .= "WHERE uid='".$result_data['user_id']."'";
				$dest_user_email = $dmsdb->query($query,'email');
				
				// Compose and send email
				dms_send_email($dest_user_email,$dms_config['sub_email_from'],$dms_config['sub_email_subject'],$message);
				}
			}
		}
	}

function dms_folder_create($obj_name,$obj_owner,$obj_type = FOLDER)
	{
	global $dmsdb, $dms_user_id;
	
	$obj_name = dms_strprep($obj_name);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type,obj_name,obj_owner) VALUES (";
	$query .= "'".$obj_type."',";
	$query .= "'".$obj_name."',";
	$query .= "'".$obj_owner."')";
	$dmsdb->query($query);

	// Get the obj_id of the new object
	$obj_id = $dmsdb->getid();

	dms_perms_set_init($obj_id, $obj_owner);
	
	dms_auditing($obj_id,"folder/new");  
	}

function dms_folder_subscriptions($doc_obj_id, $opt_message="", $opt_folder_id=0)
{
	global $dms_config, $dmsdb, $dms_groups, $dms_users;

	// If the notification system is not enabled, exit.
	if($dms_config['notify_enable'] != 1) return;

	// Get the name of the document, parent obj_id, and current_version_row_id
	$query  = "SELECT obj_name,obj_owner,current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$doc_obj_id."'";

	$result = $dmsdb->query($query,"ROW");
	$cvr_id = $result->current_version_row_id;
	$doc_name = $result->obj_name;
	$folder_obj_id = $result->obj_owner;
	
	//  Get the users and groups to notify
	$query  = "SELECT user_id,group_id FROM ".$dmsdb->prefix("dms_notify")." WHERE obj_id='".$folder_obj_id."'";

	$notify_list = $dmsdb->query($query);

	//  If there aren't any users or groups to notify, then exit.
	if($dmsdb->getnumrows() < 1) return;

	// Get the name of the document, parent obj_id, and current_version_row_id
	$query  = "SELECT obj_name,current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$doc_obj_id."'";

	$result = $dmsdb->query($query,"ROW");
	$cvr_id = $result->current_version_row_id;
	$doc_name = $result->obj_name;

	// Get the name of the folder
	if($opt_folder_id > 0) $folder_obj_id = $opt_folder_id;
	
	if($folder_obj_id > 0)
		{
		$query  = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$folder_obj_id."'";
		$folder_name = $dmsdb->query($query,"obj_name");
		}
	else
		{
		$folder_name = "Root";
		}
	
	// Get the comments
	$query  = "SELECT comment FROM ".$dmsdb->prefix("dms_object_version_comments")." WHERE dov_row_id = '".$cvr_id."'";
	$comments = $dmsdb->query($query,"comment");
	if($dmsdb->getnumrows() == 0) $comments = "";
	
	$message  = "The contents of a folder have changed:<BR><BR>";
	$message .= "&nbsp;&nbsp;Folder:&nbsp;&nbsp;".$folder_name."<BR>";
	$message .= "&nbsp;&nbsp;Document:&nbsp;&nbsp;".$doc_name."<BR>";
	$message .= "<BR><BR>";
	if(0 != strlen($opt_message))
		{
		$message .= $opt_message."<BR><BR>";
		}
	$message .= "Comments:<BR><BR>";
	$message .= $comments;

	while($notify_data = $dmsdb->getarray($notify_list))
		{
		//  If a user, e-mail the user with the message.
		if($notify_data['user_id'] > 0)
			{
			$user_email = $dms_users->get_email_addr($notify_data['user_id']);

			if(strlen($user_email) > 0) 
				dms_send_email($user_email,$dms_config['notify_email_from'],$dms_config['notify_email_subject'],$message);
			}
	
		// If a group, get the members of the group and e-mail them.
		if($notify_data['group_id'] > 0)
			{
			$user_list = $dms_groups->usr_list($notify_data['group_id']);
			
			foreach ($user_list as $u_id => $u_name)
				{
				$user_email = $dms_users->get_email_addr($u_id);
				
				if(strlen($user_email) > 0) 
					dms_send_email($user_email,$dms_config['notify_email_from'],$dms_config['notify_email_subject'],$message);
				}  
			
			}
		}
}

function dms_get_config()
{
	global $class_content,$class_header,$class_subheader,$class_narrow_header,$class_narrow_content;
	global $dmsdb, $xoopsUser, $dms_anon_flag, $dms_user_id, $dms_admin_flag, $dms_disp_flag;
	
	global $dms_config;
	
	$dms_anon_flag = 0;
	$timestamps_match = FALSE;
	
	// Configuration caching.  In an effort to reduce the size of SQL queries, the configuration is cached in the
	// session variable $_SESSION['dms_config'].  If the configuration is current, $dms_config[] is loaded from this
	// session variable.  In the future, the session variable may be read directly.
	
	// Check to see if the configuration has already been read.  
	if(isset($_SESSION['dms_config']))
		{
		// Get only the timestamp from the database.
		$query  = "SELECT data from ".$dmsdb->prefix("dms_config")." ";
		$query .= "WHERE name='time_stamp'";
		$result = $dmsdb->query($query,'data');
		
//print "Retreived TS:  ".$result."<BR>";
//print "Stored TS:  ".$_SESSION['dms_config']['time_stamp']."<BR>";
		
		// If the timestamps match, load the config from the config session array.
		if($_SESSION['dms_config']['time_stamp'] == $result)
			{
			$timestamps_match = TRUE;
			
			foreach ($_SESSION['dms_config'] as $key=>$value)
				{
				$dms_config[$key] = $value;
//print "\$_SESSION['dms_config'][\"$key\"]==$value<br>";
				}
			}
		}
	else
		{
		$_SESSION['dms_config'] = array();
		}
	
	// If $timestamps_match = FALSE, Obtain the configuration from the database
	if($timestamps_match == FALSE)
		{
//print "GET ENTIRE CONFIG";
		$query = "SELECT * from ".$dmsdb->prefix("dms_config");
		$result = $dmsdb->query($query);

		while($result_data = $dmsdb->getarray($result))
			{
			$dms_config[$result_data['name']] = $result_data['data'];
			$_SESSION['dms_config'][$result_data['name']] = $result_data['data'];
			}
		}

	// Set the stylesheet classes
	if(strlen($dms_config['class_content']) > 2) $dms_config['class_content'] = " class='".$dms_config['class_content']."'";
	if(strlen($dms_config['class_header']) > 2) $dms_config['class_header'] = " class='".$dms_config['class_header']."'";
	if(strlen($dms_config['class_subheader']) > 2) $dms_config['class_subheader'] = " class='".$dms_config['class_subheader']."'";
	if(strlen($dms_config['class_narrow_header']) > 2) $dms_config['class_narrow_header'] = " class='".$dms_config['class_narrow_header']."'";
	if(strlen($dms_config['class_narrow_content']) > 2) $dms_config['class_narrow_content'] = " class='".$dms_config['class_narrow_content']."'";
	
// Legacy class strings...to be removed later
	$class_content = $dms_config['class_content'];
	$class_header = $dms_config['class_header'];
	$class_subheader = $dms_config['class_subheader'];
	$class_narrow_header = $dms_config['class_narrow_header'];
	$class_narrow_content = $dms_config['class_narrow_content'];

	// Get the user id and determine if the user is an administrator
	if($xoopsUser)
		{
		$dms_user_id = $xoopsUser->getVar('uid');
		// Determine if the user is an administrator
		if($xoopsUser->IsAdmin()) 
			{
			$dms_admin_flag = 1;
			}
		else 
			{
			$dms_admin_flag = 0;
			}
		}
	else
		{
		if($dms_config['anon_user_id'] >= 1) 
			{
			$dms_user_id = $dms_config['anon_user_id'];
			$dms_admin_flag = 0;
			$dms_anon_flag = 1;
			}
		else 
			{
			$dms_disp_flag = "FALSE";
			//print "DMS is not configured to allow anonymous users.  Please login to access this module.\r";
			//include_once XOOPS_ROOT_PATH.'/footer.php';
			//exit (0);
			}
		}
}

$dms_rep_file_props = array("file_name" => "", "file_type" => "", "file_size" => "", "file_path" => "");  

function dms_get_rep_file_props($file_id)
{
	global $dms_rep_file_props, $dms_config, $dmsdb;
	
	// Get file information
	$query  = "SELECT obj_name,ptr_obj_id,obj_type,current_version_row_id from ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$file_id."'";  
	$first_result = $dmsdb->query($query,"ROW");
	
	// If this object is a link, get the real information.
	if ($first_result->obj_type == 4)
		{
		$query  = "SELECT current_version_row_id from ".$dmsdb->prefix('dms_objects')." ";
		$query .= "WHERE obj_id='".$first_result->ptr_obj_id."'";  
		$first_result = $dmsdb->query($query,"ROW");
		}
	
	$query  = "SELECT file_name,file_type,file_size,file_path from ".$dmsdb->prefix('dms_object_versions')." ";
	$query .= "WHERE row_id='".$first_result->current_version_row_id."'";  
	$second_result = $dmsdb->query($query,"ROW");
	
	//$dms_rep_file_props['file_name'] = $first_result->obj_name;
	$dms_rep_file_props['file_name'] = $second_result->file_name;
	$dms_rep_file_props['file_type'] = $second_result->file_type;
	$dms_rep_file_props['file_size'] = $second_result->file_size;
	$dms_rep_file_props['file_path'] = $dms_config['doc_path']."/".$second_result->file_path;

	return($dms_rep_file_props);
}


// Get a variable that has been POSTed or is in the query string (GET).  Return the contents of the variable.
// If the variable does not exist, returns FALSE;
function dms_get_var($var_name)
{
	$value = FALSE;
	if(isset($_GET[$var_name])) $value = $_GET[$var_name];
	if(isset($_POST[$var_name])) $value = $_POST[$var_name];

	return ($value);
}

// Get a checkbox variable that has been POSTed or is in the query string (GET).  Return either 1 or 0.
// If the variable does not exist, returns 0;
function dms_get_var_chk($var_name)
{
	$value = 0;
	if(isset($_GET[$var_name])) if($_GET[$var_name] == "on") $value = 1;
	if(isset($_POST[$var_name])) if($_POST[$var_name] == "on") $value = 1;
	
	return ($value);
}


function dms_graph_single_bar($value,$total,$yellow_limit = 75,$red_limit = 90)
{
	global $dms_config;

	$percent = round(($value / $total) * 100);

	if($percent < 1) $percent = 1;
	
	$color_file = "graph_green.png";
	if ($percent >= $yellow_limit) $color_file = "graph_yellow.png";
	if ($percent >= $red_limit)    $color_file = "graph_red.png";

	print "<img src='images/graph_end.png'>";
	
	for($index = 0; $index < 100; $index++)
		{
		if((int)$index <= (int)$percent) print "<img src='images/".$color_file."'>";
		else print "<img src='images/graph_grey.png'>";
		}
	
	print "<img src='images/graph_end.png'>";
	print " ".$percent."%";
}


function dms_help_system($id,$control=1)
	{
	global $dmsdb, $dms_admin_flag;
	
	// Determine Help Icon
	switch ($control)
		{
		case 1:
			$icon_text = "<font color='blue'>?</font>";
			break;
		case 2:
			$icon_text = "<img src='images/help2.gif'>";
			break;
		case 3:
			$icon_text = "<img src='images/help.gif'>";
			break;
		}
	
	// Get the object id of the help file
	$query  = "SELECT obj_id_ptr FROM ".$dmsdb->prefix("dms_help_system")." ";
	$query .= "WHERE help_id='".$id."'";
	$obj_id_ptr = $dmsdb->query($query,"obj_id_ptr");

	if($dmsdb->getnumrows() > 0)
		{
		if($control <=9)
		  print "<a href='#' title='Help' onclick='javascript:void(window.open(\"file_retrieve.php?function=view&obj_id=".$obj_id_ptr."\",null,\"width=650,scrollbars=yes,resizable=yes\"))'>".$icon_text."</a>\r";
		
		if($control == 10)
		  print "    <input type='button' name='btn_help' value='Help' onclick='javascript:void(window.open(\"file_retrieve.php?function=view&obj_id=".$obj_id_ptr."\",null,\"width=650,scrollbars=yes,resizable=yes\"))'>&nbsp;&nbsp;";
		
		}

	if($dms_admin_flag == 1)
		{
		print "<font color='red'><a href='#' title='Admin' onclick='javascript:void(location=\"config_help_system.php?id=".$id."\")'>A</a></font>\r";
		}
	}

function dms_inbox_id($user_id)
{
	global $dmsdb;
	
	// Get Destination Inbox obj_id (this will be the object_owner of the new object)
	$query  = "SELECT obj_id "; //, obj_type, obj_status, ";
	$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ON ";
	$query .= $dmsdb->prefix("dms_object_perms").".ptr_obj_id = obj_id ";
	$query .= "WHERE (obj_type='".INBOXEMPTY."' OR obj_type='".INBOXFULL."') ";
	$query .= "AND (user_id='".$user_id."') ";
	$query .= "AND (user_perms='".OWNER."')";
	
	//print "<BR>".$query."<BR>";
	//exit(0);
	
	$inbox_obj_id = $dmsdb->query($query,'obj_id');
	if($dmsdb->getnumrows() == 0) $inbox_obj_id = 0; 
	return $inbox_obj_id;
}

/*
function dms_javascript_clock()
{
	// Adds a javascript clock to the page for the purposes of locking out 

}
*/

function dms_message($message)
{
	$_SESSION['dms_message'] = $message;
}

function dms_purge_document($obj_id)
{
	global $dms_config, $dmsdb;
	
	// Always delete linked objects (routed documents)
	$query  = "DELETE from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	// if purge_level is FLAGGING (0) then just set dms_objects.obj_status to PURGED_FS (3) 
	if($dms_config['purge_level'] == FLAGGING)
		{
		$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
		$query .= "SET obj_status='".PURGED_FS."', time_stamp_delete='".time()."' ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}

	// if purge_level is FILES (1) then just set dms_objects.obj_status to PURGED_FD (4) 
	if($dms_config['purge_level'] == FILES)
		{
		$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
		$query .= "SET obj_status='".PURGED_FD."', current_version_row_id='0', time_stamp_delete='".time()."' ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
	
	// if purge_level is TOTAL (2) then delete all related database entries 
	if($dms_config['purge_level'] == TOTAL)
		{
		$query  = "DELETE from ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		
		$query  = "DELETE from ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "WHERE ptr_obj_id='".$obj_id."'";
		$dmsdb->query($query);
		
		$query  = "DELETE from ".$dmsdb->prefix("dms_object_properties")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		
		$query  = "DELETE from ".$dmsdb->prefix("dms_audit_log")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		
		$query  = "DELETE from ".$dmsdb->prefix("dms_routing_data")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}

	// If purge_level is FILES (1) or TOTAL (2) then delete the files in the repository.
	if(($dms_config['purge_level'] == FILES) || ($dms_config['purge_level'] == TOTAL))
		{
		$query  = "SELECT file_path FROM ".$dmsdb->prefix("dms_object_versions")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$result = $dmsdb->query($query);
		
		while($result_data=$dmsdb->getarray($result))
			{
			$file_path = $dms_config['doc_path']."/".$result_data['file_path'];
			unlink($file_path);
			}
		
		$query  = "DELETE from ".$dmsdb->prefix("dms_object_versions")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
}

function dms_perms_apply_group($pg_obj_id,$obj_id)
	{
	global $dmsdb;
	
	// Get the permissions categories to change     
	// 0 = OWNER, 1 = EVERYONE, 2 = GROUPS, 3 = USERS
	$query = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." WHERE obj_id='".$pg_obj_id."' AND data_type='".PERMS_GROUP."'";
	$change_perms_data = $dmsdb->query($query,"data");
	
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
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."' ".$where_clause;
	$dmsdb->query($query);

	// Copy the lifecycle stage permissions to the object.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$pg_obj_id."' ".$where_clause;
	$result = $dmsdb->query($query);
	
	while($result_data = $dmsdb->getarray($result))
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,group_id,user_perms,group_perms,everyone_perms) VALUES (";
		$query .= "'".$obj_id."',";
		$query .= "'".$result_data['user_id']."',";
		$query .= "'".$result_data['group_id']."',";
		$query .= "'".$result_data['user_perms']."',";
		$query .= "'".$result_data['group_perms']."',";
		$query .= "'".$result_data['everyone_perms']."')";
		$dmsdb->query($query);
		}
	}


function dms_perms_cache_clear()
	{
/*
	global $dms_config;
	
	for($index = 0; $index <= $dms_config['pc_cache_size']; $index++)
		{
		$_SESSION['perms_cache_obj_id'][$index] = 0;
		$_SESSION['perms_cache_perms_level'][$index] = 0;
		}
*/
	}

// This function returns the permissions level that a user has to a particular object.
function dms_perms_level($obj_id) 
	{
	global $dms_config;
	global $group_array, $dmsdb, $dms_groups, $dms_user_id, $dms_anon_flag, $dms_admin_flag;
  
	static $pl_group_list = array();
	static $pl_group_list_flag = 0;
	  
  // Obtain a list of groups the user is a member of.  This is retained as a static variable in order prevent multiple look-ups of 
  // the group_list.
	if ($pl_group_list_flag == 0)  
		{
		$pl_group_list = $dms_groups->grp_list();
		$pl_group_list_flag = 1;
		}
  
	//  Obtain the entire list of permissions for the object.
	$query  = "SELECT user_id, group_id, user_perms, group_perms, everyone_perms ";
	$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."'";
  
	$perms = $dmsdb->query($query);
	$max_perm = 0;
  
	while($perms_data = $dmsdb->getarray($perms))
    	{
		if ( ($dms_user_id == $perms_data['user_id']) && ($max_perm < $perms_data['user_perms']) )
		  $max_perm = $perms_data['user_perms'];

//print "u".$max_perm;
 
		$index = 0;
		//while($pl_group_list[$index])
		while($index < $pl_group_list['num_rows'])
			{
			if( ($pl_group_list[$index] == $perms_data['group_id']) && ($max_perm < $perms_data['group_perms']) )
			  $max_perm = $perms_data['group_perms']; 
			$index++;
			}

//print "g".$max_perm;
	 
		if ($perms_data['everyone_perms'] > $max_perm) $max_perm = $perms_data['everyone_perms'];
//print "e".$max_perm;
		}

	// If the user is anonymous, grant them a maximum of readonly perms.
	if( ($dms_anon_flag >= 1) && ($max_perm > READONLY) ) $max_perm = READONLY;
//print "a".$max_perm;

	// If the user is an administrator and $dms_config['admin_display'] == 1, set the perm level to OWNER
	if( ($dms_admin_flag == 1) && ($dms_config['admin_display'] == '1')) $max_perm = OWNER;
//exit(0);
	return $max_perm;
	}

function dms_perms_owner_user_id($obj_id)
{
	global $dmsdb;
	
	//  Obtain user_id for the owner for the object.
	$query  = "SELECT user_id ";
	$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."' and user_perms='4'";
	
	return $dmsdb->query($query,'user_id');
}  

// Set the initial permissions for an object.
function dms_perms_set_init($obj_id, $parent_folder_id)
	{
	global $dms_config, $dmsdb, $dms_user_id;
	
	// If there is a parent folder and the permissions are inherited from the parent folder, use them.
	if( ($dms_config['inherit_perms'] == 1) && ($parent_folder_id > 0) )
		{
		// Copy only the non-owner permissions, if they exist.
		$query = "SELECT * from ".$dmsdb->prefix('dms_object_perms')." WHERE ptr_obj_id='".$parent_folder_id."'";
		$result = $dmsdb->query($query);
		$num_rows = $dmsdb->getnumrows();
		
		if($num_rows > 0)
			{
			while($result_data = $dmsdb->getarray($result))
				{
				if($result_data['user_perms'] != '4')
					{
					$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." ";
					$query .= "(ptr_obj_id,user_id, group_id, user_perms, group_perms, everyone_perms) VALUES ('";
					$query .= $obj_id."','";
					$query .= $result_data['user_id']."','";
					$query .= $result_data['group_id']."','";
					$query .= $result_data['user_perms']."','";
					$query .= $result_data['group_perms']."','";
					$query .= $result_data['everyone_perms']."')";

					$dmsdb->query($query);
					}
				}
			}
		}
		
	// Store the owner permissions in dms_object_perms
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." ";
	$query .= "(ptr_obj_id,user_id, group_id, user_perms, group_perms, everyone_perms) VALUES ('";
	$query .= $obj_id."','";
	$query .= $dms_user_id."','";
	$query .= "0','";
	$query .= "4','";
	$query .= "0','";
	$query .= "0')";

	$dmsdb->query($query);
	}

function dms_redirect($url)
	{
	//header("Location:".$url);
	//header("Refresh: 1;url=".$url);
	
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	print "    location=\"".$url."\";\r";
	print "</SCRIPT>\r";
	}

function dms_select_version_number($select_box_naming = 'slct_version',$major_num = 1, $minor_num = 0, $sub_minor_num = 0)
{
	global $dms_tab_index;
  
	print "<select name='".$select_box_naming."_major' tabindex='".$dms_tab_index++."'>\r";
	
	$index=0;
	while($index < 10)
	{
		print "<option value='".$index."' ";
		if ($index == $major_num) print " selected";
		print ">".$index."</option> \r";
		
		$index++;
		}
	print "</select>\r";  
	
	print "&nbsp;.&nbsp;\r";
	
	print "<select name='".$select_box_naming."_minor' tabindex='".$dms_tab_index++."'>\r";
	$index=0;
	while($index < 10)
	{
		print "<option value='".$index."' ";
		if ($index == $minor_num) print " selected";
		print ">".$index."</option> \r";
		
		$index++;
		}
	print "</select>\r";  
	
	print "&nbsp;.&nbsp;\r";
	
	print "<select name='".$select_box_naming."_sub_minor' tabindex='".$dms_tab_index++."'>\r";
	$index=0;
	while($index < 10)
	{
		print "<option value='".$index."' ";
		if ($index == $sub_minor_num) print " selected";
		print ">".$index."</option> \r";
		
		$index++;
		}
	print "</select>\r";  
}

function dms_send_email($to="", $from="", $subject="", $message_text="", $attachment_obj_id=0)
{
	global $dms_rep_file_props;
	
	// If there is an attachment, get all of the file information.
	if($attachment_obj_id > 0)
		{
		dms_get_rep_file_props($attachment_obj_id);
		$file = fopen($dms_rep_file_props['file_path'],'rb');
		$data = fread($file,filesize($dms_rep_file_props['file_path']));
		fclose($file);
		
		$data = chunk_split(base64_encode($data));
		}
	
	$mime_boundary = "==Multipart_Boundary_x".md5(mt_rand())."x";
	
	$headers  = "To:  ".$to."\n";
	$headers .= "From:  ".$from."\n";
	$headers .= "X-Mailer: PHP mailer\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: multipart/mixed;\r\n boundary=\"".$mime_boundary."\"";
	
	$message  = "This is a multi-part message in MIME format.\n\n";
	$message .= "--".$mime_boundary."\n";
	$message .= "Content-Type: text/html; charset=iso-8859-1\n";
	$message .= "Content-Transfer-Encoding: 7bit\n\n";
	$message .= $message_text."\n\n";

	// Add timestamp
	$message .= strftime("<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>Message Sent:  %d-%B-%Y %I:%M%p",time())."\n\n";
	// End Add timestamp
	
	if($attachment_obj_id > 0)
		{
		$message .= "--".$mime_boundary."\n";
		$message .= "Content-Type: ".$dms_rep_file_props['file_type'].";\n";
		$message .= " name=\"".$dms_rep_file_props['file_name']."\"\n";
		$message .= "Content-Transfer-Encoding: base64\n\n";
		$message .= $data."\n\n";
		}
		
	$message .= "--".$mime_boundary."--\n";
	mail($to, $subject, $message, $headers);
}

function dms_set_inbox_status($obj_id)
{
	global $dmsdb;

	// Check to see if this $obj_id is an inbox
	$query  = "SELECT obj_type FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id = '".$obj_id."'";
	$obj_type = $dmsdb->query($query,'obj_type');

	if( ($obj_type == INBOXFULL) || ($obj_type == INBOXEMPTY) )
		{
		// Get the number of documents in the inbox
		$query  = "SELECT count(*) as num FROM ".$dmsdb->prefix('dms_objects')." ";
		$query .= "WHERE obj_owner='".$obj_id."'";
		$number_of_docs = $dmsdb->query($query,'num');

		$obj_type=INBOXEMPTY;
		if ($number_of_docs > 0) $obj_type = INBOXFULL;

		// Set the status of the inbox
		$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET obj_type='".$obj_type."' ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
}

function dms_set_obj_status($obj_id,$status)
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "SET obj_status='".$status."'";
	if($status >= DELETED) $query .= ", time_stamp_delete='".time()."' ";
	$query .= " WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);
	}

function strleft($str, $num_chars)
{
	return substr ($str, 0, $num_chars);
}

function strright($str, $num_chars)
{
	$str_length = strlen($str);
	return substr ($str, ($str_length - $num_chars),$str_length);
}

function dms_strprep($str)
{
	$str = str_replace("'","",$str);    // Replace ' with <nothing>
	$str = str_replace("\""," ",$str);   // Replace " with <space>
	$str = str_replace("("," ",$str);    // Replace ( with <space>
	$str = str_replace(")"," ",$str);    // Replace ) with <space>
	$str = str_replace("<"," ",$str);    // Replace < with <space>
	$str = str_replace(">"," ",$str);    // Replace > with <space>
	return $str;
}

function dms_str_get_bytes($number)
{
	$number = trim($number);

	$multiplier = strtoupper(substr($number,(strlen($number)-1),1));

	switch($multiplier)
		{
		case 'G':	$number *= 1024;
		case 'M':	$number *= 1024;
		case 'K':	$number *= 1024;
		}

	return $number;
}

function dms_str_restore($str)
{
	$str = str_replace("`","'",$str);    // Replace ` with '
}

function dms_update_config_time_stamp()
{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='".time()."' ";
	$query .= "WHERE name='time_stamp'";
	
	$dmsdb->query($query);
}

function dms_update_misc_text($obj_id,$lifecycle_stage_override = 0)
{
	global $dms_config,$dmsdb;
	
	$misc_text = "";
	
	$query  = "SELECT template_obj_id, lifecycle_id, lifecycle_stage FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$result = $dmsdb->query($query,"ROW");

	$template_obj_id = $result->template_obj_id;
	$lifecycle_id = $result->lifecycle_id;
	$lifecycle_stage = $result->lifecycle_stage;
	
	if($lifecycle_stage_override > 0) $lifecycle_stage = $lifecycle_stage_override;
	
	// If configured and applicable, add the template name.
	if( ($dms_config['misc_text_disp_template'] == 1) && ($template_obj_id > 0) )
		{
		$query = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$template_obj_id."'";
		$misc_text = $dmsdb->query($query,'obj_name');
		}
		
	// If configured and applicable, add the lifecycle stage name.
	if( ($dms_config['misc_text_disp_lc_stage'] == 1) && ($lifecycle_id > 0) )
		{
		if(strlen($misc_text) > 0) $misc_text = $misc_text.", ";
		
		$query  = "SELECT lifecycle_stage_name FROM ".$dmsdb->prefix("dms_lifecycle_stages")." WHERE ";
		$query .= "lifecycle_id = '".$lifecycle_id."' AND lifecycle_stage = '".$lifecycle_stage."'";
		$misc_text = $misc_text.$dmsdb->query($query,'lifecycle_stage_name');
		}

	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." SET misc_text='".$misc_text."' WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);
}

// Get a variable from the dms session cache.  If it does not exist, create it and set it to 0.
function dms_var_cache_get($variable)
{
	if(!isset($dms_var_cache[$variable]))  
		{
		$dms_var_cache[$variable] = 0;
		}

	return $dms_var_cache[$variable];
}

function dms_var_cache_load()
{
	global $dms_var_cache;
	
	// Check to see if the variable cache exists...if not, create it.  
	if(!isset($_SESSION['dms_var_cache']))
		{
		$_SESSION['dms_var_cache'] = array();
		}

	foreach ($_SESSION['dms_var_cache'] as $key=>$value)
		{
		$dms_var_cache[$key] = $value;
		//print "\$dms_var_cache[\"$key\"]==$value<br>";
		}
}

function dms_var_cache_save()
{
	global $dms_var_cache;
	
	// Check to see if the variable cache exists...if not, create it.  
	if(!isset($_SESSION['dms_var_cache']))
		{
		$_SESSION['dms_var_cache'] = array();
		}

	foreach ($dms_var_cache as $key=>$value)
		{
		$_SESSION['dms_var_cache'][$key] = $value;
		}
}

function dms_var_cache_set($variable, $value=0)
{
	$dms_var_cache[$variable] = $value;
}

?>
