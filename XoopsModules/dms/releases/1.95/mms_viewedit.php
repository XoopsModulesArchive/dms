<? 
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

// MMS Integration
// mms_viewedit.php


include '../../mainfile.php';

include_once 'inc_dms_functions.php';
include XOOPS_ROOT_PATH.'/header.php';

//$mms_id=$HTTP_GET_VARS['mmsid'];
$mms_id = dms_get_var("mmsid");
$mms_property_num = '3';
 
//$search_query = $HTTP_POST_VARS['txt_search_query'];
$search_query = dms_get_var("txt_search_query");

print "<table width='100%'>\r";
print "  <tr>\r";
  
// Content
print "    <td valign='top'>\r";
print "      <table width='100%'>\r";
display_dms_header(1);
print "      </table>\r";
  
print "      <BR>\r";

print "<table width='100%'>\r";

print "  <tr>\r";
print "    <td width='100%' ".$class_subheader.">\r";
print "      MMS Documents\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td><BR></td></tr>\r";

// Get the search_limit to limit the search to X number of entries
$search_limit = $dms_config['search_limit'];

// Create SQL Query
$ps_query  = "SELECT ".$dmsdb->prefix("dms_object_properties").".obj_id, obj_name, misc_text ";
$ps_query .= "FROM ".$dmsdb->prefix("dms_object_properties")." ";
$ps_query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ";
$ps_query .= "ON ".$dmsdb->prefix("dms_object_properties").".obj_id ";
$ps_query .= "= ".$dmsdb->prefix("dms_objects").".obj_id ";
$ps_query .= " WHERE property_".$mms_property_num."='".$mms_id."'";
$ps_query .= " AND obj_status < 2";
$ps_query .= " ORDER BY obj_name ";
$ps_query .= " LIMIT ".$search_limit; 
//print $ps_query;

$result = $dmsdb->query($ps_query);
$num_rows = $dmsdb->getnumrows();

if ($num_rows > 0)
	{
	print "  <tr>\r";
	print "    <td>\r";
	print "      <table>\r";
	
	while($result_data = mysql_fetch_array($result))
		{

		// Permissions required to view this object:
	//  BROWSE, READONLY, EDIT, OWNER
		$perms_level = dms_perms_level($result_data['obj_id']);
		$perms_level = dms_determine_admin_perms($perms_level);
	
		if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
			{
			print "  <tr>\r";
			print "    <td align='left'>  <!-- ".$result_data['obj_id']." -->\r";

			$misc_text = $result_data['misc_text'];
			if (strlen($misc_text) >0)
				{
				$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
				}
			else $misc_text = "";
			
			if ($dms_anon_flag == 1)   // If the user is anonymous, provide at most browse or read-only access.
				{    
				switch ($perms_level)
					{
					case 1:
						print "&nbsp;&nbsp;&nbsp;".$result_data['obj_name'].$misc_text;
						break;
					case 2:
						print "&nbsp;&nbsp;&nbsp;<a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$result_data['obj_id']."\"))'>".$result_data['obj_name'].$misc_text."</a>\r";
						break;
					default:
					print _DMS_ACCESS_DENIED ;
					}
				}
			else
				{
					print "&nbsp;&nbsp;&nbsp;<a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$result_data['obj_id']."\"))'>".$result_data['obj_name'].$misc_text."</a>\r";
				}
		
			print "    </td>\r";
			print "  </tr>\r";
			}
		}

	if($num_rows >= $search_limit)
		{    
		// Documents listed exceed limits
		print "  <tr><td>\r";
		print "        <BR>" . _DMS_BECAUSE_AT_LEAST . $search_limit. _DMS_DOCS_EXCEED_LIMIT;
		print "        " . _DMS_REFINE_PARAMETERS;
		print "  </td></tr>\r";
		}
	
		print "      </table>\r";
		print "    </td>\r";
		print "  </tr>\r";
	}
else
	{
	print "<tr><td><b>" . _DMS_NO_FILES_FOUND  . "</b><br></td></tr>"; 
	}

    


print "    </td>\r";
print "  </tr>\r";
print "</table>\r";
 
include_once XOOPS_ROOT_PATH.'/footer.php';
?> 
 
 
