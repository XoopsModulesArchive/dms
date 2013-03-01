<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 8/22/2006                                //
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

// Main Menu
// job_server_manager.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_job_server_functions.php';

$days = array(0 => "Day",1 => "Monday",2 => "Tuesday",3 => "Wednesday",4 => "Thursday",5 => "Friday",6 => "Saturday",7 => "Sunday");

$function = dms_get_var("function");
$job_id = dms_get_var("row_id");

if ($function=="update")
	{
	$time_flag = AT;  //dms_get_var("slct_time_flag");
	$day_flag = dms_get_var("slct_day_flag");
	
	// Determine the flags value
	$flags = 0;
	//$flags += (dms_get_var("slct_run_flag") * 1);
	$flags += 1;  // Set run flag to "run" for now.  In the future, this can be selected by the admin.
	$flags += ($time_flag * 2);
	$flags += ($day_flag * 4);
	
	$sched_day = dms_get_var("slct_sched_day");
	$sched_hour = dms_get_var("slct_sched_hour");
	$sched_minute = dms_get_var("slct_sched_minute");
	
	$next_run_time = js_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute);

	$query  = "UPDATE ".$dmsdb->prefix("dms_job_services")." SET ";
	$query .= "job_name='".dms_get_var("txt_job_name")."', ";
	$query .= "job_type='".dms_get_var("slct_job_type")."', ";
	$query .= "next_run_time='".$next_run_time."', ";
	$query .= "flags='".$flags."', ";
	$query .= "sched_day='".$sched_day."', ";
	$query .= "sched_hour='".$sched_hour."', ";
	$query .= "sched_minute='".$sched_minute."', ";
	$query .= "obj_id_a='".dms_get_var("txt_obj_id_a")."', ";
	$query .= "obj_id_b='".dms_get_var("txt_obj_id_b")."', ";
	$query .= "obj_id_c='".dms_get_var("txt_obj_id_c")."', ";
	$query .= "text='".dms_get_var("txt_text")."' ";
	$query .= "WHERE row_id='".$job_id."'";
	$dmsdb->query($query);
	}

if ($function=="add")
	{
	$time_flag = AT;  //dms_get_var("slct_time_flag");
	$day_flag = dms_get_var("slct_day_flag");
	
	// Determine the flags value
	$flags = 0;
	//$flags += (dms_get_var("slct_run_flag") * 1);
	$flags += 1;  // Set run flag to "run" for now.  In the future, this can be selected by the admin.
	$flags += ($time_flag * 2);
	$flags += ($day_flag * 4);
	
	$sched_day = dms_get_var("slct_sched_day");
	$sched_hour = dms_get_var("slct_sched_hour");
	$sched_minute = dms_get_var("slct_sched_minute");
	
	$next_run_time = js_next_run_time($day_flag,$time_flag,$sched_day,$sched_hour,$sched_minute);

	$query  = "INSERT INTO ".$dmsdb->prefix('dms_job_services')." ";
	$query .= "(job_name,job_type,next_run_time,flags,sched_day,sched_hour,sched_minute,obj_id_a,obj_id_b,obj_id_c,text) ";  
	$query .= "VALUES (";
	$query .= "'".dms_get_var("txt_job_name")."',";
	$query .= "'".dms_get_var("slct_job_type")."',";
	$query .= "'".$next_run_time."',";
	$query .= "'".$flags."',";
	$query .= "'".$sched_day."',";
	$query .= "'".$sched_hour."',";
	$query .= "'".$sched_minute."',";
	$query .= "'".dms_get_var("txt_obj_id_a")."',";
	$query .= "'".dms_get_var("txt_obj_id_b")."',";
	$query .= "'".dms_get_var("txt_obj_id_c")."',";
	$query .= "'".dms_get_var("txt_text")."')";
	$dmsdb->query($query);
	}

$edit_job_name = "";
$edit_job_type = "";
$edit_sched_day = "";
$edit_sched_hour = "";
$edit_sched_minute = "";
$edit_obj_id_a = "";
$edit_obj_id_b = "";
$edit_obj_id_c = "";
$edit_text = "";

// Extract the flags
$edit_run_flag = FALSE;
$edit_time_flag = AT;
$edit_day_flag = ON;

if ($function=="edit")
	{
	//  Get the job information
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_job_services")." WHERE row_id='".$job_id."'";
	$job_data = $dmsdb->query($query,"ROW");
	
	$edit_row_id = $job_data->row_id;
	
	//$recurring_flag = FALSE;
	if( ($job_data->flags & 1) == 1) $edit_run_flag = TRUE;
	if( ($job_data->flags & 2) == 2) $edit_time_flag = EVERY;
	if( ($job_data->flags & 4) == 4) $edit_day_flag = EVERY;
	
	$edit_job_name = $job_data->job_name;
	$edit_job_type = $job_data->job_type;
	$edit_sched_day = $job_data->sched_day;
	$edit_sched_hour = $job_data->sched_hour;
	$edit_sched_minute = $job_data->sched_minute;
	$edit_obj_id_a = $job_data->obj_id_a;
	$edit_obj_id_b = $job_data->obj_id_b;
	$edit_obj_id_c = $job_data->obj_id_c;
	$edit_text = $job_data->text;
	}
	
if ($function=="delete")
	{
	// Delete the job
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_job_services')." WHERE ";
	$query .= "row_id='".$job_id."'";
	$dmsdb->query($query);
	}

include XOOPS_ROOT_PATH.'/header.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";
print "</SCRIPT>\r";  

print "<form method='post' name='frm_job_server_manager' action='job_server_manager.php'>\r";
print "<table width='100%'>\r";

//  display_dms_header();

print "  <tr>\r";
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>Job Server Manager</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";

print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <input type='button' name='btn_refresh' value='Refresh' onclick='location=\"job_server_manager.php\";'>\r";
print "            &nbsp;&nbsp;&nbsp;\r";
print "            <input type='button' name='btn_exit' value='Exit' onclick='location=\"index.php\";'>\r";

print "          </td>\r";

print "        </tr>\r";

print "      </table>\r";

print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_subheader'].">\r";
print "            Job Services:\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";

print "        <tr>\r";

print "          <td ".$dms_config['class_content'].">\r";
print "            <u>Job Name</u>\r";
print "          </td>\r";

print "          <td ".$dms_config['class_content']." width='20%'>\r";
print "            <u>Job Type</u>\r";
print "          </td>\r";

print "          <td ".$dms_config['class_content'].">\r";
print "            <u>Next Run Time</u>\r";
print "          </td>\r";

/*
print "          <td ".$dms_config['class_content'].">\r";
print "            <u>Schedule</u>\r";
print "          </td>\r";
*/

print "          <td width='20%' ".$dms_config['class_content'].">\r";
print "            <u>Options</u>\r";
print "          </td>\r";

print "        </tr>\r";

$query = "SELECT * FROM ".$dmsdb->prefix('dms_job_services')." ORDER BY job_name";
$result = $dmsdb->query($query);

while($result_data = $dmsdb->getarray($result))
	{
	// Extract the flags
	$run_flag = FALSE;
	$time_flag = AT;
	$day_flag = ON;
	$recurring_flag = FALSE;
	if( ($result_data['flags'] & 1) == 1) $run_flag = TRUE;
	if( ($result_data['flags'] & 2) == 2) $time_flag = EVERY;
	if( ($result_data['flags'] & 4) == 4) $day_flag = EVERY;
	if( ($result_data['flags'] & 8) == 8) $recurring_flag = TRUE;
	
	$job_type = "";
	switch($result_data['job_type'])
		{
		case FTS_INDEX:		$job_type = "Full Text Search Index";		break;
		case OBJ_DELETION:	$job_type = "Purge Deleted Objects";		break;
		case EXPIRE_DOCS:	$job_type = "Document Expiration";		break;
		case PERM_CHANGE:	$job_type = "Change Permissions";		break;
		case EXTERN_PUB:	$job_type = "Publish For External Retrieval";	break;
		case EXEC_SCRIPT:	$job_type = "Execute Script";			break;
		case PURGE_FOLDER:	$job_type = "Purge Folder";			break;
		default:		$job_type = "Undefined";
		}
	
	print "        <tr>\r";
	print "          <td align='left'>\r";
	print "            ".$result_data['job_name'];
	print "          </td>\r";
	
	print "          <td align='left'>\r";
	print "            ".$job_type;
	print "          </td>\r";
	
	print "          <td align='left'>\r";
	
	if($result_data['next_run_time'] > 0) print "            ".strftime("%d-%B-%Y %H:%M",$result_data['next_run_time']);
	else print "            Never";
	
	print "          </td>\r";

/*
	$schedule_str = "";
	
	if($day_flag == EVERY) $schedule_str = "Every ";
	else $schedule_str .= "On ";
	
	$schedule_str .= $days[$result_data['sched_day']]." ";
	
	if($time_flag == EVERY) $schedule_str.= "Every ";
	else $schedule_str .= "At ";
	
	$schedule_str .= sprintf("%02d",$result_data['sched_hour']).":".sprintf("%02d",$result_data['sched_minute']);
	
	print "          <td align='left'>\r";
	print "            ".$schedule_str;
	print "          </td>\r";
*/
		
	print "          <td align='left'>\r";
	print "            <a href='job_server_manager.php?function=edit&row_id=".$result_data['row_id']."'>edit</a>";
	print "            &nbsp;&nbsp;&nbsp;";
	print "            <a href='job_server_manager.php?function=delete&row_id=".$result_data['row_id']."'>delete</a>\r"; 
	print "          </td>\r";
	
	print "        </tr>\r";
	}
  
print "      </table>\r";
print "    </td>\r";

print "  </tr>\r";

print "  <tr>\r";

print "    <td>\r";

print "      <table width='100%' border='1' ".$class_content.">\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%'>\r";
if($function=="edit")
	print "            <b>Edit:</b>\r";
else
	print "            <b>Add:</b>\r";

print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." style='text-align: left;'>\r";
print "            Job Name:&nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_job_name' value='".$edit_job_name."' ".$dms_config['class_content']." size='45' maxlength='45'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%' style='text-align: left;'>\r";
print "            Type:&nbsp;&nbsp;&nbsp;\r";
print "            <select name='slct_job_type'>\r";

for($index = 0; $index < 6; $index++) $selected[$index] = "";
if($function == "edit") $selected[$edit_job_type] = "SELECTED";

print "              <option value='".FTS_INDEX."' ".$selected[FTS_INDEX].">Full Text Search Index</option>\r";
print "              <option value='".OBJ_DELETION."' ".$selected[OBJ_DELETION].">Purge Deleted Objects</option>\r";
print "              <option value='".PURGE_FOLDER."' ".$selected[PURGE_FOLDER].">Purge Folder</option>\r";
print "              <option value='".EXPIRE_DOCS."' ".$selected[EXPIRE_DOCS].">Document Expiration</option>\r";
print "              <option value='".PERM_CHANGE."' ".$selected[PERM_CHANGE].">Change Permissions</option>\r";
print "              <option value='".EXTERN_PUB."' ".$selected[EXTERN_PUB].">Publish For External Retrieval</option>\r";
print "              <option value='".EXEC_SCRIPT."' ".$selected[EXEC_SCRIPT].">Execute Script</option>\r";
print "            </select>\r";
print "          </td>\r";
print "        </tr>\r";


print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%' style='text-align: left;'>\r";
print "            Schedule:&nbsp;&nbsp;&nbsp;\r";

print "            <select name='slct_day_flag'>\r";

for($index = 0; $index < 2; $index++) $selected[$index] = "";
if($function == "edit") $selected[$edit_day_flag] = "SELECTED";

print "              <option value='".EVERY."' ".$selected[EVERY].">Every</option>\r";
print "              <option value='".ON."' ".$selected[ON].">On</option>\r";
print "            </select>\r";

print "            <select name='slct_sched_day'>\r";

$index = 0;
while(isset($days[$index]))
	{
	print "<option value='".$index."'";
	if(  ($function == "edit") && ($edit_sched_day == $index) ) print "SELECTED";
	print ">".$days[$index]."</option>\r";
	$index++;
	}

print "            </select>\r";

print "            &nbsp;&nbsp;&nbsp;At\r";

/*
print "            <select name='slct_time_flag'>\r";
print "              <option value='".AT."'>At</option>\r";
print "              <option value='".EVERY."'>Every</option>\r";
print "            </select>\r";
*/
print "            <select name='slct_sched_hour'>\r";

for($index = 0; $index < 24; $index++)
	{
	print "<option value='".$index."' ";
	if(  ($function == "edit") && ($edit_sched_hour == $index) ) print "SELECTED";
	print ">".$index."</option>\r";
	}

print "            </select>\r";

print "            :\r";

print "            <select name='slct_sched_minute'>\r";

for($index = 0; $index < 60; $index += 15)
	{
	print "<option value='".$index."' ";
	if( ($function == "edit") && ($edit_sched_minute == $index) ) print "SELECTED";
	print ">".$index."</option>\r";
	}

print "            </select>\r";

print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." style='text-align: left;'>\r";
print "            Number A:&nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_obj_id_a' value='".$edit_obj_id_a."' ".$dms_config['class_content']." size='12' maxlength='12'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." style='text-align: left;'>\r";
print "            Number B:&nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_obj_id_b' value='".$edit_obj_id_b."' ".$dms_config['class_content']." size='12' maxlength='12'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." style='text-align: left;'>\r";
print "            Number C:&nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_obj_id_c' value='".$edit_obj_id_c."' ".$dms_config['class_content']." size='12' maxlength='12'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." style='text-align: left;'>\r";
print "            Text:&nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_text' value='".$edit_text."' ".$dms_config['class_content']." size='50' maxlength='250'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td ".$dms_config['class_content']." width='20%' style='text-align: left;'>\r";

if($function == "edit")
	{
	print "            <input type='hidden' name='function' value='update'>\r";
	print "            <input type='hidden' name='row_id' value='".$edit_row_id."'>\r";
	print "            <input type='submit' name='btn_add' value='Update'>\r";
	}
else
	{
	print "            <input type='hidden' name='function' value='add'>\r";
	print "            <input type='submit' name='btn_add' value='Add'>\r";
	}
	

print "          </td>\r";
print "        </tr>\r";



print "      </table>\r";
print "    </td>\r";
print "  </tr>\r";

print "</table>\r";

print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

  
?>
