<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 6/24/2003                                //
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

// DMS Functions
// inc_upgrade_manager.php

function dms_update_tables($old_version,$current_version)
	{
	while($old_version != $current_version)
		{
		if ($old_version==0.94) dms_update_0094();  
		if ($old_version==0.95) dms_update_0095();
		if ($old_version==0.96) dms_update_0096();
		if ($old_version==0.97) dms_update_0097();
		if ($old_version==0.98) dms_update_0098();
		if ($old_version==0.99) dms_update_0099();
		if ($old_version==1.00) dms_update_0100();
		if ($old_version==1.10) dms_update_0110();
		if ($old_version==1.20) dms_update_0120();
		if ($old_version==1.30) dms_update_0130();
		if ($old_version==1.31) dms_update_0131();
		if ($old_version==1.40) dms_update_0140();
		if ($old_version==1.50) dms_update_0150();
		if ($old_version==1.60) dms_update_0160();
		if ($old_version==1.70) dms_update_0170();
		if ($old_version==1.80) dms_update_0180();
		if ($old_version==1.81) dms_update_0181();
		if ($old_version==1.82) dms_update_0182();
		if ($old_version==1.83) dms_update_0183();
		if ($old_version==1.84) dms_update_0184();
		if ($old_version==1.85) dms_update_0185();
		if ($old_version==1.86) dms_update_0186();
		if ($old_version==1.87) dms_update_0187();
		if ($old_version==1.88) dms_update_0188();
		if ($old_version==1.89) dms_update_0189();
		if ($old_version==1.90) dms_update_0190();
		if ($old_version==1.91) dms_update_0191();
		if ($old_version==1.92) dms_update_0192();
		if ($old_version==1.93) dms_update_0193();
		if ($old_version==1.94) dms_update_0194();
		$old_version = dms_get_old_version();
		}
	return;
	}
	
// This function must remain because the update system requires a separate method of returning the version.
function dms_get_old_version()
	{
	global $dmsdb;
	
	$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='version'";
	$old_version = $dmsdb->query($query,'data');

	// If no version is found, assume version 0.94
	if ($old_version =="") $old_version = 0.94;
	return($old_version);
	}

function dms_update_0094()
	{
	global $dmsdb;

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('version','0.95')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('class_content','')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('class_header','even')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('class_subheader','even')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('class_narrow_header','head')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('class_narrow_content','odd')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('anon_user_id','0')";
	$dmsdb->query($query);
	}

function dms_update_0095()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='0.96' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0096()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='0.97' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "ADD COLUMN time_stamp_delete varchar(12) not null default '0' AFTER time_stamp";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "CHANGE time_stamp time_stamp_create varchar(12) not null default '0'";
	$dmsdb->query($query);

	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "SET time_stamp_delete='".time()."' ";
	$query .= "where obj_status='2'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "CHANGE time_stamp time_stamp varchar(12) not null default '0'";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_routing_data")." ";
	$query .= "CHANGE time_stamp time_stamp varchar(12) not null default '0'";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_audit_log")." ";
	$query .= "CHANGE time_stamp time_stamp varchar(12) not null default '0'";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "ADD COLUMN perms_limit_flag tinyint(2) not null default '0' AFTER everyone_perms";
	$dmsdb->query($query);
	}

function dms_update_0097()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='0.98' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('updates_root_obj_id','0')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('time_stamp','".time()."')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('routing_email_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('routing_email_subject','A document has been routed to your DMS inbox')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('routing_email_from','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('document_email_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('document_email_subject','A document has been sent to you from the DMS')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('document_email_from','')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('purge_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('purge_level','2')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('purge_delay','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('purge_limit','2')";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "DROP COLUMN perms_limit_flag";
	$dmsdb->query($query);
	}

function dms_update_0098()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='0.99' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('default_interface','1')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pc_enable','1')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pc_cache_size','500')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pc_cache_refresh','50')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pc_refresh_delay','30')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('sub_email_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('sub_email_subject','A document has been accessed in the DMS.')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('sub_email_from','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('doc_display_limit','100')";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_subscriptions")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "obj_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "user_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "PRIMARY KEY (row_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_object_misc")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "obj_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "data_type tinyint(2) unsigned NOT NULL default '0', ";
	$query .= "data varchar(255) NOT NULL default '', ";
	$query .= "PRIMARY KEY (row_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycles")." ";
	$query .= "ADD COLUMN obj_id bigint(14) NOT NULL default '0' AFTER lifecycle_id";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "ADD COLUMN obj_id bigint(14) NOT NULL default '0' AFTER lifecycle_id";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "ADD COLUMN lifecycle_stage_name varchar(255) NOT NULL default '' AFTER new_obj_location";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "ADD COLUMN change_perms_flag tinyint(2) NOT NULL default '0' AFTER lifecycle_stage_name";
	$dmsdb->query($query);
	
	$query  = "DROP TABLE ".$dmsdb->prefix("dms_lifecycle_apply_perms");
	$dmsdb->query($query);
	
	$query  = "DROP TABLE ".$dmsdb->prefix("dms_lifecycle_doc_perms");
	$dmsdb->query($query);
	}

function dms_update_0099()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.00' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_config")." ";
	$query .= "CHANGE name name varchar(30) NOT NULL default ''";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "DROP COLUMN lifecycle_suspend_flag";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "ADD COLUMN misc_text varchar(255) NOT NULL default '' AFTER time_stamp_delete";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('misc_text_disp_template','1')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('misc_text_disp_lc_stage','1')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('inherit_perms','0')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('max_file_sys_counter_lock','LOCKED')";
	$dmsdb->query($query);
	}
	
function dms_update_0100()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.10' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pdftk_enable','0')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('pdftk_path','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('group_source','PORTAL')";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_object_version_comments")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "dov_row_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "comment text NOT NULL default '', ";
	$query .= "PRIMARY KEY (row_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_groups")." (";
	$query .= "group_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "group_name varchar(50) unsigned NOT NULL default '', ";
	$query .= "group_description text NOT NULL default '', ";
	$query .= "group_type varchar(10) NOT NULL default 'PERMS', ";
	$query .= "PRIMARY KEY (group_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_groups_users_link")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "group_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "user_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "PRIMARY KEY (row_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	}

function dms_update_0110()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.20' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('notify_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('notify_email_subject','A document has been accessed in the DMS.')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('notify_email_from','')";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_notify")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment, ";
	$query .= "obj_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "user_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "group_id bigint(14) unsigned NOT NULL default '0', ";
	$query .= "PRIMARY KEY (row_id) ";
	$query .= ") TYPE=MyISAM;";
	$dmsdb->query($query);
	}

function dms_update_0120()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.30' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0130()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.31' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adn_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adn_mask','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adn_mask_char','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adn_prop_field','-1')";
	$dmsdb->query($query);
	}

function dms_update_0131()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.40' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('checkinout_enable','1')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('routing_enable','1')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('routing_auto_inbox','0')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('lifecycle_enable','1')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('comments_enable','1')";
	$dmsdb->query($query);
	}

function dms_update_0140()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.50' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adv_enable','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adv_mask','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('adv_mask_char','')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('lifecycle_name_preserve','0')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('lifecycle_del_previous','0')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('lifecycle_alpha_move','0')";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "ADD COLUMN init_version_flag tinyint(2) NOT NULL default '0' AFTER sub_minor_version";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_user_doc_history")." (";
	$query .= "user_id bigint(14) unsigned NOT NULL default '0',";
	$query .= "obj_id bigint(14) unsigned NOT NULL default '0',";
	$query .= "time_stamp varchar(12) NOT NULL default '0',";
	$query .= "obj_name varchar(30) NOT NULL default ''";
	$query .= ") TYPE=MyISAM";
	$dmsdb->query($query);
	}

function dms_update_0150()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.60' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('full_text_search_cdo','0')";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_help_system")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment,";
	$query .= "help_id varchar(30) NOT NULL default '',";
	$query .= "obj_id_ptr bigint(14) unsigned NOT NULL default '0',";
	$query .= "PRIMARY KEY (row_id)";
	$query .= ") TYPE=MyISAM";
	$dmsdb->query($query);
	}

function dms_update_0160()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.70' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_user_prefs")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment,";
	$query .= "user_id bigint(14) unsigned NOT NULL default '0',";
	$query .= "pref_name varchar(30) NOT NULL default '',";
	$query .= "data varchar(30) NOT NULL default '',";
	$query .= "PRIMARY KEY (row_id)";
	$query .= ") TYPE=MyISAM";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "ADD COLUMN opt_obj_copy_location bigint(14) unsigned NOT NULL default '0' AFTER lifecycle_stage_name";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "ADD COLUMN perms_group_id bigint(14) unsigned NOT NULL default '0' AFTER change_perms_flag";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('prop_perms_enable','1')";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ADD INDEX (obj_owner)";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_object_perms")." ADD INDEX (ptr_obj_id)";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_lifecycle_stages")." SET change_perms_flag = '1'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_lifecycle_stages")." ";
	$query .= "CHANGE change_perms_flag flags smallint(8) not null default '0'";
	$dmsdb->query($query);
	}

function dms_update_0170()
	{
	global $dmsdb;
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.80' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0180()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.81' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0181()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.82' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='max_file_sys_counter_lock'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('init_config_lock','LOCKED')";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('doc_name_sync','0')";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "SET data_type='20' WHERE data_type='15'";
	$dmsdb->query($query);
	}

function dms_update_0182()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.83' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "CREATE TABLE ".$dmsdb->prefix("dms_job_services")." (";
	$query .= "row_id bigint(14) unsigned NOT NULL auto_increment,";
	$query .= "job_type smallint(8) NOT NULL default '0',";
	$query .= "next_run_time varchar(12) NOT NULL default '0',";
	$query .= "flags smallint(8) NOT NULL default '0',";
	$query .= "sched_day smallint(8) NOT NULL default '0',";
	$query .= "sched_hour smallint(8) NOT NULL default '0',";
	$query .= "sched_minute smallint(8) NOT NULL default '0',";
	$query .= "obj_id_a bigint(14) unsigned NOT NULL default '0',";
	$query .= "obj_id_b bigint(14) unsigned NOT NULL default '0',";
	$query .= "obj_id_c bigint(14) unsigned NOT NULL default '0',";
	$query .= "PRIMARY KEY (row_id)";
	$query .= ") TYPE=MyISAM";
	$dmsdb->query($query);
	}
	
function dms_update_0183()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.84' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_job_services")." ";
	$query .= "ADD COLUMN text varchar(255) NOT NULL default '' AFTER obj_id_c";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('extern_doc_access','0')";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_job_services")." ";
	$query .= "ADD COLUMN job_name varchar(50) NOT NULL default '' AFTER row_id";
	$dmsdb->query($query);
	}

function dms_update_0184()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.85' WHERE name='version'";
	$dmsdb->query($query);
	
	//  Check the config of the default_interface.  If it is 4, change it to 2.  The Single Directory Beta interface is not the
	//  Single Directory interface.
	
	$query  = "SELECT data FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='default_interface'";
	$default_interface = $dmsdb->query($query,"data");
	print "DI:  :".$default_interface.":";

	if($default_interface == '4')
		{
		$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
		$query .= "SET data='2' WHERE name='default_interface'";
		$dmsdb->query($query);
		}
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='pc_enable'";
	$dmsdb->query($query);
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='pc_cache_size'";
	$dmsdb->query($query);
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='pc_cache_refresh'";
	$dmsdb->query($query);
	
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_config")." ";
	$query .= "WHERE name='pc_refresh_delay'";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('doc_hist_block_rows','10')";
	$dmsdb->query($query);
	}

function dms_update_0185()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.86' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0186()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.87' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0187()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.88' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0188()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.89' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_audit_log")." ";
	$query .= "ADD COLUMN obj_name VARCHAR(255) NOT NULL default '' AFTER descript";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "ADD COLUMN file_type varchar(50) NOT NULL default 'unknown' AFTER misc_text";
	$dmsdb->query($query);
	}

function dms_update_0189()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.90' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='2' WHERE name='default_interface'";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('OS','unknown')";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "SET file_type = 'unchecked' WHERE file_type = 'unknown'";
	$dmsdb->query($query);
	
	$query  = "UPDATE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "SET file_type = 'web_page' WHERE obj_type='40'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "MODIFY file_type varchar(50) not null default 'unchecked'";
	$dmsdb->query($query);
	}

function dms_update_0190()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.91' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "ADD COLUMN time_stamp_expire varchar(12) NOT NULL default '0' AFTER time_stamp_delete";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('write_job_server_config','0')";
	$dmsdb->query($query);
	
	$dms_config['write_job_server_config'] = 0;
	}
	
function dms_update_0191()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.92' WHERE name='version'";
	$dmsdb->query($query);
	
	$query  = "ALTER TABLE ".$dmsdb->prefix("dms_objects")." ";
	$query .= "ADD COLUMN num_views smallint(8) NOT NULL default '0' AFTER file_type";
	$dmsdb->query($query);
	
	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('doc_expiration_enable','0')";
	$dmsdb->query($query);
	}

function dms_update_0192()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.93' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0193()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.94' WHERE name='version'";
	$dmsdb->query($query);
	}

function dms_update_0194()
	{
	global $dmsdb;

	$query  = "UPDATE ".$dmsdb->prefix("dms_config")." ";
	$query .= "SET data='1.95' WHERE name='version'";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('search_summary_flag','1')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('search_summary_c_before','100')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('search_summary_c_after','300')";
	$dmsdb->query($query);

	$query  = "INSERT INTO ".$dmsdb->prefix("dms_config")." ";
	$query .= "VALUES ('search_results_per_page','10')";
	$dmsdb->query($query);
	}

?>
