<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 7/14/2004                                //
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
// lifecycle_manager.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

$user_id = dms_get_var("user_id");
 
$query_limit = 25;

$query_start = dms_get_var("query_start");
if($query_start == FALSE) $query_start = 0;

if($query_start > 0) $query_limit_clause = " LIMIT ".$query_start.",".$query_limit;
else $query_limit_clause = " LIMIT ".$query_limit;

include XOOPS_ROOT_PATH.'/header.php';

    
print "<form method='post' name='frm_audit_log_user' action='audit_log_user.php'>\r";
print "<table width='100%'>\r";
  
print "  <tr>\r";
  
// Content
print "    <td valign='top'>\r";
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$dms_config['class_header'].">\r";
print "            <center><b><font size='2'>" . _DMS_AUDITING . "</font></b></center>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";
  
print "      <BR>\r";

print "      <table>\r";
print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"index.php\";'>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";

print "      <BR>\r";
  
print "      <table>\r";
print "        <tr><td align='left'>\r";  
print "          <b>" . _DMS_USER_NAME . "</b>  ".$xoopsUser->getUnameFromId($user_id)."\r";
print "        </td></tr>\r";
  
print "        <tr><td><BR></td></tr>\r";
print "      </table>\r";  
    
print "      <table>\r";
print "        <tr>\r";
print "          <td colspan='1' ".$class_subheader.">\r";
print "            <b>" . _DMS_AUDIT_LOG . "</b>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";
    
$query  = "SELECT * FROM ".$dmsdb->prefix('dms_audit_log')." ";
$query .= "WHERE user_id='".$user_id."' ORDER BY time_stamp desc";
$query .= $query_limit_clause;
$result = $dmsdb->query($query);
  
print "      <table width='100%' border='1' ".$dms_config['class_content'].">\r";
 
print "        <tr>\r";
print "          <td align='left' ".$dms_config['class_content'].">\r";
print "            <b>" . _DMS_DATE_AND_TIME . "</b>\r";
print "          </td>\r";

print "          <td align='left' width='10%' ".$dms_config['class_content'].">\r";
print "            <b>" . _DMS_OBJECT_ID . "</b>\r";
print "          </td>\r";
    
print "          <td align='left' ".$class_content.">\r";
print "            <b>" . _DMS_DESCRIPTION . "</b>\r";
print "          </td>\r";
  
print "        </tr>\r";

$result_counter = 0;   
while($result_data = $dmsdb->getarray($result))
	{
	print "        <tr>\r";
	print "          <td align='left' ".$dms_config['class_content'].">\r";
	print "            <a href=\"audit_log_detail.php?row_id=".$result_data['row_id']."\">".strftime("%d-%B-%Y %I:%M%p",$result_data['time_stamp'])."</a>\r";
	print "          </td>\r";

	print "          <td align='left' ".$dms_config['class_content'].">\r";
	print "            <a href='audit_log_obj.php?obj_id=".$result_data['obj_id']."'>".$result_data['obj_id']."</a>\r";
	print "          </td>\r";
    
	print "          <td align='left' ".$dms_config['class_content'].">\r";
	print "            ".$result_data['descript']."\r";
	print "          </td>\r";
  
	print "        </tr>\r";
	
	$result_counter++;
	}

print "        <tr>\r";
print "          <td colspan='3'><BR></td>\r";
print "        </tr>\r";
	
print "        <tr>\r";
print "          <td colspan='3' align='right'>\r";

if($query_start > 0)
	{
	print "      <a href=\"audit_log_user.php?user_id=".$user_id."\">&lt&lt</a>\r";
	print "      &nbsp;&nbsp;\r";
	
	if( ($query_start-$query_limit) > 0 )
		{
		print "      <a href=\"audit_log_user.php?user_id=".$user_id."&query_start=".($query_start - $query_limit)."\">&lt</a>\r";
		print "      &nbsp;&nbsp;\r";
		}
	}

if($result_counter == $query_limit)
	{
	print "      <a href=\"audit_log_user.php?user_id=".$user_id."&query_start=".($query_start + $query_limit)."\">&gt</a>\r";
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
