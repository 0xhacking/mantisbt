<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( "core_API.php" ) ?>
<?php login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

	if ( ON == $g_hide_closed_default ) {
		$g_hide_closed_default = "on";
	} else {
		$g_hide_closed_default = "";
	}

	# check to see if the cookie does not exist
	if ( empty( $g_view_all_cookie_val ) ) {
		$t_settings_string = "v1#any#any#any#".$g_default_limit_view."#".
							$g_default_show_changed."#".$g_hide_closed_default.
							"#any#any#last_updated#DESC";
		setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length, $g_cookie_path );
		print_header_redirect( $g_view_all_bug_page."?f=2" );
	}

	# Check to see if new cookie is needed
	$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
	if ( $t_setting_arr[0] != "v1" ) {
		$t_settings_string = "v1#any#any#any#".$g_default_limit_view."#".
							$g_default_show_changed."#".$g_hide_closed_default.
							"#any#any#last_updated#DESC";
		setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length, $g_cookie_path );
		print_header_redirect( $g_view_all_bug_page."?f=1" );
	}

	if ( isset( $f_print ) ) {
		print_header_redirect( $g_print_all_bug_page );
	}

	if( !isset( $f_search_text ) ) {
		$f_search_text = false;
	}

	if ( !isset( $f_page_number ) ) {
		$f_page_number = 1;
	}

	if ( !isset( $f_per_page ) ) {
		$f_per_page = 4;
	}

	if ( !isset( $f_hide_closed ) ) {
		$f_hide_closed = "";
	}

	if ( isset( $f_save ) ) {
		if ( 1== $f_save ) {
			# We came here via the FILTER form button click
			# Save preferences
			$t_settings_string = "v1#".
								$f_show_category."#".
								$f_show_severity."#".
								$f_show_status."#".
								$f_per_page."#".
								$f_highlight_changed."#".
								$f_hide_closed."#".
								$f_user_id."#".
								$f_assign_id."#".
								$f_sort."#".
								$f_dir;
			setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length, $g_cookie_path );
		} else if ( 2 == $f_save ) {
			# We came here via clicking a sort link
			# Load pre-existing preferences
			$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
			$f_show_category 		= $t_setting_arr[1];
			$f_show_severity	 	= $t_setting_arr[2];
			$f_show_status 			= $t_setting_arr[3];
			$f_per_page 			= $t_setting_arr[4];
			$f_highlight_changed 	= $t_setting_arr[5];
			$f_hide_closed 			= $t_setting_arr[6];
			$f_user_id 				= $t_setting_arr[7];
			$f_assign_id 			= $t_setting_arr[8];

			if ( !isset( $f_sort ) ) {
				$f_sort		 			= $t_setting_arr[9];
			}
			if ( !isset( $f_dir ) ) {
				$f_dir		 			= $t_setting_arr[10];
			}
			# Save new preferences
			$t_settings_string = "v1#".
								$f_show_category."#".
								$f_show_severity."#".
								$f_show_status."#".
								$f_per_page."#".
								$f_highlight_changed."#".
								$f_hide_closed."#".
								$f_user_id."#".
								$f_assign_id."#".
								$f_sort."#".
								$f_dir;

			setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length, $g_cookie_path );
		}
	} else {
		# Load preferences
		$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
		$f_show_category 		= $t_setting_arr[1];
		$f_show_severity	 	= $t_setting_arr[2];
		$f_show_status 			= $t_setting_arr[3];
		$f_per_page 			= $t_setting_arr[4];
		$f_highlight_changed 	= $t_setting_arr[5];
		$f_hide_closed 			= $t_setting_arr[6];
		$f_user_id 				= $t_setting_arr[7];
		$f_assign_id 			= $t_setting_arr[8];
		$f_sort 				= $t_setting_arr[9];
		$f_dir		 			= $t_setting_arr[10];
	}

	# Limit reporters to only see their reported bugs
	if ( ON == $g_limit_reporters ) {
		if ( get_current_user_field( "access_level" ) <= REPORTER ) {
			$f_user_id = get_current_user_field( "id" );
		}
	}

	# date values
	/*if ( !isset( $f_start_month ) ) {
		$f_start_month = date( "m" );
	}
	if ( !isset( $f_end_month ) ) {
		$f_end_month = date( "m" );
	}
	if ( !isset( $f_start_day ) ) {
		$f_start_day = 1;
	}
	if ( !isset( $f_end_day ) ) {
		$f_end_day = date( "d" );
	}
	if ( !isset( $f_start_year ) ) {
		$f_start_year = date( "Y" );
	}
	if ( !isset( $f_end_year ) ) {
		$f_end_year = date( "Y" );
	}
	if ( !isset( $f_status ) ) {
		$f_status = "open";
	}*/

	# Build the query string based on the user's viewing criteria.
	# Build the query up in sections, because two queries need to
	# be performed.
	#
	# 1) count of all the rows
	# 2) listing of the current page of rows, ordered appropriately
	#

	# project selection
	if ( "0000000" == $g_project_cookie_val ) { # ALL projects
		$t_access_level = get_current_user_field( "access_level" );
		$t_user_id = get_current_user_field( "id" );

		$t_pub = PUBLIC;
		$t_prv = PRIVATE;
		$query2 = "SELECT DISTINCT( p.id )
			FROM $g_mantis_project_table p, $g_mantis_project_user_list_table u
			WHERE (p.enabled=1 AND
				p.view_state='$t_pub') OR
				(p.enabled=1 AND
				p.view_state='$t_prv' AND
				u.user_id='$t_user_id'  AND
                u.project_id=p.id)
			ORDER BY p.name";
		$result2 = db_query( $query2 );
		$project_count = db_num_rows( $result2 );

		if ( 0 == $project_count ) {
			$t_where_clause = " WHERE 1=1";
		} else {
			$t_where_clause = " WHERE (";
			for ($i=0;$i<$project_count;$i++) {
				$row = db_fetch_array( $result2 );
				extract( $row, EXTR_PREFIX_ALL, "v" );

				$t_where_clause .= "(project_id='$v_id')";
				if ( $i < $project_count - 1 ) {
					$t_where_clause .= " OR ";
				}
			} # end for
			$t_where_clause .= ")";
		}
	} else {
		$t_where_clause = " WHERE project_id='$g_project_cookie_val'";
	}
	# end project selection

	if ( $f_user_id != "any" ) {
		$t_where_clause .= " AND reporter_id='$f_user_id'";
	}

	if ( "none" == $f_assign_id ) {
		$t_where_clause .= " AND handler_id=0";
	} else if ( $f_assign_id != "any" ) {
		$t_where_clause .= " AND handler_id='$f_assign_id'";
	}

	$t_clo_val = CLOSED;
	if (( "on" == $f_hide_closed  )&&( $f_show_status!="closed" )) {
		$t_where_clause = $t_where_clause." AND status<>'$t_clo_val'";
	}

	if ( $f_show_category != "any" ) {
		$t_where_clause = $t_where_clause." AND category='$f_show_category'";
	}
	if ( $f_show_severity != "any" ) {
		$t_where_clause = $t_where_clause." AND severity='$f_show_severity'";
	}
	if ( $f_show_status != "any" ) {
		$t_where_clause = $t_where_clause." AND status='$f_show_status'";
	}

	# Simple Text Search - Thnaks to Alan Knowles
	if ( $f_search_text ) {
		$t_columns_clause = " $g_mantis_bug_table.*";
		$t_from_clause = " FROM $g_mantis_bug_table, $g_mantis_bug_text_table";
		$t_where_clause .= " AND ((summary LIKE '%".addslashes($f_search_text)."%') OR
							(description LIKE '%".addslashes($f_search_text)."%') OR
							(steps_to_reproduce LIKE '%".addslashes($f_search_text)."%') OR
							(additional_information LIKE '%".addslashes($f_search_text)."%') OR
							($g_mantis_bug_table.id LIKE '%".addslashes($f_search_text)."%')) AND
							$g_mantis_bug_text_table.id = $g_mantis_bug_table.bug_text_id";
	} else {
		$t_columns_clause = " *";
		$t_from_clause = " FROM $g_mantis_bug_table";
	}

	# Get the total number of bugs that meet the criteria.
	$query3 = "SELECT count(*) " . $t_from_clause . $t_where_clause;
	$result3 = db_query( $query3 );
	$row = db_fetch_array( $result3 );
	$t_query_count = $row['count(*)'];

	# Guard against silly values of $f_per_page.
	if ( 0 == $f_per_page ) {
		$f_per_page = 1;
	}
	$f_per_page = (int)abs( $f_per_page );


	# Use $t_query_count and $f_per_page to determine how many pages
	# to split this list up into.
	# For the sake of consistency have at least one page, even if it
	# is empty.
	$t_page_count = ceil($t_query_count / $f_per_page);
	if ($t_page_count < 1) {
		$t_page_count = 1;
	}

	# Make sure f_page_number isn't past the last page.
	if ($f_page_number > $t_page_count) {
		$f_page_number = $t_page_count;
	}

	$query  = "SELECT ".$t_columns_clause.", UNIX_TIMESTAMP(last_updated) as last_updated";
	$query .= $t_from_clause;
	$query .= $t_where_clause;

	# Now add the rest of the criteria i.e. sorting, limit.
	if ( !isset( $f_sort ) ) {
		$f_sort="last_updated";
	}
	$query = $query." ORDER BY '$f_sort' $f_dir";

	# t_offset = ((f_page_number - 1) * f_per_page)
	# f_per_page = f_per_page
	#
	# for example page number 1, per page 5:
	#     t_offset = 0
	# for example page number 2, per page 5:
	#     t_offset = 5
	#
	$t_offset = ( ( $f_page_number - 1 ) * $f_per_page );
	if ( isset( $f_per_page ) ) {
		$query = $query." LIMIT $t_offset, $f_per_page";
	}

	# perform query
	$result = db_query( $query );
	$row_count = db_num_rows( $result );

	$link_page = $g_view_all_bug_page;
	$page_type = "all";

	# BEGIN CSV export feature
	if ( isset( $f_csv ) ) {
		# Do we want this parsed to the browser as a csv attachment (also changes the viewing parameters below)
		# Send headers to browser to active mime loading
		# Leigh Morresi <leighm@linuxbandwagon.com>

		header( "Content-Type: text/plain; name=$g_page_title.csv" );
		header( "Content-Transfer-Encoding: BASE64;" );
		header( "Content-Disposition: attachment; filename=$g_page_title.csv" );

		echo "$s_email_project,$g_page_title \n\n";
		echo "$s_priority,$s_id,$s_severity,$s_status,$s_version,$s_assigned_to,$s_reporter,$s_updated,$s_summary\n";

		for ( $i=0; $i < $row_count; $i++ ) {
			$row = db_fetch_array($result);
			extract( $row, EXTR_PREFIX_ALL, "v" );

			$t_last_updated		= date( $g_short_date_format, $v_last_updated );
			$t_priority			= get_enum_element( $s_priority_enum_string, $v_priority );
			$t_severity			= get_enum_element( $s_severity_enum_string, $v_severity );
			$t_status			= get_enum_element( $s_status_enum_string, $v_status );
			$t_hander_name		= get_user_info( $v_handler_id, "username" );
			$t_reporter_name	= get_user_info( $v_reporter_id, "username" );
			$v_summary			= string_display( $v_summary );

			echo "$t_priority,$v_id,$t_severity,$t_status,$v_version,$t_hander_name,$t_reporter_name,$t_last_updated,\"$v_summary\"\r\n";
		}
		die; #end here, maybe redirect back to the current page?
	}
	# END CSV export feature
?>
<?php print_page_top1() ?>
<?php
	if ( get_current_user_pref_field( "refresh_delay" ) > 0 ) {
		print_meta_redirect( $PHP_SELF."?f_page_number=".$f_page_number, get_current_user_pref_field( "refresh_delay" )*60 );
	}
?>
<?php print_page_top2() ?>

<?php include( $g_view_all_include_file ) ?>

<?php print_page_bot1( __FILE__ ) ?>