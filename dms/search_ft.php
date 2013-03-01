<? 
// This code has been obtained from http://www.zend.com/codex.php?id=763&single=1
// and modified for use in this DMS.

/* 
php4swish-e 1.1, a web search interface for the swish-e search engine. 
swish-e is a popular open-source search engine that runs on many platforms. 
More information on swish-e is available at swish-e.org. 
This code has been thoroughly tested and is ready for production 
 on any UNIX or Linux server that has the swish-e search engine installed. 
You are free to modify this code as you see fit. 
You must specify the path of swish-e ($swish) and 
 the location of the search index file ($search_index). 
You will also need to change the default index file served if it is not index.php. 
If you want the meta description information to display completely, 
 be sure the <meta description... information is on *one* line for each web page. 
If you wish to allow external search forms to call this script, be sure to set the 
 form's action attribute to whatever you name this file. 
Suggestions for enhancements are welcome. 
*/ 
 
 
include '../../mainfile.php';

include_once 'inc_dms_functions.php';
include XOOPS_ROOT_PATH.'/header.php';

 
//$search_query = $HTTP_POST_VARS['txt_search_query'];
$search_query = dms_get_var("txt_search_query");

if ($dms_admin_flag == 1) 
  {
  $admin_flag = 1;
  }
else 
  {
  $admin_flag = 0;
  }

print "<table width='100%'>\r";
  
//  display_dms_header();
  
print "  <tr>\r";
  
// Content
print "    <td valign='top'>\r";
print "      <table width='100%'>\r";
display_dms_header(1);
print "      </table>\r";
  
print "      <BR>\r";



print "<table width='100%'>\r";

print "  <tr>\r";
print "    <td width='100%' ".$dms_config['class_subheader'].">\r";
print "      Full Text Search\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr>\r";
print "    <td align='left'>\r";

print "      <br>\r";
//begin search box
print "      <form name='frm_ft_search' method='post' action='search_ft.php'>\r"; 
$search_query = stripslashes($search_query); 
print "        <input type='text' name='txt_search_query' value='".$search_query."' size='60' maxlength='250' ".$dms_config['class_content'].">\r";
print "        <BR><BR>\r";  
print "        <input type='submit' name='btn_search' value='Search' ".$dms_config['class_content'].">\r"; 
print "        <input type='button' name='btn_exit' value='Exit' onclick='location=\"index.php\"'>\r";
print "      </form>\r"; 
// end search box 

if(strlen($search_query) > 2) 
	{
	// Get the location of the document repository (the index files are located in the root)
	$repository_root = $dms_config['doc_path'];
	$repository_root_strlen = strlen($dms_config['doc_path']);

	// Get the location of the SWISH-E executable
	$swish_e_path = $dms_config['swish-e_path'];

	// Get the search_limit to limit the search to X number of entries
	$search_limit = $dms_config['search_limit'];

	$search_query = EscapeShellCmd($search_query);                          // escape potentially malicious shell commands 
	$search_query = stripslashes($search_query);                            // remove backslashes from search query 
	$search_query = ereg_replace('("|\')', '', $search_query);              // remove quotes from search query  
	$swish = $swish_e_path."/swish-e";                                      // path of swish-e command
	$search_index = $repository_root."/index.swish-e";                      // path of swish-e index file 
	$search_params = "-H1 -m" . $search_limit;                              // Additional search parameters

	$pp = popen("$swish -w $search_query -f $search_index $search_params", "r") or die("The search request generated an error...Please try again."); 

//print "$swish -w $search_query -f $search_index $search_params<BR>";

	$line_cnt = 1; 
	while ($nline = @fgets($pp, 1024))   /* loop through each line of the pipe result (i.e. swish-e output) to find hit number */ 
		{
		if ($line_cnt == 4) 
			{ 
			$num_line = $nline; 
			break;			/* grab the 4th line, which contains the number of hits returned */ 
			} 
		$line_cnt++; 
		} 

	$num_results = ereg_replace('# Number of hits: ','',$num_line);		/* strip out all but the number of hits */ 

	/*
	if ($search_query) 
		{ 
		if ($num_results > 0) 
			{ 
			if ($num_results == 1) 
				{
				$result_case = "result";
				}
			else
				{
				$result_case = "results";
				} 
			}
		}
	*/
		
	$table_header_flag = FALSE; 
	$disp_nff_flag = TRUE;

	print "<table>\r";

	while ($line = @fgets($pp, 4096))
		{
		/* loop through each line of the pipe result (i.e. swish-e output) */ 
		if (preg_match("/^(\d+)\s+(\S+)\s+\"(.*)\"\s+(\d+)/", $line))
			{
			/* Skip commented-out lines and the last line */ 
			$line = explode('"', $line);                                   /* split the string into an array by quotation marks */ 
			$line[1] = ereg_replace("[[:blank:]]", "%%", $line[1]);        /* replace every space with %% for the phrase in quotation marks */ 
			$line = implode('"', $line);                                   /* collapse the array into a string */ 
			$line = ereg_replace("[[:blank:]]", "\t", $line);              /* replace every space with a tab */ 
			list ( $relevance, $result_url, $result_title, $file_size ) = explode("\t", $line);  /* split the line into an array by tabs; assign variable names to each column */ 
			$relevance = $relevance/10;                                    /* format relevance as a percentage for search results */ 
			//$result_title = ereg_replace('%%',' ', $result_title);         /* replace every %% with a space */ 
			//$result_title = ereg_replace('"','', $result_title);           /* strip out the quotation marks */ 
			//$url = parse_url($result_url);                                 /* split the URL into an array of its components */ 
			//$link = $url["path"];                                          /* assign the web link to the path component to return a relative URL */ 
			//if (preg_match("/\/$/", $link)) { $link = "$link" . "index.php"; }/* if the URL ends in "/", we need to append "index.php" (if index.php is the DirectoryIndex file) to grab the appropriate description for the index page--YOU MAY NEED TO CHANGE THIS */ 
			//$page_requested = "$DOCUMENT_ROOT" . "$link";                  /* return the full path of the file on the web server */ 
			//$description = "grep -i description $page_requested";          /* return the meta description information */ 
			//$description = ereg_replace('<meta.name="description".content="', '', $description);  /* strip out the tag open */ 
			//$description = ereg_replace('\">', '', $description);          /* strip out the tag close */ 
			//if ($url["query"]) /* if the URL contains a query string, append the URL with that query string */ 
			//	{
			//	$link = $link . "?" . $url["query"];
			//	}  

			$result_url = trim(substr( $result_url, ($repository_root_strlen - 1), strlen($result_url) ) );

			$query    = "SELECT * ";
			$query   .= "FROM ".$dmsdb->prefix("dms_object_versions")." ";
			$query   .= "WHERE file_path='".strright($result_url, (strlen($result_url)-2) )."'";  
			$ver_info = $dmsdb->query($query,'ROW');

			$query    = "SELECT * ";
			$query   .= "FROM ".$dmsdb->prefix("dms_objects")." ";
			$query   .= "WHERE obj_id='".$ver_info->obj_id."'";  
			$obj_info = $dmsdb->query($query,'ROW');
			if ($obj_info->obj_id > 0)
				{
				// Permissions required to view this object:
				//  BROWSE, READONLY, EDIT, OWNER
				if($admin_flag == 0) $perms_level = dms_perms_level($obj_info->obj_id);
				else $perms_level = 4;

				if ($obj_info->obj_status < 2)
					{ 
					if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
						{
						$disp_nff_flag = FALSE;

						if ($table_header_flag == FALSE)
							{
							$table_header_flag = TRUE;
							print "  <tr>\r";
							print "    <td width='75%'><b>Document(s):</b></td>\r";
							print "    <td><b>Version:</b></td>\r";
							print "    <td><b>Relevance:</b></td>\r";
							print "  </tr>\r";
							}

						$misc_text = $obj_info->misc_text;
						if (strlen($misc_text) >0)
							{
							$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
							}
						else $misc_text = "";
							
						print "  <tr>\r";
						print "    <td align='left'>  <!-- ".$obj_info->obj_id." -->\r";
						print "      <a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$obj_info->obj_id."\"))'>".$obj_info->obj_name.$misc_text."</a>\r";
						print "    </td>\r";
						
						print "    <td>\r";
						print "   ".$ver_info->major_version.".".$ver_info->minor_version."".$ver_info->sub_minor_version;
						print "    </td>\r";
						
						print "    <td>\r";
						print "   ".$relevance."%";
						print "    </td>\r";
						
						print "  </tr>\r";
						}
					}
				}
			} 
		} 

	if ($disp_nff_flag == TRUE) print "<tr><td colspan='2'><b>No files have been found that match your query.</b><br></td></tr>"; 

	print "</table>\r";

	pclose($pp);		/* close the shell pipe */ 
	}

print "          </td>\r";
print "        </tr>\r";
print "      </table>\r";


print "    </td>\r";
print "  </tr>\r";
print "</table>\r";
	 
include_once XOOPS_ROOT_PATH.'/footer.php';
?> 
 
 
