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

// dms_block_doc_history.php

include_once XOOPS_ROOT_PATH."/modules/dms/inc_dms_functions.php";

function dms_show_history()
{
	global $dmsdb,$dms_user_id;

	$block = array();
	$block['title']="Document History";

	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_user_doc_history")." WHERE user_id='".$dms_user_id."' ORDER BY time_stamp DESC";
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	$block['content'] = "";
	if($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			$block['content'] .= "&nbsp;<a href=\"".XOOPS_URL."/modules/dms/file_options.php?obj_id=".$result_data['obj_id']."\">";
			$block['content'] .= $result_data['obj_name']."</a><BR>";
			}
		}
	else $block['content'] = "Empty";

	return $block;
}

?>
