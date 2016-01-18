<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 11/16/2005                                //
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

// Search Function For Xoops/DMS Integration
// inc_search_x.php

function dms_search_x($queryarray, $andor = " AND ", $limit = 0, $offset = 0, $user_id = 0)
	{
	global $xoopsDB, $xoopsUser; 

	$ret = array();
	
	// Get the $user_id.  If the $user_id remains 0, then do not continue the search.
	if( ($user_id==0) && ($xoopsUser))
		$user_id = $xoopsUser->getVar('uid');

	if($user_id==0) return $ret;

	// Get the list of groups that the user is a member of.
	$query = "SELECT groupid FROM ".$xoopsDB->prefix("groups_users_link")." WHERE uid='".$user_id."'";
	$result = $xoopsDB->query($query);

	$group_list = array();
	
	$index = 0;
	while($result_data = $xoopsDB->fetchArray($result))
		{
		$group_list[$index]=$result_data['groupid'];  
		$index++;
		} 

	// Assemble the $group_query
	$group_query = "";
	$index = 0;
	while($group_list[$index])  
		{
		$group_query .= " OR group_id='".$group_list[$index]."'";
		$index++;
		};

	// Assemble the main search query.
	$query  = "SELECT obj_id, obj_type, obj_name, ";
	$query .= "user_id, group_id, user_perms, group_perms, everyone_perms ";
	$query .= "FROM ".$xoopsDB->prefix("dms_object_perms")." ";
	$query .= "INNER JOIN ".$xoopsDB->prefix("dms_objects")." ON ";
	$query .= $xoopsDB->prefix("dms_object_perms").".ptr_obj_id = obj_id ";
	$query .= "WHERE ";
	$query .= "(everyone_perms !='0'";
	$query .= $group_query;
	$query .= " OR user_id='".$user_id."'";
	$query .= ")";
	$query .= " AND ( obj_status < 2 ) ";
	$query .= " AND (obj_type='0') ";
	
	if(is_array($queryarray))
		{
		$query .= " AND ( (obj_name LIKE '%".$queryarray[0]."%') ";
		
		$count = count($queryarray);              
				
		for($i=1;$i<$count;$i++)
			{
			$query .= $andor;
			$query .= " (obj_name LIKE '%".$queryarray[$i]."%') ";
			}
		$query .= ")";
		}
		
	$query .= "GROUP BY obj_id ";
	$query .= "ORDER BY obj_name ";
	$query .= "LIMIT ".(int)$offset.",".(int)$limit; 
	$result = $xoopsDB->query($query);
   
        $i = 0;
        
	while($row = $xoopsDB->fetchArray($result))
		{
		$ret[$i]['image'] = "images/file_text.png";
		$ret[$i]['link'] = "file_options.php?obj_id=".$row['obj_id'];
                $ret[$i]['title'] = $row['obj_name'];
		$ret[$i]['time'] = ""; //$row['time_stamp_create'];
		$ret[$i]['uid'] = "";
                $i++;
		}

	return $ret;
}

