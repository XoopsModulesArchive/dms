<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 5/23/2005                                //
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

// Portal Abstraction Layer For Xoops
// inc_pal_xoops.php

// Database specific function calls
class dms_pal_db
{
	var $num_rows;

	function getarray($result)
	{
		return mysql_fetch_array($result);
	}
	
	function getid()
	{
		return mysql_insert_id();
	}
	
	function getobject($result)
	{
		return mysql_fetch_object($result);
	}
	
	function getnumrows($result = "")
	{
		global $xoopsDB;
		if(strlen($result) > 0) return $xoopsDB->getRowsNum($result);
		else return $this->num_rows;  
	}
	
	function prefix($table)
	{
		global $xoopsDB;
		return $xoopsDB->prefix($table);
	}

	function query($query, $instruct = "")
	{
		$result = mysql_query($query);

/*
		if($result == FALSE)
			{
			print "Query Error:\r";
			print "  Query:  ".$query."\r";
			print "  Instruction:  ".$instruct."\r";
			exit(0);
			}
*/
		if( ($result != FALSE) && 
		  (stristr($query, "SELECT") != FALSE) ) $this->num_rows = mysql_num_rows($result);
		else $this->num_rows = 0;
		
		//$this->$num_rows = mysql_num_rows($result);
		if ( ($this->num_rows == 1) && (strlen($instruct) > 0 ) ) 
			{
			$result = mysql_fetch_object($result);
			if($instruct == "ROW") return $result;
			$result = $result->$instruct;
			}
		
		return $result;
	}


}
$dmsdb = new dms_pal_db();



// Portal specific function calls


class dms_pal_group
{
	var $grp_source;
	
	function grp_details($grp_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		else
			return $this->portal_grp_details($grp_id);
		}
	
	function grp_list($usr_id = 0)
		{
		global $dms_user_id;
		
		if($usr_id == 0) $usr_id = $dms_user_id;
		
		if($this->grp_source == "DMS")
			{
			}
		else
			return $this->portal_grp_list($usr_id);
		}
		
	function grp_list_all()
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			return $this->portal_grp_list_all();
		}
		
	function grp_create($grp_name, $grp_descript)
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			return $this->portal_grp_create($grp_name,$grp_descript);
		}
		
	function grp_delete($grp_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			return $this->portal_grp_delete($grp_id);
		}
		
	function usr_list($grp_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			return $this->portal_usr_list($grp_id);
		}

	function usr_list_all()
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			return $this->portal_usr_list_all();
		}

		
	function usr_add($grp_id, $usr_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			$this->portal_usr_add($grp_id,$usr_id);
		}
		
	function usr_delete($grp_id, $usr_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		
		else
			$this->portal_usr_delete($grp_id,$usr_id);
		}

	function usr_delete_all($grp_id)
		{
		if($this->grp_source == "DMS")
			{
			}
		else
			$this->portal_usr_delete_all($grp_id);
		}
		
	// "Private", portal specific functions
	function portal_grp_details($grp_id)
		{
		global $dmsdb;
		
		$details = array();
		
		$query = "SELECT name,description,group_type FROM ".$dmsdb->prefix("groups")." WHERE groupid='".$grp_id."'";
		$result = $dmsdb->query($query,"ROW");
		
		$details['name'] = $result->name;
		$details['descript'] = $result->description;
		$details['type'] = $result->group_type;

		return $details; 
		}
	
	function portal_grp_list($usr_id = 0)
		{
		global $dmsdb;
		
		$query = "SELECT groupid FROM ".$dmsdb->prefix("groups_users_link")." WHERE uid='".$usr_id."'";
		$result = $dmsdb->query($query);

		$index = 0;
		while($result_data = $dmsdb->getarray($result))
			{
			$group_list[$index]=$result_data['groupid'];  
			$index++;
			} 
		
		$group_list['num_rows'] = $index--;
			
		return $group_list;
		}

	function portal_grp_list_all()
		{
		global $dmsdb;
		$group_list = array();
		
		$query = "SELECT groupid,name FROM ".$dmsdb->prefix("groups");
		$result = $dmsdb->query($query);
		
		while($result_data = $dmsdb->getarray($result))
			{
			$group_list[$result_data['groupid']]=$result_data['name'];
			}
		
		return $group_list;
		}
		
	function portal_grp_create($grp_name,$grp_descript)
		{
		global $dmsdb;
		$query  = "INSERT INTO ".$dmsdb->prefix("groups")." ";
		$query .= "(name,descript) VALUES ('".$grp_name."','".$grp_descript."')";
		$dmsdb->query($query);
		}
		
	function portal_grp_delete($grp_id)
		{
		global $dmsdb;
		$query  = "DELETE FROM ".$dmsdb->prefix("groups")." ";
		$query .= "WHERE groupid='".$grp_id."'";
		$dmsdb->query($query);
		}
		
	function portal_usr_list($grp_id)
		{
		$user_list = array();
		global $dmsdb;
		
		$query  = "SELECT uid FROM ".$dmsdb->prefix("groups_users_link")." ";
		$query .= "WHERE groupid='".$grp_id."'";
		$result = $dmsdb->query($query);
		
		while($result_data = $dmsdb->getarray($result))
			{
			$query  = "SELECT uname FROM ".$dmsdb->prefix("users")." ";
			$query .= "WHERE uid='".$result_data['uid']."'";
			$uname = $dmsdb->query($query,"uname");
			
			$user_list[$result_data['uid']] = $uname;
			}
		
		return $user_list;
		}
	
	function portal_usr_list_all()
		{
		$user_list = array();
		global $dmsdb;
		
		$query = "SELECT uid,uname FROM ".$dmsdb->prefix("users");
		$result = $dmsdb->query($query);
		
		while($result_data = $dmsdb->getarray($result))
			{
			$user_list[$result_data['uid']]=$result_data['uname'];
			}
		
		return $user_list;
		}
		
	function portal_usr_add($grp_id, $usr_id)
		{
		global $dmsdb;
		
		$query  = "INSERT INTO ".$dmsdb->prefix("groups_users_link")." ";
		$query .= "(groupid,uid) VALUES ('".$grp_id."','".$usr_id."')";
		$dmsdb->query($query);
		}

	function portal_usr_delete($grp_id, $usr_id)
		{
		global $dmsdb;
		
		$query  = "DELETE FROM ".$dmsdb->prefix("groups_users_link")." ";
		$query .= "WHERE groupid='".$grp_id."' AND uid='".$usr_id."'";
		$dmsdb->query($query);
		}

	function portal_usr_delete_all($grp_id)
		{
		global $dmsdb;
		
		$query  = "DELETE FROM ".$dmsdb->prefix("groups_users_link")." ";
		$query .= "WHERE groupid='".$grp_id."'";
		$dmsdb->query($query);
		}
		
}
 

$dms_groups = new dms_pal_group;

class dms_pal_user
{
	function get_email_addr($user_id)
		{
		global $dmsdb;
		
		$query  = "SELECT email FROM ".$dmsdb->prefix("users")." ";
		$query .= "WHERE uid='".$user_id."'";
		return $dmsdb->query($query,"email");
		}

	function get_username($user_id)
		{
		global $dmsdb;
		
		$query  = "SELECT uname FROM ".$dmsdb->prefix("users")." ";
		$query .= "WHERE uid='".$user_id."'";
		return $dmsdb->query($query,"uname");
		}
}

$dms_users = new dms_pal_user;

?>
