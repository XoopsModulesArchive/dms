<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 5/13/2003                                //
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

// link_options.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_properties.php';

include XOOPS_ROOT_PATH.'/header.php';
  
$option_button_width=" style='width: 6em;' ";


//$link_id = $HTTP_GET_VARS['obj_id'];
$link_id = dms_get_var("obj_id");

// Get actual object ID by using link id.
$query  = "SELECT ptr_obj_id,obj_type from ".$dmsdb->prefix('dms_objects')." ";
$query .= "WHERE obj_id='".$link_id."'";  
//print $query;
$result = $dmsdb->query($query,'ROW');
$obj_id = $result->ptr_obj_id;  
  
// Get object information
$query  = "SELECT template_obj_id,obj_name,obj_status,obj_checked_out_user_id,time_stamp_create,current_version_row_id,lifecycle_id ";
$query .= "FROM ".$dmsdb->prefix("dms_objects")." ";
$query .= "WHERE obj_id='".$obj_id."'";  
//print $query;
$object = $dmsdb->query($query,'ROW');

// Get current version information
$query  = "SELECT major_version,minor_version,sub_minor_version,file_type,file_size,time_stamp ";
$query .= "FROM ".$dmsdb->prefix("dms_object_versions")." ";
$query .= "WHERE row_id='".$object->current_version_row_id."'";
//print $query;
$current_version = $dmsdb->query($query,'ROW');

// Get routing data
$query  = "SELECT * FROM ".$dmsdb->prefix("dms_routing_data")." ";
$query .= "WHERE obj_id='".$link_id."'";
//print $query;
$routing_data = $dmsdb->query($query,'ROW');

// Get permissions
$perms_level = dms_perms_level($obj_id);

if ($object->obj_status == CHECKEDOUT) 
  {
  $checked_out = TRUE;
  }
else
  {
  $checked_out = FALSE;
  }

  
// Message Box
include_once 'inc_message_box.php';
dms_message_box();
dms_dhtml_mb_functions();
  

// Options Menu

print "<script type='text/javascript'>\r";
print "<!--\r";
print "var thresholdY = 15; // in pixels; threshold for vertical repositioning of layer\r";
print "var ordinata_margin = 20; // to start the layer a bit above the mouse vertical coordinate\r";
print "// -->\r";
print "</script>\r";

print "<script type='text/javascript' src='".XOOPS_URL."/include/layersmenu.js'></script>\r";

print "<script language='JavaScript'>\r";
print "<!--\r";
print "currentX = -1;\r";
print "function grabMouseX(e) {\r";
print "  if ((DOM && !IE4) || Opera5) {\r";
print "    currentX = e.clientX;\r";
print "    } else if (NS4) {\r";
print "    currentX = e.pageX;\r";
print "    } else {\r";
print "    currentX = event.x;\r";
print "    }\r";
/*
print "  if (DOM && !IE4 && !Opera5 && !Konqueror) {\r";
print "    currentX += window.pageXoffset;\r";
print "      } else if (IE4 && DOM && !Opera5 && !Konqueror) {\r";
print "      currentX += document.body.scrollLeft;\r";
print "    }\r";
*/
print "  }\r";

print "// -->\r";
print "</script>\r";

print "<script type='text/javascript'>\r";
print "<!--\r";

print "function popUpMenu() {\r";
print "shutdown();\r";
print "setleft('div_menu',currentX);\r";
print "popUp(\"div_menu\",true);\r";
print "}\r";

print "function moveLayers() {\r";
print "grabMouseX;\r";
print "setleft('div_menu',currentX);\r";
print "settop('div_menu',currentY);\r";
print "}\r";

print "function shutdown() {\r";
print "popUp('div_menu',false);\r";
print "}\r";

print "if (NS4) {\r";
print "document.onmousedown = function() { shutdown(); }\r";
print "} else {\r";
print "document.onclick = function() { shutdown(); }\r";
print "}\r";

print "// -->\r";
print "</script>\r";

print "<div id='div_menu' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

print "<table ".$dms_config['class_narrow_header']." width='150' cellspacing='1' style='width: 6em;'>\r";

print "<th nowrap='nowrap' align='center'>Options</th>\r";

print "<tr><td ".$dms_config['class_narrow_content']." nowrap='nowrap' align='center'>\r";
//

if ( ($perms_level == EDIT) || ($perms_level == OWNER) )
	{
	// Checkin/Checkout/Cancel Checkout Buttons
	if ($checked_out==FALSE)
		{
		print "<a href='file_checkout.php?obj_id=".$obj_id."&return_url=link_options.php?obj_id=".$link_id."\"'>". _DMS_CHECKOUT ."</a><BR>\r";
		}
	
	if ($checked_out==TRUE)
		{
		print "<a href='file_checkin.php?obj_id=".$obj_id."&return_url=link_options.php?obj_id=".$link_id."'>". _DMS_CHECKIN ."</a><BR>\r";
	
		if (($checked_out==TRUE) && ( ($xoopsUser->isAdmin()) || ($object->obj_checked_out_user_id == $xoopsUser->getVar('uid')) ) )
			{
			print "<a href='file_checkout_cancel.php?obj_id=".$obj_id."&return_url=link_options.php?obj_id=".$link_id."'>". _DMS_CANCEL_CHECKOUT ."</a><BR>\r";
			}
		}
	}

//print "<a href='link_delete.php?link_id=".$link_id."'>". _DMS_DELETE ."</a><BR>\r";


// Copy/Move/Delete Buttons
if (( $checked_out==FALSE) || ($dms_admin_flag == 1) )
	{  
	if (($checked_out==FALSE) && ($object->lifecycle_id == 0))
		{
//		print "<a href='file_copy.php?obj_id=".$obj_id."'>" . _DMS_COPY . "</a>&nbsp;\r";
		print "<a href='link_move.php?obj_id=".$link_id."'>Move</a>&nbsp;\r";
		}

	if (( $checked_out==FALSE) || ($dms_admin_flag == 1) )
		{
		print "<a href='obj_delete.php?obj_id=".$link_id."'>Delete</a><BR>\r";
		}
	}



// E-mail button and Export buttons
if($dms_config['document_email_enable']=='1')
	{
	print "<a href='file_email.php?obj_id=".$obj_id."&return_url=link_options.php?obj_id=".$link_id."'>". _DMS_EMAIL. "</a>&nbsp;\r";
	}

print "<a href='file_retrieve.php?function=export&obj_id=".$obj_id."'>" . _DMS_EXPORT . "</a><BR>\r";

//print "<a href='index.php'>". _DMS_EXIT ."</a><BR>\r";


//
print "</td></tr>\r";

print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
print "</td></tr>\r";

print "</table>\r";

print "</div>\r";

print "<script language='JavaScript'>\r";
print "<!--\r";
print "moveLayers();\r";
print "loaded = 1;\r";
print "// -->\r";
print "</script>\r";

// End Options Menu
  
  
  
// Add the version_view() javascript function
print "<SCRIPT LANGUAGE='Javascript'>\r";
print "function version_view()\r";
print "  {\r";
print "  if (document.frm_options.slct_version_view.value == 0) return;\r";
print "  var url = 'file_retrieve.php?function=vv&obj_id=".$obj_id."&ver_id=';\r";
print "  url = url + document.frm_options.slct_version_view.value;\r";
print "  window.open(url);\r";
print "  }\r";
print "</SCRIPT>\r";

  
print "<form method='post' name='frm_options' action='file_options.php'>\r";
print "<table width='100%' border='0'>\r";

print "  <tr>\r";
  
print "    <td>\r";
print "      <table border='0'>\r";

display_dms_header(2);
   
print "        <tr><td colspan='2'><BR></td></tr>\r";

print "        <tr>\r";
print "          <td>\r";
	
if ($perms_level > BROWSE)
	print "<a href='#' title='View Document' onclick='javascript:void(window.open(\"file_retrieve.php?function=view&obj_id=".$obj_id."\"))'><font size='3'><b>".$object->obj_name."</b></font></a>\r";
else
	print "<font size='3'><b>".$object->obj_name."</b></font>\r";
		
print "          </td>\r";

print "          <td align='right'>\r";
	
if ( ( ($perms_level == READONLY) || ($perms_level == EDIT) || ($perms_level == OWNER) ) && ($dms_config['checkinout_enable'] == 1) )  
	{

	// Display the "view version or rendition" select box
	print "            View older version:  ";
	
	print "            <select name='slct_version_view' onchange='version_view();'>\r";
	print "            <option value='0'>None</option>\r";
	
	$query  = "SELECT row_id,major_version,minor_version,sub_minor_version FROM ".$dmsdb->prefix('dms_object_versions')." ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$result = $dmsdb->query($query);
	
	while($result_data = $dmsdb->getarray($result))
		{
		print "            <option value='".$result_data['row_id']."'>";
		print $result_data['major_version'].".".$result_data['minor_version'].".".$result_data['sub_minor_version'];
		print "            </option>\r";
		}
	
	print "          </select>\r";
	print "        </td>\r";
	print "      </tr>\r";
	}


print "  <tr><td colspan='2'><BR></td></tr>\r";

// Options Menu 
print "  <tr>\r";
print "    <td align='left' valign='top' colspan='2' class='".$dms_config['class_content']."'>\r";
print "            <input type='button' name='btn_options' value='"._DMS_OPTIONS."' onmouseover='grabMouseX(event); moveLayerY(\"div_menu\", currentY, event); popUpMenu();'>&nbsp;&nbsp;";

// Optional Help Button
dms_help_system("link_options",10);

print "            <input type='button' name='btn_exit' value='"._DMS_EXIT."' onclick='location=\"index.php\";'>";
print "    </td>\r";
print "  </tr>\r";
// Options Menu End

print "        <tr><td colspan='2'><BR></td></tr>\r";


if ($perms_level >= BROWSE)    
  {
  // Display the properties
  print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;". _DMS_PROPERTIES ."</td></tr>\r";
  print "        <tr><td colspan='2'><BR></td></tr>\r";
  print "        <tr>\r";
  print "          <td width='30%' align='left'>&nbsp;&nbsp;&nbsp;". _DMS_NAME_DOT ."</td>";
  print "          <td align='left'>".$object->obj_name."</td>\r";
  print "        </tr>\r";

  if ($perms_level > BROWSE)
    {
    //print "        <tr><td colspan='2'><BR></td></tr>\r";
  
    display_file_properties($obj_id,3);
    }  
  
  print "        <tr><td colspan='2'><BR></td></tr>\r";
  
  if ($object->obj_status == CHECKEDOUT)
    {
    // Display the name of the user who has this document checked out.
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;". _DMS_CHECKED_OUT_BY ."</td></tr>\r";
	
	$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." WHERE uid='".$object->obj_checked_out_user_id."'";
	$result = $dmsdb->query($query,'ROW');
	
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	print "        <tr><td align='left' colspan='2'>&nbsp;&nbsp;&nbsp;".$result->uname."</td></tr>\r";
	print "        <tr><td colspan='2'><BR></td></tr>\r";
	}
  }

// Display document information
print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;". _DMS_INFORMATION ."</td></tr>\r";
print "        <tr><td colspan='2'><BR></td></tr>\r";
  
print "        <tr>\r";
print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;". _DMS_DOC_OWNER ."</td>";
print "          <td align='left'>".$xoopsUser->getUnameFromId(dms_perms_owner_user_id($obj_id))."</td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;". _DMS_PERMISSION_LEVEL ."</td>";
print "          <td align='left'>";
  
switch ($perms_level)
  {
  case BROWSE:
	print _DMS_BROWSE;
	break;
  case READONLY:
	print _DMS_READ_ONLY;
	break;
  case EDIT:
	print _DMS_EDIT;
	break;
  case OWNER:
	print _DMS_OWNER;
	break;
  default:
    print _DMS_NONE;
  }
  
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left' width='30%'>&nbsp;&nbsp;&nbsp;". _DMS_CURRENT_VERSION ."</td>";
print "          <td align='left'>".$current_version->major_version.".".$current_version->minor_version.$current_version->sub_minor_version."</td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_ROUTED_BY ."</td>";
print "          <td align='left'>".$xoopsUser->getUnameFromId($routing_data->source_user_id)."</td>\r";
print "        </tr>\r";
  
print "        <tr>\r";
print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_ROUTED ."</td>";
print "          <td align='left'>".strftime("%d-%B-%Y %I:%M%p",$routing_data->time_stamp)."</td>\r";
print "        </tr>\r"; 
  
if ($perms_level >= BROWSE)
  {    
  print "        <tr>\r";
  print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_MODIFIED ."</td>";
  print "          <td align='left'>".strftime("%d-%B-%Y %I:%M%p",$current_version->time_stamp)."</td>\r";
  print "        </tr>\r";
  
  print "        <tr>\r";
  print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_CREATED ."</td>";
  print "          <td align='left'>".strftime("%d-%B-%Y %I:%M%p",$object->time_stamp_create)."</td>\r";
  print "        </tr>\r";

  print "        <tr>\r";
  print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_SIZE ."</td>";
  print "          <td align='left'>".$current_version->file_size." bytes</td>\r";
  print "        </tr>\r";
  }  
 
	// If this document was created with a template, display the name of the template    
	if ($object->template_obj_id > 0)
		{
		// Get object information
		$query  = "SELECT obj_name ";
		$query .= "FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".$object->template_obj_id."'";  
		$template_object = $dmsdb->query($query,'ROW');
		
		print "        <tr>\r";
		print "          <td align='left'>&nbsp;&nbsp;&nbsp;". _DMS_TEMPLATE_NAME ."</td>";
		print "          <td align='left'>".$template_object->obj_name."</td>\r";
		print "        </tr>\r";
		}
  
  
  print "        <tr><td colspan='2'><BR></td></tr>\r";

/*      
if ($perms_level > BROWSE)
  {  
  // Display the "view version or rendition" select box
  print "        <tr><td colspan='2' align='left' ".$dms_config['class_subheader'].">&nbsp;". _DMS_VIEW_VERSION_OF_DOC ."</td></tr>\r";
  
  print "        <tr><td colspan='2'><BR></td></tr>\r";
  
  print "        <tr>\r";
  print "          <td align='left'>&nbsp;&nbsp;&nbsp;\r"; 
  print "            <select name='slct_version_view' onchange='version_view();'>\r";
  print "            <option value='0'>None</option>\r";
  
  $query  = "SELECT row_id,major_version,minor_version,sub_minor_version FROM ".$dmsdb->prefix('dms_object_versions')." ";
  $query .= "WHERE obj_id='".$obj_id."'";
  $result = $dmsdb->query($query);
 
  while($result_data = $dmsdb->getarray($result))
    {
    print "            <option value='".$result_data['row_id']."'>";
    print $result_data['major_version'].".".$result_data['minor_version'].".".$result_data['sub_minor_version'];
    print "            </option>\r";
    }
    
  print "          </select>\r";
  print "        </td>\r";
  print "      </tr>\r";

  print "      <tr><td colspan='2'><BR></td></tr>\r";
  }
*/
  
  
if ($perms_level < BROWSE)
  { 
  print "        <tr><td><BR></td></tr>\r";
  print "        <tr>\r";
  print "          <td align='center' colspan='2'><b>" . _DMS_NO_PERMISSION_TO_ACCESS_DOC . "</b></td>\r";
  print "        </tr>\r";
  
  print "        <tr><td colspan='2'><BR></td></tr>\r";
  }
  
      
print "      </table>\r";
print "    </td>\r";
print "  </tr>\r";

print "</table>\r";
print "</form>\r";

include_once XOOPS_ROOT_PATH.'/footer.php';

dms_show_mb();

?>
