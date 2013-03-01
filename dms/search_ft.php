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
include_once 'inc_search_summary.php';
//include XOOPS_ROOT_PATH.'/header.php';
include 'inc_pal_header.php';



$search_query = dms_get_var("txt_search_query");
if($search_query == FALSE) $search_query = dms_get_var("search_query");
if($search_query == FALSE) $search_query = "";

$last_query = dms_get_var("hdn_last_query");
if($last_query == FALSE) $last_query = dms_get_var("last_query");
//if($last_query != FALSE) $last_query = stripslashes($last_query);

$page = dms_get_var("page");

//if($page == FALSE) print "<BR>FALSE";
//print "<BR>LQ=".$last_query;
//print "<BR>SQ=".$search_query;

if( ( ($last_query != $search_query) || (strlen($search_query) < 2) ) && ($page == FALSE) ) dms_clear_search_results();





function dms_clear_search_results()
	{
//print "<BR>CLEAR<BR>";
	$_SESSION['dms_search_results'] = "";
	}

function dms_display_search_results($page = 1)
	{
	global $dms_config, $search_query, $last_query;

	$table_header_flag = FALSE; 
	$disp_nff_flag = TRUE;

	$results_per_page = $dms_config['search_results_per_page'];
	$total_number_pages = ceil($_SESSION['dms_search_results']['total_results']/$results_per_page);

	$start = $results_per_page * $page - $results_per_page;
	$end = $results_per_page * $page - 1;
	if($end >= $_SESSION['dms_search_results']['total_results']) $end = $_SESSION['dms_search_results']['total_results'] - 1;

//print "<BR>S:  ".$start;
//print "<BR>E:  ".$end;

	for($i = $start; $i <= $end; $i++)
		{
		$disp_nff_flag = FALSE;

		if ($table_header_flag == FALSE)
			{
			$table_header_flag = TRUE;

			if($_SESSION['dms_search_results']['total_results'] > $results_per_page)
				{
				print "  <tr>\r";
				print "    <td colspan='2'></td>\r";
				print "    <td colspan='2' align='right'>";

				print "Page:&nbsp;&nbsp;";

				for($p_index = 1; $p_index <= $total_number_pages; $p_index++)
					{
					if($p_index == $page)
						{
						print "&nbsp;<b>".$p_index."</b>&nbsp;";
						}
					else
						{
						print "&nbsp;<a href='?page=".$p_index."&search_query=".$search_query."&last_query=".$last_query."'>".$p_index."</a>&nbsp;";
						}
					}

				print "    </td>\r";
				print "  </tr>\r";
				}


			print "  <tr>\r";
			print "    <td width='75%' colspan='2' align='left'><b>Document(s):</b></td>\r";
			print "    <td align='left'><b>Version:</b></td>\r";
			print "    <td align='left'><b>Relevance:</b></td>\r";
			print "  </tr>\r";
			}

		print "  <tr>\r";
		print "    <td align='left' colspan='2'>  <!-- ".$_SESSION['dms_search_results']['obj_id'][$i]." -->\r";
		print "      <a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$_SESSION['dms_search_results']['obj_id'][$i]."\"))'>".$_SESSION['dms_search_results']['obj_name'][$i]."</a>\r";
		print "    </td>\r";
		
		print "    <td align='left'>\r";
		print "   ".$_SESSION['dms_search_results']['version'][$i];
		print "    </td>\r";
		
		print "    <td align='left'>\r";
		print "   ".$_SESSION['dms_search_results']['relevance'][$i]."%";
		print "    </td>\r";
		
		print "  </tr>\r";

		if($dms_config['search_summary_flag'] == 1)
			{
			print "  <tr>\r";
			print "    <td width='5%'>&nbsp;</td>\r";
			print "    <td align='left'>";

			if(strlen($_SESSION['dms_search_results']['summary'][$i]) < 2);
				{
				$_SESSION['dms_search_results']['summary'][$i] 
					= dms_search_summary($_SESSION['dms_search_results']['path_and_file'][$i],
					$_SESSION['dms_search_results']['summary_query'][$i]);
				}

			print $_SESSION['dms_search_results']['summary'][$i];

			print "    </td>\r";
			print "    <td colspan='2'></td>\r";
			print "  </tr>\r";
			}
		}

	if ($disp_nff_flag == TRUE) print "<tr><td colspan='2'><b>No files have been found that match your query.</b><br></td></tr>"; 
	}

function dms_store_search_results($obj_id,$obj_name,$version,$relevance,$full_path_and_file,$summary_query)
	{
//print "<BR>STORE<BR>";

	if(isset($_SESSION['dms_search_results']['total_results']))
		{
		$index = $_SESSION['dms_search_results']['total_results'];
		}
	else
		{
		$_SESSION['dms_search_results']['total_results'] = 0;
		$index = 0;
		}

	$_SESSION['dms_search_results']['obj_id'][$index] = $obj_id;
	$_SESSION['dms_search_results']['obj_name'][$index] = $obj_name;
	$_SESSION['dms_search_results']['version'][$index] = $version;
	$_SESSION['dms_search_results']['relevance'][$index] = $relevance;
	$_SESSION['dms_search_results']['summary'][$index] = "";
	$_SESSION['dms_search_results']['path_and_file'][$index] = $full_path_and_file;
	$_SESSION['dms_search_results']['summary_query'][$index] = $summary_query;

	$_SESSION['dms_search_results']['total_results']++;
	}


print "<table width='100%'>\r";
  
print "  <tr>\r";
  
// Content
print "    <td valign='top'>\r";
print "      <table width='100%'>\r";
display_dms_header(1);
print "      </table>\r";
  
print "      <table width='100%'>\r";
print "      <form name='frm_ft_search' method='post' action='search_ft.php'>\r"; 
print "        <tr>\r";
print "          <td width='100%' ".$dms_config['class_content']." align='left'>\r";
print "            <b>Full Text Search:</b>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <tr>\r";
print "          <td align='left'>\r";

print "            <br>\r";
//begin search box
//print "            <form name='frm_ft_search' method='post' action='search_ft.php'>\r"; 
$search_query = stripslashes($search_query);
$last_query = stripslashes($last_query);
print "              <input type='text' name='txt_search_query' value='".$search_query."' size='60' maxlength='250' ".$dms_config['class_content'].">\r";
print "              <BR><BR>\r";  
print "              <input type='submit' name='btn_search' value='Search' ".$dms_config['class_content'].">\r"; 
print "              <input type='button' name='btn_exit' value='Exit' onclick='location=\"index.php\"'>\r";
print "              <input type='hidden' name='hdn_last_query' value='".$search_query."'>";
//print "            </form>\r"; 
// end search box 

print "<BR><BR>\r";

print "            <table>\r";

//print "<BR>".$page;
if( ( (strlen($search_query) > 2) && ($last_query != $search_query) ) && ($page == FALSE) ) 
	{
//print "<BR>SEARCH!";
	// Get the first word in $search_query and use it for the $summary_query.
	$summary_query = str_replace("\""," ",$search_query);
	$summary_query = trim($summary_query);
	$summary_query_e = explode(" ",$summary_query);
	$summary_query = trim($summary_query_e[0]);
	$summary_query = rtrim($summary_query,"*");

//print "<BR>SQ:  ".$summary_query;

	// Get the location of the document repository (the index files are located in the root)
	$repository_root = $dms_config['doc_path'];
	$repository_root_strlen = strlen($dms_config['doc_path']);

	// Get the location of the SWISH-E executable
	$swish_e_path = $dms_config['swish-e_path'];

	// Get the search_limit to limit the search to X number of entries
	$search_limit = $dms_config['search_limit'];
//print "<BR>Query:  ".$search_query;
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
		
	//$table_header_flag = FALSE; 
	//$disp_nff_flag = TRUE;

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

			$full_path_and_file = $result_url;
			$result_url = trim(substr( $result_url, ($repository_root_strlen - 1), strlen($result_url) ) );
			$file_path = strright($result_url, (strlen($result_url)-2) );

			$query    = "SELECT * ";
			$query   .= "FROM ".$dmsdb->prefix("dms_object_versions")." ";
			$query   .= "WHERE file_path='".$file_path."'";  
			$ver_info = $dmsdb->query($query,'ROW');

			$query    = "SELECT * ";
			$query   .= "FROM ".$dmsdb->prefix("dms_objects")." ";
			$query   .= "WHERE obj_id='".$ver_info->obj_id."'";  
			$obj_info = $dmsdb->query($query,'ROW');
			if ($obj_info->obj_id > 0)
				{
				// Permissions required to view this object:
				//  BROWSE, READONLY, EDIT, OWNER
				if($dms_admin_flag == 0) $perms_level = dms_perms_level($obj_info->obj_id);
				else $perms_level = 4;

				if ($obj_info->obj_status < 2)
					{ 
					if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
						{
						$misc_text = $obj_info->misc_text;
						if (strlen($misc_text) >0)
							{
							$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
							}
						else $misc_text = "";
							
						$store_obj_id = $obj_info->obj_id;
						$store_obj_name = $obj_info->obj_name.$misc_text;
						$store_version_num = $ver_info->major_version.".".$ver_info->minor_version."".$ver_info->sub_minor_version;
						$store_relevance = $relevance;
						dms_store_search_results($store_obj_id,$store_obj_name,$store_version_num,$relevance,$full_path_and_file,$summary_query);
						}
					}
				}
			} 
		} 
	pclose($pp);		/* close the shell pipe */ 
	}

if($page == FALSE) $page = 1;
if(strlen($search_query) > 2) dms_display_search_results($page);

print "            </table>\r";
print "          </td>\r";
print "        </tr>\r";
print "      </form>\r";
print "      </table>\r";

print "    </td>\r";
print "  </tr>\r";
print "</table>\r";

include 'inc_pal_footer.php';

?> 
 
 
