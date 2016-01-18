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

function dms_main_int_options_block()
	{
	global $dmsdb,$dms_user_id;

	global $active_folder_type, $active_folder, $active_folder_perms, $dms_admin_flag, $dms_config;

	$block_as_title = TRUE;
	
	$image_filenew = 'filenew.gif';
	$image_fileimport = 'fileimport.gif';
	$image_batchimport = 'batchimport.gif';
	$image_www = 'www.gif';
	$image_foldernew = 'foldernew.gif';
	$image_search = 'search.gif';
	$image_configure = 'configure.gif';
	
	if($block_as_title == FALSE)
		{
		$image_filenew = 'filenew_l.gif';
		$image_fileimport = 'fileimport_l.gif';
		$image_batchimport = 'batchimport_l.gif';
		$image_www = 'www_l.gif';
		$image_foldernew = 'foldernew_l.gif';
		$image_search = 'search_l.gif';
		$image_configure = 'configure_l.gif';
		}
	
	$block = array();
	$block['title'] = "Options";

	$block['content'] = "";

	$block['content'] .= "  <tr>\r";
	//print "    <td width='60%'><img src='images/help.gif' title='Help'><BR></td>\r";
	
	if( ( ($active_folder_type == FOLDER) 
	&& ( ( ($active_folder!=0) && ( ($active_folder_perms == EDIT) || ($active_folder_perms == OWNER) ) ) ) 
	&& ($active_folder_type != DISKDIR) )
	|| ( ($active_folder == 0) && ($dms_admin_flag == 1) )
	)
		{
		$block['content'] .= "  <td valign='top'>";
	
		if ($dms_config['template_root_obj_id'] != 0)
			$block['content'] .= "    <a href='file_new.php'><img src='images/blocks/".$image_filenew."' title='Create Document'></a>&nbsp;";
	
		$block['content'] .= "    <a href='file_import.php'><img src='images/blocks/".$image_fileimport."' title='Import Document'></a>&nbsp;";

		if ($dms_config['OS'] == "Linux") 
			{
			$block['content'] .= "    <a href='file_batch_import.php' title='Import Multiple Documents'><img src='images/blocks/".$image_batchimport."'></a>&nbsp;";
			}

		$block['content'] .= "    <a href='url_add.php'><img src='images/blocks/".$image_www."' title='Add Web Page'></a>&nbsp;";
		$block['content'] .= "    <a href='folder_new.php'><img src='images/blocks/".$image_foldernew."' title='Create Folder'></a>&nbsp;";
		//$block['content'] .= "</td>\r";
		}
	//else
	//	{
	//	$block['content'] .= "    <td align='left'><BR></td>";
	//	}
/*
	if ($dms_config['full_text_search'] == '1')
		{
		$block['content'] .= "    <a href='#' onmouseover='grabMouseX(event); moveLayerY(\"div_menu_search\", currentY, event); popUpSearchMenu();'><img src='images/blocks/".$image_search."' title='Search'></a>&nbsp;";
		}
	else 
		{
		$block['content'] .= "    <td width='25%' align='right' valign='top'><a href='search_prop.php'><img src='images/blocks/".$image_search."' title ='Search'></a>&nbsp;";
		}
	
	if ($dms_admin_flag == 1) 
		{
		$block['content'] .= "<a href='#' onmouseover='grabMouseX(event); moveLayerY(\"div_menu_admin\", currentY, event); popUpAdminMenu();'><img src='images/blocks/".$image_configure."'></a";
		}
*/	
	$block['content'] .= "      </td></tr>\r";
	
	return $block;
}

?>
