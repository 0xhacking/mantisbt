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

	# check to see if the cookie does not exist
	if ( empty( $g_view_all_cookie_val ) ) {
		$t_settings_string = "v1#any#any#any#".$g_default_limit_view."#".
							$g_default_show_changed."#0#any#any#last_updated#DESC";
		setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length );
		print_header_redirect( $g_print_all_bug_page."?f=2" );
	}

	# Check to see if new cookie is needed
	$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
	if ( $t_setting_arr[0] != "v1" ) {
		$t_settings_string = "v1#any#any#any#".$g_default_limit_view."#".
							$g_default_show_changed."#0#any#any#last_updated#DESC";
		setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length );
		print_header_redirect( $g_print_all_bug_page."?f=1" );
	}

	if( !isset( $f_search_text ) ) {
		$f_search_text = false;
	}

	if ( !isset( $f_offset ) ) {
		$f_offset = 0;
	}

	if ( !isset( $f_hide_closed ) ) {
		$f_hide_closed = "";
	}

	if ( isset( $f_save ) ) {
		if ( 1 == $f_save ) {
			# We came here via the FILTER form button click
			# Save preferences
			$t_settings_string = "v1#".
								$f_show_category."#".
								$f_show_severity."#".
								$f_show_status."#".
								$f_limit_view."#".
								$f_highlight_changed."#".
								$f_hide_closed."#".
								$f_user_id."#".
								$f_assign_id."#".
								$f_sort."#".
								$f_dir;
			setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length );
		} else if ( 2 == $f_save ) {
			# We came here via clicking a sort link
			# Load pre-existing preferences
			$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
			$f_show_category 		= $t_setting_arr[1];
			$f_show_severity	 	= $t_setting_arr[2];
			$f_show_status 			= $t_setting_arr[3];
			$f_limit_view 			= $t_setting_arr[4];
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
								$f_limit_view."#".
								$f_highlight_changed."#".
								$f_hide_closed."#".
								$f_user_id."#".
								$f_assign_id."#".
								$f_sort."#".
								$f_dir;

			setcookie( $g_view_all_cookie, $t_settings_string, time()+$g_cookie_time_length );
		}
	} else {
		# Load preferences
		$t_setting_arr 			= explode( "#", $g_view_all_cookie_val );
		$f_show_category 		= $t_setting_arr[1];
		$f_show_severity	 	= $t_setting_arr[2];
		$f_show_status 			= $t_setting_arr[3];
		$f_limit_view 			= $t_setting_arr[4];
		$f_highlight_changed 	= $t_setting_arr[5];
		$f_hide_closed 			= $t_setting_arr[6];
		$f_user_id 				= $t_setting_arr[7];
		$f_assign_id 			= $t_setting_arr[8];
		$f_sort 				= $t_setting_arr[9];
		$f_dir		 			= $t_setting_arr[10];
	}

	# Build our query string based on our viewing criteria

	$query = "SELECT *, UNIX_TIMESTAMP(last_updated) as last_updated
			 FROM $g_mantis_bug_table";

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
				p.access_min<='$t_access_level') OR
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
	if ( ( "on" == $f_hide_closed  )&&( "closed" != $f_show_status )) {
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
	if ($f_search_text) {
		$t_where_clause .= " AND ((summary LIKE '%".addslashes($f_search_text)."%')
							OR (description LIKE '%".addslashes($f_search_text)."%')
							OR (steps_to_reproduce LIKE '%".addslashes($f_search_text)."%')
							OR (additional_information LIKE '%".addslashes($f_search_text)."%')
							OR ($g_mantis_bug_table.id LIKE '%".addslashes($f_search_text)."%'))
							AND $g_mantis_bug_text_table.id = $g_mantis_bug_table.bug_text_id";
		$query = "SELECT $g_mantis_bug_table.*, $g_mantis_bug_text_table.description
				FROM $g_mantis_bug_table, $g_mantis_bug_text_table ".$t_where_clause;
	} else {
		$query = $query.$t_where_clause;
	}

	if ( !isset( $f_sort ) ) {
		$f_sort="last_updated";
	}
	$query = $query." ORDER BY '$f_sort' $f_dir";
	if ( $f_sort != "priority" ) {
		$query = $query.", priority DESC";
	}

	if ( isset( $f_limit_view ) ) {
		$query = $query." LIMIT $f_offset, $f_limit_view";
	}

	# perform query
    $result = db_query( $query );
	$row_count = db_num_rows( $result );

	$link_page = $g_print_all_bug_page;
	$page_type = "all";
?>
<?php print_page_top1() ?>
<?php
	if ( get_current_user_pref_field( "refresh_delay" ) > 0 ) {
		print_meta_redirect( $PHP_SELF."?f_offset=".$f_offset, get_current_user_pref_field( "refresh_delay" )*60 );
	}
?>
<?php print_head_bottom() ?>
<?php print_body_top() ?>

<form method="post" action="<?php echo $link_page ?>?f=3">
<input type="hidden" name="f_offset" value="0">
<input type="hidden" name="f_save" value="1">
<input type="hidden" name="f_sort" value="<?php echo $f_sort ?>">
<input type="hidden" name="f_dir" value="<?php echo $f_dir ?>">
<table class="width100">
<tr>
    <td class="print">
        <?php echo $s_search ?>
    </td>
    <td class="print">
		<?php echo $s_reporter ?>
	</td>
    <td class="print">
		<?php echo $s_assigned_to ?>
	</td>
    <td class="print">
		<?php echo $s_category ?>
	</td>
    <td class="print">
		<?php echo $s_severity ?>
	</td>
    <td class="print">
		<?php echo $s_status ?>
	</td>
    <td class="print">
		<?php echo $s_show ?>
	</td>
    <td class="print">
		<?php echo $s_changed ?>
	</td>
    <td class="print">
		<?php echo $s_hide_closed ?>
	</td>
    <td class="print">
		&nbsp;
	</td>
</tr>
<tr>
	<td>
	    <input type="text" name="f_search_text" value="<?php echo $f_search_text; ?>">
	</td>
	<td>
		<select name="f_user_id">
			<option value="any"><?php echo $s_any ?></option>
			<option value="any"></option>
			<?php print_reporter_option_list( $f_user_id ) ?>
		</select>
	</td>
	<td>
		<select name="f_assign_id">
			<option value="any"><?php echo $s_any ?></option>
			<option value="none" <?php if ( "none" == $f_assign_id ) echo "SELECTED" ?>><?php echo $s_none ?></option>
			<option value="any"></option>
			<?php print_assign_to_option_list( $f_assign_id ) ?>
		</select>
	</td>
	<td>
		<select name="f_show_category">
			<option value="any"><?php echo $s_any ?></option>
			<option value="any"></option>
			<?php print_category_option_list( $f_show_category ) ?>
		</select>
	</td>
	<td>
		<select name="f_show_severity">
			<option value="any"><?php echo $s_any ?></option>
			<option value="any"></option>
			<?php print_enum_string_option_list( $s_severity_enum_string, $f_show_severity ) ?>
		</select>
	</td>
	<td>
		<select name="f_show_status">
			<option value="any"><?php echo $s_any ?></option>
			<option value="any"></option>
			<?php print_enum_string_option_list( $s_status_enum_string, $f_show_status ) ?>
		</select>
	</td>
	<td>
		<input type="text" name="f_limit_view" size="3" maxlength="7" value="<?php echo $f_limit_view ?>">
	</td>
	<td>
		<input type="text" name="f_highlight_changed" size="3" maxlength="7" value="<?php echo $f_highlight_changed ?>">
	</td>
	<td>
		<input type="checkbox" name="f_hide_closed" <?php if ( "on" == $f_hide_closed ) echo "CHECKED"?>>
	</td>
	<td>
		<input type="submit" value="<?php echo $s_filter_button ?>">
	</td>
</tr>
</table>
</form>

<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="7">
		<?php echo $s_viewing_bugs_title ?>
		<?php
			if ( $row_count > 0 ) {
				$v_start = $f_offset+1;
				$v_end   = $f_offset+$row_count;
			} else {
				$v_start = 0;
				$v_end   = 0;
			}
			PRINT "($v_start - $v_end)";
		?>
	</td>
	<td class="right">
		<a href="<?php echo $g_summary_page ?>">Back to Summary</a>
	</td>
<p>
</tr>
<tr class="row-category">
	<td class="center" width="8%">
		<?php print_view_bug_sort_link( $link_page, "P", "priority", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "priority" ) ?>
	</td>
	<td class="center" width="8%">
		<?php print_view_bug_sort_link( $link_page, $s_id, "id", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "id" ) ?>
	</td>
	<td class="center" width="3%">
		#
	</td>
	<td class="center" width="12%">
		<?php print_view_bug_sort_link( $link_page, $s_category, "category", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "category" ) ?>
	</td>
	<td class="center" width="10%">
		<?php print_view_bug_sort_link( $link_page, $s_severity, "severity", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "severity" ) ?>
	</td>
	<td class="center" width="10%">
		<?php print_view_bug_sort_link( $link_page, $s_status, "status", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "status" ) ?>
	</td>
	<td class="center" width="12%">
		<?php print_view_bug_sort_link( $link_page, $s_updated, "last_updated", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "last_updated" ) ?>
	</td>
	<td class="center" width="37%">
		<?php print_view_bug_sort_link( $link_page, $s_summary, "summary", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "summary" ) ?>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="8">
		&nbsp;
	</td>
</tr>
<?php
	for($i=0; $i < $row_count; $i++) {
		# prefix bug data with v_
		$row = db_fetch_array($result);
		extract( $row, EXTR_PREFIX_ALL, "v" );

		$v_summary = string_display( $v_summary );
		$t_last_updated = date( $g_short_date_format, $v_last_updated );

		# alternate row colors
		$status_color = alternate_colors( $i, "#ffffff", $g_primary_color2 );

		# grab the bugnote count
		$bugnote_count = get_bugnote_count( $v_id );
		
		# grab the project name
		$project_name = get_project_field($v_project_id,"NAME");

		$query = "SELECT MAX(last_modified)
				FROM $g_mantis_bugnote_table
				WHERE bug_id='$v_id'";
		$res2 = db_query( $query );
		$v_bugnote_updated = db_result( $res2, 0, 0 );
?>
<tr bgcolor="<?php echo $status_color ?>">
	<td class="print">
		<?php echo get_enum_element($s_priority_enum_string, $v_priority) ?>
	</td>
	<td class="print">
		<?php echo $v_id ?>
		<?php # type project name if viewing 'all projects'?>
		<?php if ( "0000000" == $g_project_cookie_val ) {?>
		<BR><?php print "[$project_name]"; }?>
	</td>
	<td class="print">
		<?php
			if ($bugnote_count > 0){
				if ( $v_bugnote_updated >
					strtotime( "-$f_highlight_changed hours" ) ) {
					PRINT "<span class=\"bold\">$bugnote_count</span>";
				} else {
					PRINT "$bugnote_count";
				}
			} else {
				echo "&nbsp;";
			}
		?>
	</td>
	<td class="print">
		<?php echo $v_category ?>
	</td>
	<td class="print">
		<?php print_formatted_severity_string( $v_status, $v_severity ) ?>
	</td>
	<td class="print">
		<?php
			# print username instead of status
			if (( ON == $g_show_assigned_names )&&( $v_handler_id > 0 )&&
				( $v_status!=CLOSED )&&( $v_status!=RESOLVED )) {
				echo "(".get_user_info( $v_handler_id, "username" ).")";
			} else {
				echo get_enum_element( $s_status_enum_string, $v_status );
			}
		?>
	</td>
	<td class="print">
		<?php
			if ( $v_last_updated >
				strtotime( "-$f_highlight_changed hours" ) ) {

				PRINT "<span class=\"bold\">$t_last_updated</span>";
			} else {
				PRINT "$t_last_updated";
			}
		?>
	</td>
	<td class="left">
		<span class="print"><?php echo $v_summary ?></a>
	</td>
</tr>
<?php
	}
?>
</table>
