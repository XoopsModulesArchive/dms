<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 1/4/2006                                //
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

// inc_message_box.php


print "<script language='JavaScript'>\r";
print "<!--\r";
print "  var interval_id;\r";
print "// -->\r";
print "</script>\r";


// Check to see if the message session variable exists
if(isset($_SESSION['dms_message']))
	{
	$dms_message = $_SESSION['dms_message'];
	}
else
	{
	$_SESSION['dms_message'] = "";
	$dms_message = "";
	}

// message box functions

function dms_message_box()
	{
	global $dms_config,$dms_message;

	print "<div id='div_message_box' style='position: absolute; visibility: hidden; z-index:1000;'>\r";

	print "<table ".$dms_config['class_narrow_header']." width='250' cellspacing='1' style='width: 20em;'>\r";

	print "<th nowrap='nowrap' align='center'>Message</th>\r";

	print "<tr><td align='center' ".$dms_config['class_narrow_content']." nowrap='nowrap'>\r";

	print $dms_message."<BR>";
	
	print "<BR><a href='#' onmouseover='shutdown_mb();'>[Close]</a>\r";
	
	print "</td></tr>\r";

	print "</table>\r";

	print "</div>\r";
	}
	
	
function dms_dhtml_mb_functions($load_layersmenu = 1)
	{
	global $dms_config;

	print "<script type='text/javascript'>\r";
	print "<!--\r";
	print "var thresholdY = 15; // in pixels; threshold for vertical repositioning of layer\r";
	print "var ordinata_margin = 20; // to start the layer a bit above the mouse vertical coordinate\r";
	print "// -->\r";
	print "</script>\r";

	if($load_layersmenu == 1) print "<script type='text/javascript' src='".XOOPS_URL."/modules/dms/layersmenu.js'></script>\r";
	
	print "<script language='JavaScript'>\r";
	print "<!--\r";
/*
	print "currentX = -1;\r";
	print "function grabMouseX(e) {\r";
	print "  if ((DOM && !IE4) || Opera5) {\r";
	print "    currentX = e.clientX;\r";
	print "    } else if (NS4) {\r";
	print "    currentX = e.pageX;\r";
	print "    } else {\r";
	print "    currentX = event.x;\r";
	print "    }\r";
*/
	/*
	print "  if (DOM && !IE4 && !Opera5 && !Konqueror) {\r";
	print "    currentX += window.pageXoffset;\r";
	print "      } else if (IE4 && DOM && !Opera5 && !Konqueror) {\r";
	print "      currentX += document.body.scrollLeft;\r";
	print "    }\r";
	*/
//	print "  }\r";


	print "function scroll_mb() {\r";
	print "  var y=window.scrollY;\r";
	print "  setleft(\"div_message_box\",100);\r";
	print "  settop(\"div_message_box\",(200 + y) );\r";
	print "}\r";

	print "function show_mb() {\r";
	print "  shutdown_mb();\r";
	print "  setleft(\"div_message_box\",200);\r";
	print "  settop(\"div_message_box\",200);\r";
	print "  popUp(\"div_message_box\",true);\r";
	print "}\r";

/*
	print "function moveLayersfmb() {\r";
	print "grabMouseX;\r";
	print "setleft(\"div_fading_message_box\",currentX);\r";
	print "settop(\"div_fading_message_box\",currentY);\r";
	print "}\r";
*/
	print "function shutdown_mb() {\r";
	print "  window.clearInterval(interval_id)\r";
	print "  popUp(\"div_message_box\",false);\r";
	print "}\r";
/*
	print "if (NS4) {\r";
	print "document.onmousedown = function() { shutdownfmb(); }\r";
	print "} else {\r";
	print "document.onclick = function() { shutdownfmb(); }\r";
	print "}\r";
*/
/*
	print "moveLayersfmb();\r";
	print "loaded = 1;\r";
*/
	print "// -->\r";
	print "</script>\r";
	}
	
function dms_show_mb()
	{
	global $dms_message;

	if(0 == strlen($dms_message)) return;
	
	print "<script type='text/javascript'>\r";
	print "<!--\r";
	print "  show_mb();\r";
	print "  setTimeout(\"shutdown_mb()\",3500);\r";
//	print "  interval_id = setInterval(\"scroll_mb()\",10);\r";  //  Code to scroll the message box.  Disabled due to IE conflict.
	print "// -->\r";
	print "</script>\r";
	
	$_SESSION['dms_message'] = "";
	}
?>
