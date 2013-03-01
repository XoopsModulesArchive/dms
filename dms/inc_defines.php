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

// defines.php


// Object Types
define('FILE',0);
define('FOLDER',1);
define('INBOXEMPTY',2);
define('INBOXFULL',3);
define('DOCLINK',4);
define('DISKDIR',5);
define('LIFECYCLE',20);
define('LIFECYCLE_STAGE',21);
define('PERMISSION',30);
define('WEBPAGE',40);

// Object Status
define('NORMAL',0);
define('CHECKEDOUT',1);
define('DELETED',2);
define('PURGED_FS',3);  // Purged, Files Saved
define('PURGED_FD',4);  // Purged, Files Deleted

// Document Purge Levels
define('FLAGGING',0);  // Only the status flag is changed
define('FILES',1);     // The status flag is changed and the files are deleted
define('TOTAL',2);     // All database entries and files are deleted (No auditing)

// Permissions
define('NONE',0);
define('BROWSE',1);
define('READONLY',2);
define('EDIT',3);
define('OWNER',4);
//define('DENY',99);

// Search Parameters
define('IS',1);
define('CONTAINS',2);
define('STARTSWITH',3);
define('ISANYOF',4);
define('ISALLOF',5);

// Version Changes
define('SAME',1);
define('INCSUB',2);
define('INCMINOR',3);
define('INCMAJOR',4);

// dms_object_misc data types
define('PATH',1);
define('URL',2);
define('FOLDER_AUTO_LIFECYCLE_NUM',11);
define('FLAGS',20);
define('PERMS_GROUP',40);

// Job Server

// Job types
define('FTS_INDEX',0);
define('OBJ_DELETION',1);
define('PERM_CHANGE',2);
define('EXTERN_PUB',3);
define('EXEC_SCRIPT',4);

// Scheduling
define('ON',0);
define('AT',0);
define('EVERY',1);

define('DAY',0);
define('MONDAY',1);
define('TUESDAY',2);
define('WEDNESDAY',3);
define('THURSDAY',4);
define('FRIDAY',5);
define('SATURDAY',6);
define('SUNDAY',7);
?>
