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

// inc_dynamic_menus.php

dms_admin_menu();
dms_search_menu();
dms_dhtml_menu_functions();

	
function dms_admin_menu()
	{
	global $dms_config;

	print "<div id='div_menu_admin' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

	print "<table ".$dms_config['class_narrow_header']." width='120' cellspacing='1' style='width: 12em;'>\r";

	print "<th nowrap='nowrap' align='center'>Administration</th>\r";

	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";

	print "<a href='audit_log_select_user.php'>Auditing</a><BR>";
	print "<a href='group_editor.php'>Group Editor</a><BR>";
	print "<a href='job_server_manager.php'>Job Server</a><BR>";
	print "<a href='lifecycle_manager.php'>Lifecycles</a><BR>";
	print "<a href='perms_manager.php'>Permissions Groups</a><BR>";
	print "<a href='statistics.php'>Statistics</a><BR>";
	print "<BR>";
	print "<a href='folder_copy_by_id.php'>Copy Folder Contents</a><BR>";
	print "<a href='perms_xfer_ownership.php'>Transfer Ownership</a><BR>";
	print "<BR>";
	print "<a href='config_main.php'>Configuration</a><BR>";
	
	print "<BR><a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	
	print "</td></tr>\r";

	/*
	print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	print "</td></tr>\r";
*/
	print "</table>\r";

	print "</div>\r";
	}

	
function dms_search_menu()
	{
	global $dms_config;

	print "<div id='div_menu_search' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

	print "<table ".$dms_config['class_narrow_header']." width='120' cellspacing='1' style='width: 12em;'>\r";

	print "<th nowrap='nowrap' align='center'>Search</th>\r";

	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";

	print "<a href='search_ft.php'>Full Text</a><BR>";
	print "<a href='search_prop.php'>Properties</a><BR>";

	
	print "<BR>";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	
	print "</td></tr>\r";
/*
	print "<tr><td style='margin-top: 5px; font-size: smaller; text-align: right;'>\r";
	print "<a href='#' onmouseover='shutdown();'>[Close]</a>\r";
	print "</td></tr>\r";
*/
	print "</table>\r";

	print "</div>\r";
	}

	
function dms_dhtml_menu_functions()
	{
	global $dms_config;

	print "<script type='text/javascript'>\r";
	print "<!--\r";
	print "var thresholdY = 15; // in pixels; threshold for vertical repositioning of layer\r";
	print "var ordinata_margin = 20; // to start the layer a bit above the mouse vertical coordinate\r";
	print "// -->\r";
	print "</script>\r";

	print "<script type='text/javascript' src='".XOOPS_URL."/modules/dms/layersmenu.js'></script>\r";

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
	
	print "currentX = currentX - 130;\r";
	/*
	print "  if (DOM && !IE4 && !Opera5 && !Konqueror) {\r";
	print "    currentX += window.pageXoffset;\r";
	print "      } else if (IE4 && DOM && !Opera5 && !Konqueror) {\r";
	print "      currentX += document.body.scrollLeft;\r";
	print "    }\r";
	*/
	print "  }\r";


	print "function popUpAdminMenu() {\r";
	print "shutdown();\r";
	print "setleft(\"div_menu_admin\",currentX);\r";
	print "popUp(\"div_menu_admin\",true);\r";
	print "}\r";

	print "function popUpSearchMenu() {\r";
	print "shutdown();\r";
	print "setleft(\"div_menu_search\",currentX);\r";
	print "popUp(\"div_menu_search\",true);\r";
	print "}\r";
	
	print "function moveLayers() {\r";
	print "grabMouseX;\r";
	print "setleft(\"div_menu_admin\",currentX);\r";
	print "settop(\"div_menu_admin\",currentY);\r";
	
	print "setleft(\"div_menu_search\",currentX);\r";
	print "settop(\"div_menu_search\",currentY);\r";
	
	print "}\r";

	print "function shutdown() {\r";
	print "popUp(\"div_menu_admin\",false);\r";
	print "popUp(\"div_menu_search\",false);\r";
	print "}\r";

	print "if (NS4) {\r";
	print "document.onmousedown = function() { shutdown(); }\r";
	print "} else {\r";
	print "document.onclick = function() { shutdown(); }\r";
	print "}\r";

	print "moveLayers();\r";
	print "loaded = 1;\r";
	print "// -->\r";
	print "</script>\r";
	}
	


?>