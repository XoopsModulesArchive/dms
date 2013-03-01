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

// index.php
// Configuration Page

include_once '../../mainfile.php';
include_once (XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include_once (XOOPS_ROOT_PATH."/include/cp_functions.php");
include_once 'inc_dms_functions.php';

include_once (XOOPS_ROOT_PATH."/modules/dms/inc_pal.php");

global $db; 

$os_types = array(0=>"Unknown",1=>"Linux",2=>"Unix",3=>"Windows");

//$hdn_update_form = dms_get_var("hdn_update_form");
if (dms_get_var("hdn_update_form") == "TRUE")
	{
	//  Anonymous User
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var('slct_anon_user_id')."' WHERE name='anon_user_id'";
	$dmsdb->query($query);
	
	//  ADN System
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_adn_enable")."' WHERE name='adn_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_adn_mask')."' ";
	$query .= "WHERE name='adn_mask'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_adn_mask_char')."' ";
	$query .= "WHERE name='adn_mask_char'";
	$dmsdb->query($query);

	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("slct_adn_prop_field")."' WHERE name='adn_prop_field'";
	$dmsdb->query($query);

	//  ADV System
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_adv_enable")."' WHERE name='adv_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_adv_mask')."' ";
	$query .= "WHERE name='adv_mask'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_adv_mask_char')."' ";
	$query .= "WHERE name='adv_mask_char'";
	$dmsdb->query($query);
	
	//  Checkin/Checkout/Versioning
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_checkinout_enable")."' WHERE name='checkinout_enable'";
	$dmsdb->query($query);
	
	//  Comments
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_comments_enable")."' WHERE name='comments_enable'";
	$dmsdb->query($query);
	
	//  Deletion System
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_purge_enable")."' WHERE name='purge_enable'";
	$dmsdb->query($query);
	
	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("slct_purge_level")."' WHERE name='purge_level'";
	$dmsdb->query($query);

	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("slct_purge_delay")."' WHERE name='purge_delay'";
	$dmsdb->query($query);
	
	//  Document Properties
	
	for ($index = 0; $index < 10; $index++)
	{
	switch ($index)
		{
		case 0:  $data = dms_get_var('txt_property_0_name');  break;
		case 1:  $data = dms_get_var('txt_property_1_name');  break;
		case 2:  $data = dms_get_var('txt_property_2_name');  break;
		case 3:  $data = dms_get_var('txt_property_3_name');  break;
		case 4:  $data = dms_get_var('txt_property_4_name');  break;
		case 5:  $data = dms_get_var('txt_property_5_name');  break;
		case 6:  $data = dms_get_var('txt_property_6_name');  break;
		case 7:  $data = dms_get_var('txt_property_7_name');  break;
		case 8:  $data = dms_get_var('txt_property_8_name');  break;
		case 9:  $data = dms_get_var('txt_property_9_name');  break;
		}

	$query =  "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$data."' ";
	$query .= "WHERE name='property_".$index."_name'";
	$dmsdb->query($query);
	}

	//  Document Repository
	
	$doc_path = dms_get_var('txt_doc_path');
	$doc_path = trim($doc_path);
	$doc_path = rtrim($doc_path,"/");
	$doc_path = rtrim($doc_path,"\\");
	$doc_path = str_replace("\\","\\\\",$doc_path);
	
	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".$doc_path."' WHERE name='doc_path'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_max_file_sys_counter")."' ";
	$query .= "WHERE name='max_file_sys_counter'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_doc_name_sync")."' ";
	$query .= "WHERE name='doc_name_sync'";
	$dmsdb->query($query);
	
	//  Document Templates
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var('txt_template_root_obj_id')."' ";
	$query .= "WHERE name='template_root_obj_id'";
	$dmsdb->query($query);
	
	//  Document History
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var('txt_doc_hist_block_rows')."' ";
	$query .= "WHERE name='doc_hist_block_rows'";
	$dmsdb->query($query);
	
	//  E-mail Configuration
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_document_email_enable")."' ";
	$query .= "WHERE name='document_email_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_document_email_subject")."' ";
	$query .= "WHERE name='document_email_subject'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_document_email_from")."' ";
	$query .= "WHERE name='document_email_from'";
	$dmsdb->query($query);
	
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_notify_enable")."' WHERE name='notify_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_notify_email_subject")."' ";
	$query .= "WHERE name='notify_email_subject'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_notify_email_from")."' ";
	$query .= "WHERE name='notify_email_from'";
	$dmsdb->query($query);
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_sub_email_enable")."' WHERE name='sub_email_enable'";
	$dmsdb->query($query);
	
	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_sub_email_subject")."' WHERE name='sub_email_subject'";
	$dmsdb->query($query);

	$query = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_sub_email_from")."' WHERE name='sub_email_from'";
	$dmsdb->query($query);
	
	//  External Document Access
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_extern_doc_access")."' ";
	$query .= "WHERE name='extern_doc_access'";
	$dmsdb->query($query);
	
	//  Interface Settings
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_enable_admin_display")."' ";
	$query .= "WHERE name='admin_display'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var('slct_default_interface')."' ";
	$query .= "WHERE name='default_interface'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_dms_title')."' ";
	$query .= "WHERE name='dms_title'";
	$dmsdb->query($query);
  
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_doc_display_limit')."' ";
	$query .= "WHERE name='doc_display_limit'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk('chk_misc_text_disp_template')."' ";
	$query .= "WHERE name='misc_text_disp_template'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk('chk_misc_text_disp_lc_stage')."' ";
	$query .= "WHERE name='misc_text_disp_lc_stage'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_class_content')."' ";
	$query .= "WHERE name='class_content'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_class_header')."' ";
	$query .= "WHERE name='class_header'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_class_subheader')."' ";
	$query .= "WHERE name='class_subheader'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_class_narrow_header')."' ";
	$query .= "WHERE name='class_narrow_header'";
	$dmsdb->query($query);
  
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var('txt_class_narrow_content')."' ";
	$query .= "WHERE name='class_narrow_content'";
	$dmsdb->query($query);

	//  Lifecycles
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_lifecycle_enable")."' ";
	$query .= "WHERE name='lifecycle_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_lifecycle_del_previous")."' ";
	$query .= "WHERE name='lifecycle_del_previous'";
	$dmsdb->query($query);
	
	$query =  "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_lifecycle_name_preserve")."' ";
	$query .= "WHERE name='lifecycle_name_preserve'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_lifecycle_alpha_move")."' ";
	$query .= "WHERE name='lifecycle_alpha_move'";
	$dmsdb->query($query);
	
	//  OS Types
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$os_types[dms_get_var("slct_OS")]."' WHERE name = 'os'";
	$dmsdb->query($query);
	
	//  Permissions Configuration
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_prop_perms_enable")."' WHERE name='prop_perms_enable'";
	$dmsdb->query($query);
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_inherit_perms")."' WHERE name='inherit_perms'";
	$dmsdb->query($query);
/*
	//  PDFTK Configuration
	$pdftk_path = $HTTP_POST_VARS['txt_pdftk_path'];
	$pdftk_path = trim($pdftk_path);
	$pdftk_path = rtrim($pdftk_path,"/");
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$pdftk_path."' WHERE name='pdftk_path'";
	$dmsdb->query($query);
  
	if ($HTTP_POST_VARS['chk_pdftk_enable']) $value = '1';
	else $value = '0';
  
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$value."' WHERE name='pdftk_enable'";
	$dmsdb->query($query);
*/
	
	//  Routing
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_routing_enable")."' WHERE name='routing_enable'";
	$dmsdb->query($query);
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_routing_auto_inbox")."' WHERE name='routing_auto_inbox'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_routing_email_enable")."' ";
	$query .= "WHERE name='routing_email_enable'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_routing_email_subject")."' ";
	$query .= "WHERE name='routing_email_subject'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." SET data = '".dms_get_var("txt_routing_email_from")."' ";
	$query .= "WHERE name='routing_email_from'";
	$dmsdb->query($query);
	
	//  Search Configuration
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." ";
	$query .= "SET data = '".dms_get_var('txt_search_limit')."' ";
	$query .= "WHERE name='search_limit'";
	$dmsdb->query($query);

	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_enable_fts")."' WHERE name='full_text_search'";
	$dmsdb->query($query);
	 
	$swishe_path = dms_get_var('txt_swishe_path');
	$swishe_path = trim($swishe_path);
	$swishe_path = rtrim($swishe_path,"/");
	
	$query = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".$swishe_path."' WHERE name='swish-e_path'";
	$dmsdb->query($query);
  
	$query  = "UPDATE ".$dmsdb->prefix('dms_config')." SET data = '".dms_get_var_chk("chk_full_text_search_cdo")."' ";
	$query .= "WHERE name='full_text_search_cdo'";
	$dmsdb->query($query);
	
	dms_update_config_time_stamp();
	}

dms_get_config();
	
	include XOOPS_ROOT_PATH.'/header.php';
	
	print "<form method='post' action='config_main.php'>\r";
	
	print "<div ".$dms_config['class_content']." style='text-align: left' >\r";
	
	print "<b>DMS Configuration</b><BR><BR>\r";
	
	//  Anonymous User
	
	$anon_user_id=$dms_config['anon_user_id'];
	if(strlen($anon_user_id) < 1) $anon_user_id = 0;
	
	dms_display_spaces(5);
	print "Anonymous User:  ";
	
	$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." ORDER BY uname";
	$result = $dmsdb->query($query);
	
	print "<select name='slct_anon_user_id'>\r";
	print "<option value='0'>None</option>\r";
	
	while($result_data = $dmsdb->getarray($result))
		{
		print "<option value='".$result_data['uid']."' ";
		if ($anon_user_id == $result_data['uid']) print "selected";
		print ">".$result_data['uname']."</option>\r";
		}
	print "</select>\r";
	print "<BR><BR><BR>\r";
	
	// ADN System
	
	dms_display_spaces(5);
	print "Automatic Document Numbering System:<BR><BR>\r";
	
	$checked = $dms_config['adn_enable'];
	
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_adn_enable' ".$checked.">\r";
	
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'Document Number Mask:  ';
	printf("<input type=text name='txt_adn_mask' value='%s' size='30' maxlength='100'>",$dms_config['adn_mask']);
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'Mask Character:  ';
	printf("<input type=text name='txt_adn_mask_char' value='%s' size='1' maxlength='1'>",$dms_config['adn_mask_char']);
	print "<BR>\r";
	
	$adn_prop_field_value = array(-1=>"None",0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9);
	dms_display_spaces(10);
	print 'Optional Properties Field:  ';
	print "<select name='slct_adn_prop_field'>\r";
	
	foreach ($adn_prop_field_value as $value=>$key)
		{
		$selected = "";
		if($value==$dms_config['adn_prop_field']) $selected = " selected";
		print "<option value='".$value."' ".$selected.">".$key."</option>\r";
		}
	
	print "</select>\r";
	
	print "<BR><BR><BR>\r";

	
	// ADV System
	
	dms_display_spaces(5);
	print "Automatic Document Versioning System:<BR><BR>\r";
	
	$checked = $dms_config['adv_enable'];
	
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_adv_enable' ".$checked.">\r";
	
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'Document Version Mask:  ';
	printf("<input type=text name='txt_adv_mask' value='%s' size='30' maxlength='100'>",$dms_config['adv_mask']);
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'Mask Character:  ';
	printf("<input type=text name='txt_adv_mask_char' value='%s' size='1' maxlength='1'>",$dms_config['adv_mask_char']);
	print "<BR>\r";
	
	print "<BR><BR><BR>\r";

	
	//  Checkin/Checkout/Versioning
	dms_display_spaces(5);
	printf("Checkin/Checkout/Versioning:<BR><BR>\r");
	
	$checked = $dms_config['checkinout_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_checkinout_enable' ".$checked.">\r";
	print "<BR><BR><BR>\r";
		
	//  Comments
	dms_display_spaces(5);
	printf("Comments:<BR><BR>\r");
	
	$checked = $dms_config['comments_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_comments_enable' ".$checked.">\r";
	print "<BR><BR><BR>\r";
	
	// Deletion System
	
	$purge_delay_value = array(0=>"No Delay",1 => 1,2 => 2, 3=>3,4=>4,5=>5,10=>10,20=>20,30=>30,60=>60,90=>90);
	$purge_level_value = array(FLAGGING=>"Retain Files and Audit",FILES=>"Delete Files and Audit",TOTAL=>"Delete Files and Data");

	dms_display_spaces(5);
	print "Deletion System:<BR><BR>\r";

	$checked = $dms_config['purge_enable'];
	
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	print "Enable Permanent Document Deletion:  ";
	print "<input type='checkbox' name='chk_purge_enable' ".$checked.">\r";
	
	print "<BR>\r";
	
	dms_display_spaces(10);
	print "Delay Until Permanent Document Deletion (days):  ";
	
	print "<select name='slct_purge_delay'>\r";
	
	foreach ($purge_delay_value as $value=>$key)
		{
		$selected = "";
		if($value==$dms_config['purge_delay']) $selected = " selected ";
		print "<option value='".$value."' ".$selected.">".$key."</option>\r";
		}
		
	print "</select>\r";
	
	print "<BR>\r";
	
	dms_display_spaces(10);
	print "Type of Document Deletion:  ";
	
	print "<select name='slct_purge_level'>\r";
	
	foreach ($purge_level_value as $value=>$key)
		{
		$selected = "";
		if($value==$dms_config['purge_level']) $selected = " selected ";
		print "<option value='".$value."' ".$selected.">".$key."</option>\r";
		}
	
	print "</select>\r";
	print "<BR><BR><BR>\r";
	
	//  Document Properties
	
	dms_display_spaces(5);
	printf("Document Properties:<BR><BR>\r");
	
	for ($index = 0; $index < 10; $index++)
		{
		$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='property_".$index."_name'";
		$result = $dmsdb->query($query,'data');
		
		dms_display_spaces(10);
		print "Property ".$index.":  ";
		print "<input type=text name='txt_property_".$index."_name' value='".$result."' size='50' maxlength='250'><BR>\r";
		}

	print "<BR><BR>\r";
	
	//  Document Repository
	
	dms_display_spaces(5);
	printf("Document Repository:<BR><BR>");
	
	dms_display_spaces(10);
	print 'Document Storage Path:  ';
	if($dms_config['init_config_lock'] == "UNLOCKED")
		printf("<input type=text name='txt_doc_path' value='%s' size='60' maxlength='250'>",$dms_config['doc_path']);
	else
		{
		print $dms_config['doc_path'];
		print "<input type='hidden' name='txt_doc_path' value='".$dms_config['doc_path']."'>";
		}
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'Document Storage Tuning:  ';
	if($dms_config['init_config_lock'] == "UNLOCKED")
		printf("<input type=text name='txt_max_file_sys_counter' value='%s' size='4' maxlength='4'>",$dms_config['max_file_sys_counter']);
	else
		{
		print $dms_config['max_file_sys_counter'];
		print "<input type='hidden' name='txt_max_file_sys_counter' value='".$dms_config['max_file_sys_counter']."'>";
		}
		
	print "<BR>\r";
	
	$checked = $dms_config['doc_name_sync'];
	
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	print "Synchronize File Names With Document Names:  ";
	print "<input type='checkbox' name='chk_doc_name_sync' ".$checked.">\r";
	
	print "<BR><BR><BR>\r";
	
	//  Document Templates
	
	dms_display_spaces(5);
	printf("Document Templates:<BR>\r");
	
	dms_display_spaces(10);
	print 'Object ID of Root Folder for Document Templates:  ';
	print "<input type='text' name='txt_template_root_obj_id' value='".$dms_config['template_root_obj_id']."' size='8' maxlength='8'>";
	print "&nbsp;";
	print "<input type='button' name='btn_slct_template_root_dir' value='Select' onclick='location=\"config_doc_templates_slct_root_dir.php\";'>\r";
	printf("<BR><BR><BR>\r");
	
	//  Document History
	
	dms_display_spaces(5);
	printf("Document History:<BR>\r");
	
	dms_display_spaces(10);
	print 'Number of Rows in Document History Block:  ';
	print "<input type='text' name='txt_doc_hist_block_rows' value='".$dms_config['doc_hist_block_rows']."' size='3' maxlength='3'>\r";
	printf("<BR><BR><BR>\r");
	
	//  E-mail Configuration
	
	dms_display_spaces(5);
	printf("E-Mail Configuration:<BR><BR>");
	
	dms_display_spaces(10);
	print "Document:<BR>";

	$checked = $dms_config['document_email_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(15);
	print "Enable E-Mail:  ";
	print "<input type='checkbox' name='chk_document_email_enable' ".$checked."><BR>\r";

	dms_display_spaces(15);
	print "Sender E-mail Address:  ";
	printf("<input type=text name='txt_document_email_from' value='%s' size='60' maxlength='60'><BR>",$dms_config['document_email_from']);

	dms_display_spaces(15);
	print "Subject Line:  ";
	printf("<input type=text name='txt_document_email_subject' value='%s' size='60' maxlength='250'><BR>",$dms_config['document_email_subject']);

	dms_display_spaces(10);
	print "Folder Subscriptions:<BR>";

	$checked = $dms_config['notify_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(15);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_notify_enable' ".$checked."><BR>\r";

	dms_display_spaces(15);
	print "Sender E-mail Address:  ";
	printf("<input type=text name='txt_notify_email_from' value='%s' size='60' maxlength='60'><BR>",$dms_config['notify_email_from']);

	dms_display_spaces(15);
	print "Subject Line:  ";
	printf("<input type=text name='txt_notify_email_subject' value='%s' size='60' maxlength='250'><BR>",$dms_config['notify_email_subject']);
	
	dms_display_spaces(10);
	print "Document Subscriptions:<BR>";
	
	$checked = $dms_config['sub_email_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(15);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_sub_email_enable' ".$checked."><BR>\r";

	dms_display_spaces(15);
	print "Sender E-mail Address:  ";
	printf("<input type=text name='txt_sub_email_from' value='%s' size='60' maxlength='60'><BR>",$dms_config['sub_email_from']);

	dms_display_spaces(15);
	print "Subject Line:  ";
	printf("<input type=text name='txt_sub_email_subject' value='%s' size='60' maxlength='250'><BR>",$dms_config['sub_email_subject']);
	printf("<BR><BR><BR>\r");
	
	
	// External Document Access
	dms_display_spaces(5);
	printf("External Document Access:<BR><BR>");
	
	$checked = $dms_config['extern_doc_access'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_extern_doc_access' ".$checked.">\r";
	printf("<BR><BR><BR>\r");
	
	//  Interface Settings
	
	dms_display_spaces(5);
	printf("Interface Settings:<BR><BR>\r");
	
	$checked = $dms_config['admin_display'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable Administration View:  ";
	print "<input type='checkbox' name='chk_enable_admin_display' ".$checked.">\r";
	print "<BR>\r";

	$selected = array();
	for($index = 1; $index <= 4; $index++)
		{
		$selected[$index] = "";
		if($dms_config['default_interface'] == $index) $selected[$index] = " selected";
		}
	
	dms_display_spaces(10);
	print "Default User Interface:  ";
	print "<select name='slct_default_interface'>\r";
	print "  <option value='2' ".$selected[2].">Single Directory</option>\r";
	print "</select>\r";
	
	print "<BR>\r";
	
	dms_display_spaces(10);
	print 'DMS Page Title:  ';
	printf("<input type=text name='txt_dms_title' value='%s' size='60'><BR>",$dms_config['dms_title']);
	
	dms_display_spaces(10);
	printf("Documents Displayed per Page:  ");
	printf("<input type=text name='txt_doc_display_limit' value='%s' size='4'><BR>",$dms_config['doc_display_limit']);
	
	$checked = $dms_config['misc_text_disp_template'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Display Document Template Name:  ";
	print "<input type='checkbox' name='chk_misc_text_disp_template' ".$checked.">\r";
	print "<BR>\r";
	
	$checked = $dms_config['misc_text_disp_lc_stage'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Display Lifecycle Stage:  ";
	print "<input type='checkbox' name='chk_misc_text_disp_lc_stage' ".$checked.">\r";
	print "<BR>\r";
	
	printf("<BR>");

	$query = 'SELECT data from '.$xoopsDB->prefix("dms_config")." WHERE name='class_content'";
	$result = $dmsdb->query($query,'data');
	
	dms_display_spaces(10);
	print 'Content Class:  ';
	printf("<input type=text name='txt_class_content' value='%s' size='60'><BR>",$result);
	
	$query = 'SELECT data from '.$xoopsDB->prefix("dms_config")." WHERE name='class_header'";
	$result = $dmsdb->query($query,'data');
	
	dms_display_spaces(10);
	print 'Header Class:  ';
	printf("<input type=text name='txt_class_header' value='%s' size='60'><BR>",$result);
	
	$query = 'SELECT data from '.$xoopsDB->prefix("dms_config")." WHERE name='class_subheader'";
	$result = $dmsdb->query($query,'data');
	
	dms_display_spaces(10);
	print 'Sub-Header Class:  ';
	printf("<input type=text name='txt_class_subheader' value='%s' size='60'><BR>",$result);
	
	$query = 'SELECT data from '.$xoopsDB->prefix("dms_config")." WHERE name='class_narrow_header'";
	$result = $dmsdb->query($query,'data');
	
	dms_display_spaces(10);
	print 'Narrow Header Class:  ';
	printf("<input type=text name='txt_class_narrow_header' value='%s' size='60'><BR>",$result);
	
	$query = 'SELECT data from '.$xoopsDB->prefix("dms_config")." WHERE name='class_narrow_content'";
	$result = $dmsdb->query($query,'data');
	
	dms_display_spaces(10);
	print 'Narrow Content Class:  ';
	printf("<input type=text name='txt_class_narrow_content' value='%s' size='60'>",$result);
	printf("<BR><BR><BR>\r");

	//  Job Server Configuration
	dms_display_spaces(5);
	print "Job Server:<BR><BR>\r";
	
	print "<BR>\r";
	dms_display_spaces(10);
	print "<input type='button' value='Write Configuration Files' onclick='location=\"config_write_js_config.php\";'>\r";

	print "<BR><BR><BR>\r";
	
	//  Lifecycles
	dms_display_spaces(5);
	printf("Lifecycles:<BR><BR>\r");
	
	$checked = $dms_config['lifecycle_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_lifecycle_enable' ".$checked.">\r";
	print "<BR>\r";
	
	$checked = $dms_config['lifecycle_del_previous'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Keep Final Revision Only:  ";
	print "<input type='checkbox' name='chk_lifecycle_del_previous' ".$checked.">\r";
	print "<BR>\r";
	
	$checked = $dms_config['lifecycle_name_preserve'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Keep Lifecycle Stage Name:  ";
	print "<input type='checkbox' name='chk_lifecycle_name_preserve' ".$checked.">\r";
	print "<BR>\r";
	
	$checked = $dms_config['lifecycle_alpha_move'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Move documents into aphabetical sub-folders:  ";
	print "<input type='checkbox' name='chk_lifecycle_alpha_move' ".$checked.">\r";
	
	print "<BR><BR><BR>\r";
	
	//  Operating System
	dms_display_spaces(5);
	print "Operating System:  ";
	
	print "<select name='slct_OS'>\r";
	foreach ($os_types as $value=>$key)
		{
		$selected = "";
		if($key==$dms_config['OS']) $selected = " selected ";
		print "<option value='".$value."' ".$selected.">".$key."</option>\r";
		}
	print "</select>\r";
	
	print "<BR><BR><BR>\r";
	
	//  Permissions System
	dms_display_spaces(5);
	printf("Permissions System:<BR><BR>\r");
	
	$checked = $dms_config['prop_perms_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	printf("Enable Permissions Propagation Button:  ");
	printf("<input type='checkbox' name='chk_prop_perms_enable' %s>\r",$checked);
	
	printf("<BR>\r");

	$checked = $dms_config['inherit_perms'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";
	
	dms_display_spaces(10);
	printf("Inherit Permissions from Parent Directory:  ");
	printf("<input type='checkbox' name='chk_inherit_perms' %s>\r",$checked);

	printf("<BR>\r");
	

	printf("<BR><BR><BR>\r");
	
/*
	//  PDFTK Configuration
	dms_display_spaces(5);
	print "PDFTK Configuration:<BR><BR>\r";
	
	$checked = $dms_config['pdftk_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_pdftk_enable' ".$checked."><BR>\r";

	dms_display_spaces(10);
	print "PDFTK path:  ";
	print "<input type=text name='txt_pdftk_path' value='".$dms_config['pdftk_path']."' size='60' maxlength='250'>\r";
	print "<BR><BR><BR>\r";
*/
	
	//  Routing
	dms_display_spaces(5);
	print "Routing:<BR><BR>";

	$checked = $dms_config['routing_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_routing_enable' ".$checked."><BR>\r";
	
	$checked = $dms_config['routing_auto_inbox'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable Automatic Inbox Creation:  ";
	print "<input type='checkbox' name='chk_routing_auto_inbox' ".$checked."><BR>\r";
	
	$checked = $dms_config['routing_email_enable'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(10);
	print "Enable E-Mail:  ";
	print "<input type='checkbox' name='chk_routing_email_enable' ".$checked."><BR>\r";

	dms_display_spaces(10);
	print "Sender E-mail Address:  ";
	printf("<input type=text name='txt_routing_email_from' value='%s' size='60' maxlength='60'><BR>",$dms_config['routing_email_from']);

	dms_display_spaces(10);
	print "Subject Line:  ";
	printf("<input type=text name='txt_routing_email_subject' value='%s' size='60' maxlength='250'><BR>",$dms_config['routing_email_subject']);

	print "<BR><BR>\r";
	
	//  Search Configuration
	
	dms_display_spaces(5);
	print "Search Configuration:<BR><BR>\r";
	
	dms_display_spaces(10);
	print "Search Limit:  ";
	printf("<input type=text name='txt_search_limit' value='%s' size='5' maxlength='5'><BR><BR>\r",$dms_config['search_limit']);
	
	dms_display_spaces(10);
	print "Full Text Search:<BR>\r";
	
	$checked = $dms_config['full_text_search'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(15);
	print "Enable:  ";
	print "<input type='checkbox' name='chk_enable_fts' ".$checked."><BR>\r";

	$checked = $dms_config['full_text_search_cdo'];
	if ($checked == '0') $checked = "";
	else $checked = " checked";

	dms_display_spaces(15);
	print "Search Current Document Versions Only:  ";
	print "<input type='checkbox' name='chk_full_text_search_cdo' ".$checked."><BR>\r";
	
	dms_display_spaces(15);
	print "SWISH-E path:  ";
	print "<input type=text name='txt_swishe_path' value='".$dms_config['swish-e_path']."' size='60' maxlength='250'>\r";
	print "<BR>\r";

	if(strlen($dms_config['doc_path']) > 2)
		{
		printf("<BR>\r");
		dms_display_spaces(15);
		print "<input type='button' value='Write Configuration Files' onclick='location=\"config_write_swishe_config.php\";'>\r";
		}
	
	print "<BR><BR><BR>\r";
	
	//  Update and Exit Buttons
	print "<BR><BR><BR>\r";
	print "<input type='hidden' name='hdn_update_form' value='TRUE'>\r";
	print "<input type='submit' value='Update'>\r";
	print "<input type='button' value='Exit' onclick='location=\"index.php\";'>\r";
	print "</form>";
	
	print "</div>";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
?>
