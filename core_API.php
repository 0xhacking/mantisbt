<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	###########################################################################
	### CONFIGURATION VARIABLES                                             ###
	###########################################################################

	require( "config_inc.php" );

	###########################################################################
	### FUNCTIONS                                                           ###
	###########################################################################

	####################
	# MySQL
	####################
	#--------------------
	# connect to database
	function db_mysql_connect( 	$p_hostname, $p_username="root", $p_password="",
								$p_database, $p_port=3306 ) {

		$t_result = mysql_connect(  $p_hostname.":".$p_port,
									$p_username, $p_password );
		$t_result = mysql_select_db( $p_database );
	}
	#--------------------
	# execute query, requires connection to be opened,
	# goes to error page if error occurs
	# Use this when you don't want to handler an error yourself
	function db_mysql_query( $p_query ) {
		global $g_mysql_error_page;

		$t_result = mysql_query( $p_query );
		if ( !$t_result ) {
			header( "Location: $g_mysql_error_page?f_message=$p_query" );
			exit;
		}
		else {
			return $t_result;
		}
	}
	#--------------------
	function db_mysql_close() {
		$t_result = mysql_close();
	}
	#--------------------
	function db_mysql_error() {
		$t_error = mysql_errno().":".mysql_error();
	}
	#--------------------
	####################
	# Core HTML API
	####################
	#--------------------
	function print_html_top() {
		PRINT "<html>";
	}
	#--------------------
	function print_head_top() {
	   PRINT "<head>";
	}
	#--------------------
	function print_title( $p_title ) {
	   PRINT "<title>$p_title</title>";
	}
	#--------------------
	function print_css( $p_css="" ) {
		if ( !empty($p_css )) {
			include( "$p_css" );
		}
	}
	#--------------------
	function print_meta_redirect( $p_url, $p_time=0 ) {
	   PRINT "<meta http-equiv=\"Refresh\" content=\"$p_time;URL=$p_url\">";
	}
	#--------------------
	function print_head_bottom() {
	   PRINT "</head>";
	}
	#--------------------
	function print_body_top() {
		PRINT "<body>";
	}
	#--------------------
	function print_header( $p_title="Mantis" ) {
		PRINT "<div align=center><h3><font face=Verdana>$p_title</font></h3></div>";
	}
	#--------------------
	function print_footer() {
		global 	$g_string_cookie_val, $g_webmaster_email, $g_show_source;
		global  $DOCUMENT_ROOT, $PHP_SELF;

		print_source_link();

		PRINT "<hr size=1>";
		print_mantis_version();
		PRINT "<address><font size=-1>Copyright (c) 2000</font></address>";
		PRINT "<address><font size=-1><a href=\"mailto:$g_webmaster_email\">$g_webmaster_email</a></font></address>";
	}
	#--------------------
	function print_body_bottom() {
		PRINT "</body>";
	}
	#--------------------
	function print_html_bottom() {
		PRINT "<html>";
	}
	#--------------------
	function print_menu( $p_menu_file="" ) {
		global $g_primary_border_color, $g_primary_color_light;

		PRINT "<table width=100% bgcolor=$g_primary_border_color>";
		PRINT "<tr align=center height=20>";
			PRINT "<td align=center bgcolor=$g_primary_color_light>";
				include( $p_menu_file );
			PRINT "</td>";
		PRINT "</tr>";
		PRINT "</table>";
	}
	#--------------------
	### checks to see whether we need to be displaying the source link
	function print_source_link() {
		global $g_show_source, $g_show_source_page, $PHP_SELF;

		if ( $g_show_source==1 ) {
			if ( access_level_check_greater_or_equal( "administrator" ) ) {
				PRINT "<p>";
				PRINT "<div align=center>";
				PRINT "<a href=\"$g_show_source_page?f_url=$PHP_SELF\">Show Source</a>";
				PRINT "</div>";
			}
		}
		else if ( $g_show_source==2 ) {
			PRINT "<p>";
			PRINT "<div align=center>";
			PRINT "<a href=\"$g_show_source_page?f_url=$PHP_SELF\">Show Source</a>";
			PRINT "</div>";
		}
	}
	#--------------------
	### checks to see whether we need to be displaying the source link
	function print_mantis_version() {
		global $g_mantis_version, $g_show_version;

		if ( $g_show_version==1 ) {
			PRINT "<i>Mantis version $g_mantis_version</i>";
		}
	}
	#--------------------
	####################
	# String printing API
	####################
	#--------------------
	function get_enum_string( $p_field_name ) {
		global $g_mantis_bug_table;

		$query = "SHOW FIELDS
				FROM $g_mantis_bug_table";
		$result = db_mysql_query( $query );
		$entry_count = mysql_num_rows( $result );
		for ($i=0;$i<$entry_count;$i++) {
			$row = mysql_fetch_array( $result );
	    	$t_type = stripslashes($row["Type"]);
	    	$t_field = $row["Field"];
	    	if ( $t_field==$p_field_name ) {
		    	return substr( $t_type, 5, strlen($t_type)-6);
		    }
	    } ### end for
	}
	#--------------------
	# returns the number of items in a list
	# default delimiter is a ,
	function get_list_item_count( $t_enum_string, $p_delim_char="," ) {
		return count(explode($p_delim_char,$t_enum_string));
	}
	#--------------------
	### Used for update pages
	function print_categories( $p_category="" ) {
		global $g_mantis_bug_table;

		$t_category_string = get_enum_string( "category" );
	    $t_str = $t_category_string.",";
		$cat_count = get_list_item_count($t_str)-1;
		for ($i=0;$i<$cat_count;$i++) {
			$t_s = substr( $t_str, 1, strpos($t_str, ",")-2 );
			$t_str = substr( $t_str, strpos($t_str, ",")+1, strlen($t_str) );
			if ( $p_category==$t_s ) {
				PRINT "<option value=\"$t_s\" SELECTED>$t_s";
			}
			else {
				PRINT "<option value=\"$t_s\">$t_s";
			}
		} ### end for
	}
	#--------------------
	### Used for update pages
	function print_list( $p_list,  $p_item="" ) {
		global $g_mantis_bug_table;

		$t_category_string = get_enum_string( $p_list );
	    $t_str = $t_category_string.",";
		$entry_count = get_list_item_count($t_str)-1;
		for ($i=0;$i<$entry_count;$i++) {
			$t_s = substr( $t_str, 1, strpos($t_str, ",")-2 );
			$t_str = substr( $t_str, strpos($t_str, ",")+1, strlen($t_str) );
			if ( $p_item==$t_s ) {
				PRINT "<option value=\"$t_s\" SELECTED>$t_s";
			}
			else {
				PRINT "<option value=\"$t_s\">$t_s";
			}
		} ### end for
	}
	#--------------------
	### Used for update pages
	function print_list2( $p_list,  $p_item="" ) {
		global $g_mantis_bug_table;

	    $t_str = $p_list.",";
		$entry_count = get_list_item_count( $t_str )-1;
		for ($i=0;$i<$entry_count;$i++) {
			$t_s = substr( $t_str, 0, strpos($t_str, ",") );
			$t_str = substr( $t_str, strpos($t_str, ",")+1, strlen($t_str) );
			if ( $p_item==$t_s ) {
				PRINT "<option value=\"$t_s\" SELECTED>$t_s";
			}
			else {
				PRINT "<option value=\"$t_s\">$t_s";
			}
		} ### end for
	}
	#--------------------
	### Used in summary reports
	function print_bug_enum_summary( $p_enum, $p_status="" ) {
		global $g_mantis_bug_table, $g_primary_color_light, $g_primary_color_dark;

		$t_enum_string = get_enum_string( $p_enum );
	    $t_str = $t_enum_string.",";
		$enum_count = get_list_item_count($t_str)-1;
		for ($i=0;$i<$enum_count;$i++) {
			$t_s = substr( $t_str, 1, strpos($t_str, ",")-2 );
			$t_str = substr( $t_str, strpos($t_str, ",")+1, strlen($t_str) );

			$query = "SELECT COUNT(id)
					FROM $g_mantis_bug_table
					WHERE $p_enum='$t_s'";
			if ( !empty( $p_status ) ) {
				if ( $p_status=="open" ) {
					$query = $query." AND status<>'resolved'";
				}
				else if ( $p_status=="open" ) {
					$query = $query." AND status='resolved'";
				}
				else {
					$query = $query." AND status='$p_status'";
				}
			}
			$result = mysql_query( $query );
			$t_enum_count = mysql_result( $result, 0 );

			### alternate row colors
			if ( $i % 2 == 1) {
				$bgcolor=$g_primary_color_light;
			}
			else {
				$bgcolor=$g_primary_color_dark;
			}

			PRINT "<tr align=center bgcolor=$bgcolor>";
				PRINT "<td width=50%>";
					echo $t_s;
				PRINT "</td>";
				PRINT "<td width=50%>";
					echo $t_enum_count;
				PRINT "</td>";
			PRINT "</tr>";
		} ### end for
	}
	#--------------------
	### Used in summary reports
	function print_bug_date_summary( $p_date_array ) {
		global $g_mantis_bug_table, $g_primary_color_light, $g_primary_color_dark;

		$arr_count = count( $p_date_array );
		for ($i=0;$i<$arr_count;$i++) {
			$t_enum_count = get_bug_count_by_date( $p_date_array[$i] );

			### alternate row colors
			if ( $i % 2 == 1) {
				$bgcolor=$g_primary_color_light;
			}
			else {
				$bgcolor=$g_primary_color_dark;
			}

			PRINT "<tr align=center bgcolor=$bgcolor>";
				PRINT "<td width=50%>";
					echo $p_date_array[$i];
				PRINT "</td>";
				PRINT "<td width=50%>";
					echo $t_enum_count;
				PRINT "</td>";
			PRINT "</tr>";
		} ### end for
	}
	#--------------------
	# prints the profiles given the user id
	function print_profiles( $p_id ) {
		global $g_mantis_user_profile_table;

		### Get profiles
		$query = "SELECT id, platform, os, os_build, default_profile
			FROM $g_mantis_user_profile_table
			WHERE user_id='$p_id'
			ORDER BY id DESC";
	    $result = db_mysql_query( $query );
	    $profile_count = mysql_num_rows( $result );

		PRINT "<option value=\"\">";
		for ($i=0;$i<$profile_count;$i++) {
			### prefix data with v_
			$row = mysql_fetch_array( $result );
			extract( $row, EXTR_PREFIX_ALL, "v" );
			$v_platform	= string_unsafe( $v_platform );
			$v_os		= string_unsafe( $v_os );
			$v_os_build	= string_unsafe( $v_os_build );

			if ( $v_default_profile=="on" ) {
				PRINT "<option value=\"$v_id\" SELECTED>$v_platform $v_os $v_os_build";
			}
			else {
				PRINT "<option value=\"$v_id\">$v_platform $v_os $v_os_build";
			}
		}
	}
	#--------------------
	####################
	# Cookie API
	####################
	#--------------------
	### checks to see that a user is logged in
	### if the user is and the account is enabled then let them pass
	### otherwise redirect them to the login page
	function login_cookie_check( $p_redirect_url="" ) {
		global 	$g_string_cookie_val, $g_login_page,
				$g_hostname, $g_db_username, $g_db_password, $g_database_name,
				$g_mantis_user_table;

		### if logged in
		if ( isset( $g_string_cookie_val ) ) {

			db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

			### get user info
			$query = "SELECT enabled
					FROM $g_mantis_user_table
					WHERE cookie_string='$g_string_cookie_val'";
			$result = db_mysql_query( $query );
			$row = mysql_fetch_array( $result );
			if ( $row ) {
				$t_enabled = $row["enabled"];
			}

			### check for acess enabled
			if ( $t_enabled!="on" ) {
				header( "Location: $g_logout_page" );
			}

			### update last_visit date
			$query = "UPDATE $g_mantis_user_table
					SET last_visit=NOW()
					WHERE cookie_string='$g_string_cookie_val'";
			$result = mysql_query( $query );
			db_mysql_close();

			### go to redirect
			if ( !empty( $p_redirect_url ) ) {
				header( "Location: $p_redirect_url" );
				exit;
			}
			### continue with current page
			else {
				return;
			}
		}
		### not logged in
		else {
			header( "Location: $g_login_page" );
			exit;
		}
	}
	#--------------------
	### checks to see if a returning user is valid
	### also sets the last time they visited
	### otherwise redirects to the login page
	function index_login_cookie_check( $p_redirect_url="" ) {
		global 	$g_string_cookie_val, $g_login_page, $g_last_access_cookie,
				$g_hostname, $g_db_username, $g_db_password, $g_database_name,
				$g_mantis_user_table;

		### if logged in
		if ( isset( $g_string_cookie_val ) ) {
			### set last visit cookie

			db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

			### get user info
			$query = "SELECT enabled
					FROM $g_mantis_user_table
					WHERE cookie_string='$g_string_cookie_val'";
			$result = db_mysql_query( $query );
			$row = mysql_fetch_array( $result );
			if ( $row ) {
				$t_enabled = $row["enabled"];
			}

			### check for acess enabled
			if ( $t_enabled!="on" ) {
				header( "Location: $g_logout_page" );
			}

			$query = "SELECT last_visit
					FROM $g_mantis_user_table
					WHERE cookie_string='$g_string_cookie_val'";
			$result = mysql_query( $query );
			$t_last_access = mysql_result( $result, "last_visit" );
			db_mysql_close();

			setcookie( $g_last_access_cookie, $t_last_access );

			### go to redirect
			if ( !empty( $p_redirect_url ) ) {
				header( "Location: $p_redirect_url" );
				exit;
			}
			### continue with current page
			else {
				return;
			}
		}
		### not logged in
		else {
			header( "Location: $g_login_page" );
			exit;
		}
	}
	#--------------------
	### Returns the id of the currently logged in user, otherwise 0
	function get_current_user_id() {
		global 	$g_string_cookie_val,
				$g_hostname, $g_db_username, $g_db_password, $g_database_name,
				$g_mantis_user_table;

		### if logged in
		if ( isset( $g_string_cookie_val ) ) {

			db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

			### get user info
			$query = "SELECT id
					FROM $g_mantis_user_table
					WHERE cookie_string='$g_string_cookie_val'";
			$result = db_mysql_query( $query );
			return mysql_result( $result, 0 );
		}
		else {
			return 0;
		}
	}
	#--------------------
	####################
	# Authentication API
	####################
	#--------------------
	function password_match( $p_test_password, $p_password ) {
		$salt = substr( $p_password, 0, 2 );
		if ( crypt( $p_test_password, $salt ) == $p_password ) {
			return true;
		}
		else {
			return false;
		}
	}
	#--------------------
	#####################
	# User Management API
	#####################
	#--------------------
	# This string is used to use as the login identified for the web cookie
	# It is not guarranteed to be unique but should be good enough
	# It is chopped to be 128 characters in length to fit into the database
	function create_cookie_string( $p_email ) {
		mt_srand( time() );
		$t_val = mt_rand( 1000, mt_getrandmax() ) + mt_rand( 1000, mt_getrandmax() );
		$t_string = $p_email.$t_val;
		$t_cookie_string = crypt( $t_string ).md5( time() );
		$t_cookie_string = $t_cookie_string.crypt( $t_string, $t_string ).md5( $t_string ).mt_rand( 1000, mt_getrandmax() );

		return substr( $t_cookie_string, 0, 128 );
	}
	#--------------------
	####################
	# Preferences API
	####################
	#--------------------
	### return a vlue of a table of the currently logged in user
	function get_user_value( $p_table_name, $p_table_field ) {
		global 	$g_hostname, $g_db_username, $g_db_password, $g_database_name;

		### get user id
		$u_id = get_current_user_id();

		if ( $u_id ) {
			db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
			$query = "SELECT $p_table_field
					FROM $p_table_name
					WHERE user_id='$u_id'";
			$result = db_mysql_query( $query );

			if ( mysql_num_rows( $result ) > 0 ) {
				return mysql_result( $result, 0 );
			}
			else {
				return "";
			}
		}
		else {
			return "";
		}
	}
	#--------------------
	####################
	# Date API
	####################
	#--------------------
	function days_old( $month, $day, $year ) {

	}
	#--------------------
	function sql_to_unix_time( $p_timeString ) {
		return mktime( substr( $p_timeString, 8, 2 ),
					   substr( $p_timeString, 10, 2 ),
					   substr( $p_timeString, 12, 2 ),
					   substr( $p_timeString, 4, 2 ),
					   substr( $p_timeString, 6, 2 ),
					   substr( $p_timeString, 0, 4 ) );
	}
	#--------------------
	# expects the paramter to be neutral in time length
	# automatically adds the -
	function get_bug_count_by_date( $p_time_length="day" ) {
		global $g_mantis_bug_table;

		$day = strtotime( "-".$p_time_length );
		$query = "SELECT COUNT(id)
				FROM $g_mantis_bug_table
				WHERE UNIX_TIMESTAMP(last_updated)>$day";
		$result = mysql_query( $query );
		return mysql_result( $result, 0 );
	}
	#--------------------
	####################
	# String API
	####################
	#--------------------
	function string_safe( $p_string ) {
		return addslashes( nl2br( $p_string ) );
	}
	#--------------------
	function string_unsafe( $p_string ) {
		return stripslashes( $p_string );
	}
	#--------------------
	function string_display( $p_string ) {
		return htmlspecialchars(stripslashes( $p_string ));
	}
	#--------------------
	function string_edit( $p_string ) {
		return str_replace( "<br>", " ",  stripslashes( $p_string ) );
	}
	#--------------------
	#####################
	# Access Control API
	#####################
	#--------------------
	# "administrator", "developer", "updater", "reporter", "viewer"
	#--------------------
	function access_level() {
		global $g_access_cookie;
		return $HTTP_COOKIE_VARS[$g_access_cookie];
	}
	#--------------------
	### This is used to order the access levels
	function access_level_value( $p_access_level ) {
		if ( $p_access_level == "administrator" ) {
			return 10;
		}
		else if ( $p_access_level == "developer" ) {
			return 7;
		}
		else if ( $p_access_level == "updater" ) {
			return 5;
		}
		else if ( $p_access_level == "reporter" ) {
			return 3;
		}
		else if ( $p_access_level == "viewer" ) {
			return 1;
		}

	}
	#--------------------
	function access_level_check_equal( $p_access_level ) {
		global $g_string_cookie_val, $g_mantis_user_table;

		if ( !isset($g_string_cookie_val) ) {
			return false;
		}

		$query = "SELECT access_level
				FROM $g_mantis_user_table
				WHERE cookie_string='$g_string_cookie_val'";
		$result = mysql_query( $query );
		$t_access_level = mysql_result( $result, "access_level" );

		if ( access_level_value( $t_access_level ) == access_level_value( $p_access_level ) ) {
			return true;
		}
		else {
			return false;
		}
	}
	#--------------------
	function access_level_check_greater_or_equal( $p_access_level ) {
		global $g_string_cookie_val, $g_mantis_user_table;

		if ( !isset($g_string_cookie_val) ) {
			return false;
		}

		$query = "SELECT access_level
				FROM $g_mantis_user_table
				WHERE cookie_string='$g_string_cookie_val'";
		$result = mysql_query( $query );
		$t_access_level = mysql_result( $result, "access_level" );

		if ( access_level_value( $t_access_level ) >= access_level_value( $p_access_level ) ) {
			return true;
		}
		else {
			return false;
		}
	}
	#--------------------

	###########################################################################
	### END                                                                 ###
	###########################################################################
?>
