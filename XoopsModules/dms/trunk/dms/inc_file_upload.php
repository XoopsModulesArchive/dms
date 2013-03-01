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

// inc_file_upload.php


// Initialize magic_number.  This number is used to create unique file names in order to guarantee that 2 file names
// will not be identical if 2 users upload a file at the exact same time.  100000 will allow almost 100000 users to use
// this system.  Ok, the odds of this happening are slim; but, I want the odds to be zero.
$magic_number = 100000;

// Get the maximum size of a file, that can be uploaded, from the php.ini configuration.
$upload_max_filesize = dms_str_get_bytes(ini_get("upload_max_filesize"));

// Determine temporary file name to use for uploads.
$temp_file_name = "tfn".(string) time().(string) ($magic_number + $dms_user_id);


 function dms_get_file_upload_error_message($error_code)
	{
	$error_message = "";
	
	if($error_code > 0)
		{
		// Determine Error Message
		switch($error_code)
			{
			case UPLOAD_ERR_INI_SIZE:
				$error_message = "The document is too large to upload.  (UPLOAD_ERR_INI_SIZE) (MFS:".$upload_max_filesize.")";
				break;
			
			case UPLOAD_ERR_FORM_SIZE:
				$error_message = "The document is too large to upload.  (UPLOAD_ERR_FORM_SIZE) (MFS:".$upload_max_filesize.")";
				break;
			
			case UPLOAD_ERR_PARTIAL:
				$error_message = "The document was only partially uploaded.  (UPLOAD_ERR_PARTIAL)";
				break;
				
			case UPLOAD_ERR_NO_FILE:
				$error_message = "No document was uploaded.  (UPLOAD_ERR_NO_FILE)";
				break;
				
			case UPLOAD_ERR_NO_TMP_DIR:
				$error_message = "Temporary directory is not available.  (UPLOAD_ERR_NO_TMP_DIR)";
				break;
				
			case UPLOAD_ERR_CANT_WRITE:
				$error_message = "Unable to write document.  (UPLOAD_ERR_CANT_WRITE)";
				break;
			}
		}
	
	return($error_message);
	}
 
?>
