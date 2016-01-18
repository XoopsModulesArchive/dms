<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2005                                     //
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

// statistics.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

include XOOPS_ROOT_PATH.'/header.php';

if($dms_admin_flag == 0)
	{  
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	}  

//$file_id=$HTTP_GET_VARS["file_id"];

print "  <table width='100%'>\r";
display_dms_header(2);

print "  <tr><td colspan='2'><BR></td></tr>\r";
print "  <tr><td colspan='2' align='left'><b>Statistics:</b></td></tr>\r";
print "  <tr><td colspan='2'><BR></td></tr>\r";

print "  <tr>\r";
print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td colspan='2'><BR></td></tr>\r";

$query = "SELECT count(*) as num_docs FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_type='".FILE."'";  
$num_docs = $dmsdb->query($query,'num_docs');

$query = "SELECT count(*) as num_folders FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_type='".FOLDER."'";  
$num_folders = $dmsdb->query($query,'num_folders');

$query = "SELECT count(*) as num_inboxes FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_type='".INBOXEMPTY."' OR obj_type='".INBOXFULL."'";  
$num_inboxes = $dmsdb->query($query,'num_inboxes');

print "  <tr class='".$dms_config['class_content']."'>\r";
print "    <td width='25%' align='left'>\r";
print "      Documents:\r";
print "    </td>\r";

print "    <td align='left'>\r";
print "      ".$num_docs."\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr>\r";
print "    <td align='left'>\r";
print "      Folders:";
print "    </td>\r";

print "    <td align='left'>\r";
print "      ".$num_folders."\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr>\r";
print "    <td align='left'>\r";
print "      Inboxes:";
print "    </td>\r";

print "    <td align='left'>\r";
print "      ".$num_inboxes."\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td colspan='2'><BR></td></tr>\r";

$repo_free = disk_free_space($dms_config['doc_path']);
$repo_total = disk_total_space($dms_config['doc_path']);
$repo_used = $repo_total - $repo_free;

print "  <tr>\r";
print "    <td align='left'>\r";
print "      Document Repository:";
print "    </td>\r";

print "    <td align='left'>\r";
print "      ".dms_graph_single_bar($repo_used,$repo_total);
//print "      <BR>";
//print "      ".$repo_used."/".$repo_total."\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td colspan='2'><BR></td></tr>\r";
	
print "  <td colspan='2' align='left'><input type='button' name='btn_exit' value='Exit' onclick='location=\"index.php\";'></td>\r";
print "</table>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

?>



