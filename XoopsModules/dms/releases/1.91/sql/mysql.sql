#
# Table structure for `dms_config`
#

CREATE TABLE dms_config (
  name varchar(30) NOT NULL default '',
  data varchar(255) NOT NULL default ''
)  TYPE=MyISAM;

INSERT INTO dms_config
  VALUES ('version','1.91');
INSERT INTO dms_config
  VALUES ('time_stamp','0');
INSERT INTO dms_config
  VALUES ('doc_path','');
INSERT INTO dms_config
  VALUES ('dms_title','Document Management System');
INSERT INTO dms_config
  VALUES ('admin_display','1');
INSERT INTO dms_config
  VALUES ('default_interface','2');
INSERT INTO dms_config
  VALUES ('max_file_sys_counter','100');
INSERT INTO dms_config
  VALUES ('adn_enable','0');
INSERT INTO dms_config
  VALUES ('adn_mask','');
INSERT INTO dms_config
  VALUES ('adn_mask_char','');
INSERT INTO dms_config
  VALUES ('adn_prop_field','-1');
INSERT INTO dms_config
  VALUES ('adv_enable','0');
INSERT INTO dms_config
  VALUES ('adv_mask','');
INSERT INTO dms_config
  VALUES ('adv_mask_char','');
INSERT INTO dms_config
  VALUES ('extern_doc_access','0');
INSERT INTO dms_config
  VALUES ('pdftk_enable','0');
INSERT INTO dms_config
  VALUES ('pdftk_path','');
INSERT INTO dms_config
  VALUES ('full_text_search','0');
INSERT INTO dms_config
  VALUES ('full_text_search_cdo','0');
INSERT INTO dms_config
  VALUES ('search_limit','100');
INSERT INTO dms_config
  VALUES ('swish-e_path','');
INSERT INTO dms_config
  VALUES ('template_root_obj_id','0');
INSERT INTO dms_config
  VALUES ('updates_root_obj_id','0');
INSERT INTO dms_config
  VALUES ('routing_enable','0');
INSERT INTO dms_config
  VALUES ('routing_auto_inbox','0');
INSERT INTO dms_config
  VALUES ('routing_email_enable','0');
INSERT INTO dms_config
  VALUES ('routing_email_subject','A document has been routed to your DMS inbox');
INSERT INTO dms_config
  VALUES ('routing_email_from','');
INSERT INTO dms_config
  VALUES ('document_email_enable','0');
INSERT INTO dms_config
  VALUES ('document_email_subject','A document has been sent to you from the DMS');
INSERT INTO dms_config
  VALUES ('document_email_from','');
INSERT INTO dms_config
  VALUES ('sub_email_enable','0');
INSERT INTO dms_config
  VALUES ('sub_email_subject','A document has been accessed in the DMS.');
INSERT INTO dms_config
  VALUES ('sub_email_from','');
INSERT INTO dms_config
  VALUES ('notify_enable','0');
INSERT INTO dms_config
  VALUES ('notify_email_subject','DMS Notification');
INSERT INTO dms_config
  VALUES ('notify_email_from','');
INSERT INTO dms_config
  VALUES ('property_0_name','');
INSERT INTO dms_config
  VALUES ('property_1_name','');
INSERT INTO dms_config
  VALUES ('property_2_name','');
INSERT INTO dms_config
  VALUES ('property_3_name','');
INSERT INTO dms_config
  VALUES ('property_4_name','');
INSERT INTO dms_config
  VALUES ('property_5_name','');
INSERT INTO dms_config
  VALUES ('property_6_name','');
INSERT INTO dms_config
  VALUES ('property_7_name','');
INSERT INTO dms_config
  VALUES ('property_8_name','');
INSERT INTO dms_config
  VALUES ('property_9_name','');
INSERT INTO dms_config
  VALUES ('class_content','');
INSERT INTO dms_config
  VALUES ('class_header','even');
INSERT INTO dms_config
  VALUES ('class_subheader','even');
INSERT INTO dms_config
  VALUES ('class_narrow_header','head');
INSERT INTO dms_config
  VALUES ('class_narrow_content','odd');
INSERT INTO dms_config
  VALUES ('anon_user_id','0');
INSERT INTO dms_config
  VALUES ('purge_enable','1');
INSERT INTO dms_config
  VALUES ('purge_level','2');
INSERT INTO dms_config
  VALUES ('purge_delay','0');
INSERT INTO dms_config
  VALUES ('purge_limit','2');
INSERT INTO dms_config
  VALUES ('doc_display_limit','100');  
INSERT INTO dms_config
  VALUES ('misc_text_disp_template','1');
INSERT INTO dms_config
  VALUES ('misc_text_disp_lc_stage','1');
INSERT INTO dms_config
  VALUES ('inherit_perms','0');
INSERT INTO dms_config
  VALUES ('group_source','PORTAL');
INSERT INTO dms_config
  VALUES ('lifecycle_enable','0');
INSERT INTO dms_config
  VALUES ('lifecycle_name_preserve','0');
INSERT INTO dms_config
  VALUES ('lifecycle_del_previous','0');
INSERT INTO dms_config
  VALUES ('lifecycle_alpha_move','0');
INSERT INTO dms_config
  VALUES ('comments_enable','0');
INSERT INTO dms_config
  VALUES ('checkinout_enable','0');
INSERT INTO dms_config
  VALUES ('prop_perms_enable','1');
INSERT INTO dms_config
  VALUES ('init_config_lock','UNLOCKED');
INSERT INTO dms_config
  VALUES ('doc_name_sync','0');
INSERT INTO dms_config
  VALUES ('doc_hist_block_rows','10');                  
INSERT INTO dms_config
  VALUES ('OS','unknown');
INSERT INTO dms_config
  VALUES ('write_job_server_config','0');
  
#
# Table structure for `dms_object_properties_sb`
#

CREATE TABLE dms_object_properties_sb (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  property_num tinyint(2) unsigned NOT NULL default '99',
  disp_order tinyint(2) unsigned NOT NULL default '0',
  select_box_option varchar(255) NOT NULL default '',
  PRIMARY KEY (row_id)
)  TYPE=MyISAM;
    
#
# Table structure for `dms_file_sys_counters`
#

CREATE TABLE dms_file_sys_counters (
  layer_1 bigint(5) unsigned NOT NULL default '0',
  layer_2 bigint(5) unsigned NOT NULL default '0',
  layer_3 bigint(5) unsigned NOT NULL default '0',
  file bigint(5) unsigned not NULL default '0'
)  TYPE=MyISAM;

INSERT INTO dms_file_sys_counters
  VALUES ('1','1','1','1');
  
#
# Table structure for table `dms_objects`
#

CREATE TABLE dms_objects (
  obj_id bigint(14) unsigned NOT NULL auto_increment,
  ptr_obj_id bigint(14) unsigned NOT NULL default '0',
  template_obj_id bigint(14) unsigned NOT NULL default '0',
  obj_type tinyint(2) unsigned NOT NULL default '0',
  obj_name varchar(255) NOT NULL default '',
  obj_status tinyint(2) unsigned NOT NULL default '0',
  obj_owner bigint(14) NOT NULL default '0',
  obj_checked_out_user_id bigint(14) NOT NULL default '0',
  current_version_row_id bigint(14) unsigned NOT NULL default '0',
  lifecycle_id bigint(14) unsigned NOT NULL default '0',
  lifecycle_stage bigint(14) unsigned NOT NULL default '0',
  time_stamp_create varchar(12) NOT NULL default '0',
  time_stamp_delete varchar(12) NOT NULL default '0',
  time_stamp_expire varchar(12) NOT NULL default '0',
  misc_text varchar(255) NOT NULL default '',
  file_type varchar(50) NOT NULL default 'unchecked',
  PRIMARY KEY (obj_id), INDEX (obj_owner)
)  TYPE=MyISAM;

#
# Table structure for table `dms_object_perms`
#

CREATE TABLE dms_object_perms (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  ptr_obj_id bigint(14) unsigned NOT NULL default '0',
  user_id bigint(14) unsigned NOT NULL default '0',
  group_id bigint(14) unsigned NOT NULL default '0',
  user_perms tinyint(2) NOT NULL default '0',
  group_perms tinyint(2) NOT NULL default '0',
  everyone_perms tinyint(2) NOT NULL default '0',
  PRIMARY KEY (row_id), INDEX (ptr_obj_id)
) TYPE=MyISAM;

#
# Table structure for `dms_object_versions`
#

CREATE TABLE dms_object_versions (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  obj_id bigint(14) unsigned NOT NULL default '0',
  major_version smallint(5) unsigned NOT NULL default '0',
  minor_version smallint(5) unsigned NOT NULL default '0',
  sub_minor_version smallint(5) unsigned NOT NULL default '0',
  init_version_flag tinyint(2) NOT NULL default '0',
  file_path varchar(255) NOT NULL default '',
  file_name varchar(255) NOT NULL default '',
  file_type varchar(50) NOT NULL default '',
  file_size varchar(10) NOT NULL default '',
  time_stamp varchar(12) NOT NULL default '0',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

#
# Table structure for `dms_object_version_comments`
#

CREATE TABLE dms_object_version_comments (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  dov_row_id bigint(14) unsigned NOT NULL default '0',
  comment text NOT NULL,
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

#
# Table structure for `dms_object_properties`
# 

CREATE TABLE dms_object_properties (
  obj_id bigint(14) unsigned NOT NULL,
  property_0 varchar(255) NOT NULL default '',
  property_1 varchar(255) NOT NULL default '',
  property_2 varchar(255) NOT NULL default '',
  property_3 varchar(255) NOT NULL default '',
  property_4 varchar(255) NOT NULL default '',
  property_5 varchar(255) NOT NULL default '',
  property_6 varchar(255) NOT NULL default '',
  property_7 varchar(255) NOT NULL default '',
  property_8 varchar(255) NOT NULL default '',
  property_9 varchar(255) NOT NULL default '',
  PRIMARY KEY (obj_id)
) TYPE=MyISAM;

#
# Table structure for `dms_object_misc`
#

CREATE TABLE dms_object_misc (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  obj_id bigint(14) unsigned NOT NULL default '0',
  data_type tinyint(2) unsigned NOT NULL default '0',
  data varchar(255) NOT NULL default '',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;


#
# Table structure for `dms_routing_data`
#

CREATE TABLE dms_routing_data (
  obj_id bigint(14) unsigned NOT NULL default '0',
  source_user_id bigint(14) unsigned NOT NULL default '0',
  time_stamp varchar(12) NOT NULL default '0'
) TYPE=MyISAM;


#
# Table structure for `dms_exp_folders`
#

CREATE TABLE dms_exp_folders (
  user_id bigint(14) unsigned NOT NULL default '0',
  folder_id bigint(14) unsigned NOT NULL default '0'
)  TYPE=MyISAM;

#
# Table structure for `dms_active_folder`
#

CREATE TABLE dms_active_folder (
  user_id bigint(14) unsigned NOT NULL default '0',
  folder_id bigint(14) unsigned NOT NULL default '0'
)  TYPE=MyISAM;

#
# Table structure for table `dms_lifecycles`
#
  
CREATE TABLE dms_lifecycles (  
  lifecycle_id bigint(14) unsigned NOT NULL auto_increment,
  obj_id bigint(14) unsigned NOT NULL default '0',
  lifecycle_name varchar(255) NOT NULL default '',
  lifecycle_descript varchar(255) NOT NULL default '',
  PRIMARY KEY (lifecycle_id)
)  TYPE=MyISAM;

#
# Table structure for `dms_lifecycle_stages`
#

CREATE TABLE dms_lifecycle_stages (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  lifecycle_id bigint (14) unsigned NOT NULL,
  obj_id bigint(14) unsigned NOT NULL default '0',
  lifecycle_stage tinyint(2) unsigned NOT NULL default '0' ,
  new_obj_location bigint(14) unsigned NOT NULL default '0',
  lifecycle_stage_name varchar(255) NOT NULL default '',
  opt_obj_copy_location bigint(14) unsigned NOT NULL default '0',
  flags smallint(8) NOT NULL default '0',
  perms_group_id bigint(14) unsigned NOT NULL default '0',
  PRIMARY KEY (row_id)
)  TYPE=MyISAM;

#
# Table structure for table `dms_audit_log`
#

CREATE TABLE dms_audit_log (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  time_stamp varchar(12) NOT NULL default '0',
  user_id bigint(14) unsigned NOT NULL default '0',
  obj_id bigint(14) unsigned NOT NULL default '0',
  descript varchar(75) NOT NULL default '',
  obj_name varchar(255) NOT NULL default '',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

#
# Table structure for table `dms_subscriptions`
#

CREATE TABLE dms_subscriptions (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  obj_id bigint(14) unsigned NOT NULL default '0',
  user_id bigint(14) unsigned NOT NULL default '0',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

#
# Table structure for table `dms_groups`
#

CREATE TABLE dms_groups (
  group_id bigint(14) unsigned NOT NULL auto_increment,
  group_name varchar(50) NOT NULL default '',
  group_description text NOT NULL,
  group_type varchar(10) NOT NULL default 'PERMS',
  PRIMARY KEY (group_id)
) TYPE=MyISAM;

#
# Table structure for table `dms_groups_users_link`
#

CREATE TABLE dms_groups_users_link (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  group_id bigint(14) unsigned NOT NULL default '0',
  user_id bigint(14) unsigned NOT NULL default '0',
  PRIMARY KEY(row_id)
) TYPE=MyISAM;

#
# Table structure for table `dms_notify`
#

CREATE TABLE dms_notify (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  obj_id bigint(14) unsigned NOT NULL default '0', 
  user_id bigint(14) unsigned NOT NULL default '0',
  group_id bigint(14) unsigned NOT NULL default '0',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

#
# Table structure for table `dms_user_doc_history`
#

CREATE TABLE dms_user_doc_history (
  user_id bigint(14) unsigned NOT NULL default '0',
  obj_id bigint(14) unsigned NOT NULL default '0',
  time_stamp varchar(12) NOT NULL default '0',
  obj_name varchar(30) NOT NULL default ''
) TYPE=MyISAM;

#
# Table structure for table `dms_user_prefs`
#

CREATE TABLE dms_user_prefs (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  user_id bigint(14) unsigned NOT NULL default '0',
  pref_name varchar(30) NOT NULL default '',
  data varchar(30) NOT NULL default '',
  PRIMARY KEY (row_id)
)  TYPE=MyISAM;

#
# Table structure for table `dms_help_system
#

CREATE TABLE dms_help_system (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  help_id varchar(30) NOT NULL default '',
  obj_id_ptr bigint(14) unsigned NOT NULL default '0',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

# 
# Table structure for table `dms_job_services`
#


CREATE TABLE dms_job_services (
  row_id bigint(14) unsigned NOT NULL auto_increment,
  job_name varchar(50) NOT NULL default '',
  job_type smallint(8) NOT NULL default '0',
  next_run_time varchar(12) NOT NULL default '0',
  flags smallint(8) NOT NULL default '0',
  sched_day smallint(8) NOT NULL default '0',
  sched_hour smallint(8) NOT NULL default '0',
  sched_minute smallint(8) NOT NULL default '0',
  obj_id_a bigint(14) unsigned NOT NULL default '0',
  obj_id_b bigint(14) unsigned NOT NULL default '0',
  obj_id_c bigint(14) unsigned NOT NULL default '0',
  text varchar(255) NOT NULL default '',
  PRIMARY KEY (row_id)
) TYPE=MyISAM;

