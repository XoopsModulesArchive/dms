

Readme File for the DMS module for Xoops



Contents:

A.  Installation
B.  Configuration
	1.  Location of configuration options.
	2.  Anonymous User
	3.  Automatic Document Numbering System
	4.  Automatic Document Versioning System
	5.  Checkin/Checkout/Versioning
	6.  Comments
	7.  Deletion System
	8.  Document Properties
	9.  Document Repository
	10. Document Templates
	11. E-Mail
	12. Interface Settings
	13. Lifecycles
	14. Permissions System
	15. Routing
	16. Search Configuration
	17. Update Manager
C.  Upgrading
D.  Lifecycles
E.  Troubleshooting
	1.  E-mail failure
	2.  Unable to upload documents




A.  Installation

	1.  Un-tar and/or unzip this module into the modules directory of your Xoops installation.
	2.  From the Xoops Modules Administration screen, click the install button next to the DMS icon.
	3.  Click on the install button from the module install screen.
	4.  Assuming that there aren't any error messages, this modules should be successfully installed.
	5.  In order to utilize this module, you must configure the document repository next.
	
B.  Configuration
	1.  Location of Configuration Options
	
		Only the Update Manager can be accessed from the Xoops Site Administration screen.  All other changes are made
		from the "Admin/Configuration" menu on the main screen of the module.

	2.  Anonymous User.  
	
		Anonymous users can be granted limited access to the system if a user account is created for this purpose.
		
		First a user account needs to be created, in Xoops.  This user account should only be used for anonymous
		user access to the DMS.  Next, from the DMS Configuration--Anonymous User screen, set the anonymous user
		account.  Any users, not logged in, attempting to access DMS will be assumed to be using this account.
		
		Any folders and files that are going to be set up for access by anonymous users should have the permissions
		set no higher than read-only.  If these permissions are set higher than read-only, for anonymous users, the
		DMS will force the permissions to read-only.

	3.  Automatic Document Numbering System
	
		This is a beta addition, if there is enough interest, I'll add instructions at a later date.
	
	4.  Automatic Document Versioning System
	
		See the instructions for the Automatic Document Numbering System, as this system is almost identical.
	
	5.  Checkin/Checkout/Versioning
	
		The ability to check-in, check-out, and set version numbers on documents can be enabled here.
	
	6.  Comments
		
		Enabling the comments option will allow users to add comments when modifying a file.  If either document or folder 
		subscriptions are enabled, the comments will be included in the e-mail.
	
	7.  Deletion System
	
		By default, documents are not deleted, instead they are marked as deleted and are visible to the administrator,
		who can easily restore these documents.  With the Deletion system enabled, documents can now be permanently 
		deleted immediately or after a set delay.  If a delay is set, the documents will be initially handled as though
		the deletion system is not enabled.  But, after the delay period, the documents will be deleted as per the type
		of document deletion selected.
		
		There are 3 types of document deletion and they are listed below with their descriptions.
		
			1.  Retain Files and Audit:  The documents are only flagged as deleted and are no longer visible to
				administrators.  They could be restored via SQL scripting.
			2.  Deleted Files and Audit:  The documents are flagged as deleted and the files are deleted.  Most 
				metadata is retained, however.  This option saves space by deleting the files while allowing for 
				auditing.
			3.  Delete Files and Data:  This option deletes everything.  No auditing information is retained.
	
	8.  Document Properties
	
		The document properties is a list of 10 fields that can optionally be added to aid in searching
		for documents.  These properties do not appear on the options screen of any document unless they
		are set.  These fields are free-form and you can name them anything.  An example would be to add 
		a "keyword."  If you type the word "keyword" in one of the properties fields, you can now upload
		a document, click on the search option, and search for that keyword.  Any documents with that 
		keyword as a property will be displayed as a result of the search.  Other examples would include
		having the name of the author of the document, the department that created the document, etc.
		
	9.  Document Repository Configuration  (This is the only required configuration.)
	
		The document repository is the location where all of the documents will be stored.  The 
		document storage path is the sub-directory that is the root of the document repository.
		An example of this location is /var/dms_repository.  The web server MUST have read/write
		permissions to this location.  If the web server does not have the rights to the location
		of the document repository, this module will not function and you will be unable to upload
		documents.
		
		The document storage tuning value is the number of sub-directories and/or files in each
		branch of the document repository tree.  The document repository consists of a root location
		and an unlimited number of sub-directories at the first level.  The second level will contain
		a number of sub-directories equal to the document storage tuning value.  The third level, of
		the document repository tree will be created in the same manor as the second level.  The 
		fourth level, of this tree, will contain a number of files up to the number of this document
		storage tuning value.  All levels of this tree are dynamically created as needed.  Leaving
		this value at it's default value of 100 will support up to 100,000,000 documents with 100
		sub-directories created at the root level of the document repository.  It is not adviseable
		to change this number after documents have been added to the system.
		
	10.  Document Templates
	
		The document templates screen contains a setting for the Template Root Directory ID.  This ID
		number is the object number of the template directory within DMS.  To create a template directory,
		simply create a directory within DMS and upload document templates to it.  Then, ensure that the 
		users who may utilize the templates have permission to access these documents.  Find the ID number
		by clicking on the Options screen for your template directory.  In the url, you will see a number
		following "?obj_id=".  That number is the Template Root Directory ID.  Set the Template Root
		Directory ID to that number or select the directory by clicking on the select button on the document 
		templates screen and selecting the correct folder.  A "Create Document" option will appear on the
		main screen above the "Import Document" option.  You will now be able to click on the "Create Document"
		option and create a document using any of the documents within the template directory as templates
		for your new document.
		
	11.	E-mail Configuration
	
		This screen allows you to enable e-mail functionallity for both sending documents, as attachments, directly
		from the DMS and sending e-mails to inform a user that a document has been routed to their DMS inbox.  In 
		addition, the subscriptions systems are enabled at this location.  The administrator is also able to customize
		the return address and subject lines for these e-mails.
		
	12.  Interface Settings
	
		The Interface Settings screen allows some customization of the DMS interface.
		 
		Enable Administration View:  Checking this box will grant  administrators the ability to traverse the DMS directory structure
		from the main screen.  This option has been added primarily in order increase system performance when an administrator is using
		a large document repository.
		
		Default User Interface:  The drop-down box changes the user interface on the main screen.  On systems with a large number of 
			documents, it may be advantages to set this to Single Directory mode in order to increase system performance.
		
		DMS Page Title:  Displays a title on many of the web pages within the DMS.
		
		Documents Displayed per Page:  Limits the number of documents to be displayed per page.  When this limit is reached, 
			users will be able to page through the system to view other documents.
		
		Display Document Template Name:  If a document is created using a document template, the name of the template is displayed in 
			parenthesis after the document name.
			
		Display Lifecycle Stage:  If a document is within a lifecycle, the name of the lifecycle stage is displayed.
		
		The remaining options pertain to the Style-Sheet Classes utilized on the various PHP pages within the DMS.
		These options have been added in order to easily change the DMS to look properly on the system that I originally
		developed it for.

	13. Lifecycles
	
		The lifeycle system can be enabled here.  
		
		By setting the "Keep Final Revision Only" option, when a document reaches the end of a lifecycle, only the last version 
		will be kept, all other versions will be automatically deleted.  
		
		The "Keep Lifecycle Stage Name" option will force the last lifecycle stage name to be displayed along with the document if
		the "Display Lifecycle Stage" option is selected under "Interface Settings."
		
		In the future, the "Move Documents Into Alphabetical Sub-Folders" option will automatically move documents into alphabetical 
		subfolders when they reach the final stage of the lifecycle.
		
	14. Permissions System
	
		The first option, Inherit Permissions from Parent Directory, will automatically apply the permissions from the parent directory
		to new documents.  The second option, Enable Permissions Caching System, will enable a system that caches permission in order to
		optimize the DMS module.  This option should only be selected if performance degredation is noticed as more documents are added to
		the system.
	
	15. Routing
		
		The first option allows an administrator to enable the document routing system.  By selecting the "Automatic Inbox Creation" option,
		the DMS module will automatically create user inboxes as users utilize the system.  The final three options are to be configured
		if users are to be e-mailed when documents are routed to their inboxes.
	
		 
	16. Search Configuration
	
		The first option, Search Limit, limits the number of documents found when a search is requested.
	
		In order to enable Full Text Search, download and install SWISH-E Enhanced from 
		http://www.swish-e.org.  Once installed, the path to the binary will have to be added in the SWISH-E
		path field.  Do not include the filename.  i.e. /usr/local/bin  Then, ensure that the "Enable" check
		box is checked.  You will also have to schedule the SWISH-E program to execute at regular intervals to
		re-build the index...preferably in the evening.  There are two files, _binfilter.sh and swish-e.conf
		that are created in the repository directory when you click on the "Write Configuration Files" button.  This
		button is only available after the repository is configured.  Use these files with the command 
		"swish-e -c swish-e.conf" in order to build/re-build the full text index.  Please read the instructions
		with SWISH-E for more information.  If full-text searching is enabled, you will now be presented with
		a full text search when you click on the "Search" option from the main screen.  You can still do a 
		properties search by clicking on the appropriate command button from the Search screen.
		
	17. Update Manager
		See Section C. for details.

C.  Upgrading
	
	WARNING:  Do not attempt to use the upgrade manager from an install prior to version 0.93.  If you have an
		already existing install of a version prior to version 0.93, you will have to manually upgrade the system
		to version 0.94 by installing the new DMS module and manually adjusting the database tables as necessary. 
	
	WARNING:  The update manager will make changes to the database tables.  It is highly recommended that you backup
		your database before clicking on the clicking on the Update button in the DMS Update Manager.
			
	Instructions for Windows servers, for upgrading from a version prior to version 0.98, and/or users who prefer to
		manually install the module files:
	
	1.  Install the new DMS module in the modules section of your Xoops installation.
	2.  From the Xoops Modules Administration screen, click on the update button in the Action column.  On
		the next screen, click on the Update button.
	3.  Go to the DMS Update Manager and click on the Update button in the database section.  The DMS Update
		Manager will add or remove all necessary tables, columns, and entries to bring the DMS database tables
		up-to-date.
		
	Instructions for *nix servers.
	
	Note:  Do not use this set of instructions for upgrading from version 0.97 to version 0.98.  Since this feature
		was added in version 0.98, these instructions will be relevant when upgrading from 0.98 to 0.99 and beyond.
	
	Note:  Items 1 and 3, below are only required when configuring the Update Manager.
		
	1.  If not already done, create a folder in the DMS to store your new module images.
	2.  Import the new release into the folder you created above.
	3.  If the DMS Update Manager/ Module section has not been configured, from the DMS Update Manager,
		click the configure button and set the Object ID of the folder created in item 1, above.  This step is similar
		to configuring the Document Templates.  If this section has been configured, the select button will appear next
		to the "Object ID of Next Release" input box.
	4.  Click on the Select button button, next to the "Object ID of Next Release" input box and select the new DMS
		release that you uploaded in step 2, above.  Optionally, if you know the object id of the new release, you can
		type it in in the input box instead of clicking the Select button.
	5.  Click on the Install button and click OK when the "Install New Module" prompt appears.
	6.  From the Xoops Module Administration screen, click on the update button in the Action column.  On 
		the next screen, click on the Update button.
	7.  Click on the Update button, in the database section, of the DMS Update Manager to bring the database up-to-date.
		
D.  Lifecycles

	The lifecycle system will allow you to apply a lifecycle to a document.  When that document is promoted it will be
	moved to another step (folder) in the lifecycle.  For example, you could have a folder for Draft Documents, Draft
	Approvals, and Published Documents.  Applying a lifecycle to a document could move that document into the Draft
	Documents folder.  Someone could then make changes to the document and promote it to the Draft Approvals folder where
	after review, the document would finally be promoted and moved to the Published Documents folder.
	
	To create a lifecycle, do the following:
	1.  Logon as an administrator
	2.  Click on the Lifecycles link in the upper right-hand corner.
	3.  Click on the New button...a new lifecycle will be created.
	4.  Click on the edit link of the new lifecycle.
	5.  Provide a name and description of the new lifecycle and click Update.
	6.  Click on the New option to add a stage to the lifecycle.
	7.  Click on the Edit link for the new stage, change the stage number, select a destination folder, and click Update.
	8.  Click Exit.
	9.  Repeat steps 6, 7, and 8 to add additional stages.
	
	When a lifecycle is created you can now click on the lifecycle button, from a document options screen, to select a
	lifecycle.  When you select a lifecycle and click on the Apply button, the document will be moved to the first stage
	(folder) of the lifecycle.  Once a document is in a lifecycle, it can be promoted by clicking on the Promote link
	from the main screen.
	 
	
E.	Troubleshooting
	
	1.  E-mail failure
	
		If the DMS is configured to send e-mail (see the configuration, above) and e-mail is not being sent check the 
		following:
		
			1.  Ensure that e-mail can be sent from the web server.
			2.  Increase the memory_limit in the php.ini file.  This failure usually occurs with large attachments.
			
	2.  Unable to upload documents
	
		This is almost always either a permissions or configuration problem.  Ensure that Apache has read/write
		permissions to the repository and check the php.ini file to ensure that uploads are allowed and the upload size
		is configured appropriately.  If you are still unable to upload documents, ensure that PHP is not running in safe mode.  
		If PHP is running in safe mode and you are unable to change this, try setting the "Document Storage Tuning" value to 0 in 
		the "Document Repository" section of the configuration.
		
	3.  Unable to upload large documents
	
		Increase the memory_limit in the php.ini file.
		

