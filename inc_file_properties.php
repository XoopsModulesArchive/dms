<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 10/22/2003                                //
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
// inc_file_properties.php


include_once 'inc_defines.php';
//include_once 'inc_perms_check.php';
include_once 'inc_dms_functions.php';


function display_file_properties($obj_id,$num_spaces)
	{
	global $dmsdb,$dms_tab_index;

	$spaces = "";
	for ($x = 0; $x < $num_spaces; $x++)
	{
	$spaces = $spaces."&nbsp;";
	} 

	$dms_tab_index = 110;
	for ($index = 0; $index <= 9; $index++)
		{
		$query = "SELECT data FROM ".$dmsdb->prefix("dms_config")." WHERE name='property_".$index."_name'";
		$prop_name = $dmsdb->query($query,'data');
		if (strlen($prop_name) > 1)
			{
			$table_col = "property_".$index;
	
			$query = "SELECT property_".$index." FROM ".$dmsdb->prefix("dms_object_properties")." WHERE obj_id='".$obj_id."'";
			$obj_prop_val = $dmsdb->query($query,$table_col);
    
			$query  = "SELECT row_id,select_box_option FROM ".$dmsdb->prefix("dms_object_properties_sb")." ";
			$query .= "WHERE property_num='".$index."' ";
			$query .= "ORDER BY disp_order";
			$options  = $dmsdb->query($query);
			$num_db_rows = $dmsdb->getnumrows();
	
			print "<tr>\r";
			print "  <td align='left'>\r";
			print "    ".$spaces.$prop_name.":  ";
			print "  </td>\r";
			print "  <td align='left'>\r";

			if($num_db_rows > 0)
				{
				print "    <select name='var_property_".$index."' class='cContentSection' tabindex='".$dms_tab_index++."'>\r";
	  
				while($option_data = $dmsdb->getarray($options))
					{
					print "      <option value='".$option_data['row_id']."'";
					if ($obj_prop_val == $option_data['row_id']) print " selected";
					print ">".$option_data['select_box_option']."</option>\r";
					}
	  
				print "    </select>\r";
				}
			else
				{
				// Keep quotes below to allow apostrophes to be used in the properties fields.
				print '    <input type="text" name="var_property_'.$index.'" value="'.$obj_prop_val.'" size="40" maxlength="250" tabindex="'.$dms_tab_index++.'">'."\r";
				}  
	
			print "  </td>\r";
			print "</tr>\r";
			}
		}
	}


function initial_file_properties()
	{
	global $dmsdb,$dms_tab_index;

	for ($index = 0;$index <=9; $index++)
		{
		$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='property_".$index."_name'";
		$prop_name = $dmsdb->query($query,'data');
  
		$query  = "SELECT row_id,select_box_option FROM ".$dmsdb->prefix("dms_object_properties_sb")." ";
		$query .= "WHERE property_num='".$index."' ";
		$query .= "ORDER BY disp_order";
		$options  = $dmsdb->query($query);
		$num_db_rows = $dmsdb->getnumrows();
  
		if (strlen($prop_name) > 1)
			{
			print "<tr>\r";
			print "  <td align='left'>\r";
			print "    ".$prop_name.":  ";
			print "  </td>\r";
			print "  <td align='left'>\r";
    
			if($num_db_rows > 0)
				{
				print "    <select name='var_property_".$index."' class='cContentSection' tabindex='".$dms_tab_index++."'>\r";
	  
				while($option_data = $dmsdb->getarray($options))
					{
					print "      <option value='".$option_data['row_id']."'>".$option_data['select_box_option']."</option>\r";
					}
				print "    </select>\r";
				}
			else
				{
				// Keep quotes below to allow apostrophes to be used in the properties fields.
				print '    <input type="text" name="var_property_'.$index.'" value="" size="40" maxlength="250" class="cContentSection" tabindex="'.$dms_tab_index.'">'."\r";
				}  
	
			print "  </td>\r";
			print "</tr>\r";
			}
		}
	}


function update_file_properties($obj_id)
	{
	global $dmsdb;

	for ($index = 0; $index <= 9; $index++)
		{
		$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='property_".$index."_name'";
		$prop_name = $dmsdb->query($query,'data');
		if (strlen($prop_name) > 1)
			{

			switch ($index)
				{
				case 0:    $post_vars = dms_get_var('var_property_0');   break;
				case 1:    $post_vars = dms_get_var('var_property_1');   break;
				case 2:    $post_vars = dms_get_var('var_property_2');   break;
				case 3:    $post_vars = dms_get_var('var_property_3');   break;
				case 4:    $post_vars = dms_get_var('var_property_4');   break;
				case 5:    $post_vars = dms_get_var('var_property_5');   break;
				case 6:    $post_vars = dms_get_var('var_property_6');   break;
				case 7:    $post_vars = dms_get_var('var_property_7');   break;
				case 8:    $post_vars = dms_get_var('var_property_8');   break;
				case 9:    $post_vars = dms_get_var('var_property_9');   break;
				}

			$post_vars = dms_strprep($post_vars);
	  
			$query  = "UPDATE ".$dmsdb->prefix('dms_object_properties')." ";
			$query .= "SET property_".$index."='".$post_vars."' ";
			$query .= "WHERE obj_id='".$obj_id."'";
			$dmsdb->query($query);
			}  
		}
	}

?>
