<p>
<table class="width100" cellspacing="0">
<form method="post" action="<?php echo $link_page ?>?f=3">
<input type="hidden" name="f_offset" value="0">
<input type="hidden" name="f_save" value="1">
<input type="hidden" name="f_sort" value="<?php echo $f_sort ?>">
<input type="hidden" name="f_dir" value="<?php echo $f_dir ?>">
<input type="hidden" name="f_page_number" value="<?php echo $f_page_number ?>">
<input type="hidden" name="f_per_page" value="<?php echo $f_per_page ?>">
<tr class="row-category2">
    <td class="small-caption">
		<?php echo $s_reporter ?>
	</td>
    <td class="small-caption">
		<?php echo $s_assigned_to ?>
	</td>
    <td class="small-caption">
		<?php echo $s_category ?>
	</td>
    <td class="small-caption">
		<?php echo $s_severity ?>
	</td>
    <td class="small-caption">
		<?php echo $s_status ?>
	</td>
    <td class="small-caption">
		<?php echo $s_show ?>
	</td>
    <td class="small-caption">
		<?php echo $s_changed ?>
	</td>
    <td class="small-caption">
		<?php echo $s_hide_closed ?>
	</td>
</tr>
<tr>
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
		<input type="text" name="f_per_page" size="3" maxlength="7" value="<?php echo $f_per_page ?>">
	</td>
	<td>
		<input type="text" name="f_highlight_changed" size="3" maxlength="7" value="<?php echo $f_highlight_changed ?>">
	</td>
	<td>
		<input type="checkbox" name="f_hide_closed" <?php if ( "on" == $f_hide_closed ) echo "CHECKED"?>>
	</td>
</tr>
<tr class="row-category2">
    <td class="small-caption">
        <?php echo $s_search ?>
    </td>
	<td colspan="7">
		&nbsp;
	</td>
</tr>
<tr>
	<td>
	    <input type="text" size="16" name="f_search_text" value="<?php echo $f_search_text; ?>">
	</td>
	<td class="right" colspan="7">
		<input type="submit" name="f_filter" value="<?php echo $s_filter_button ?>">
		<input type="submit" name="f_csv" value="<?php echo $s_csv_export ?>">
	</td>
</tr>
</form>
</table>

<p>
<table class="width100" cellspacing="1">
<form method="post" action="<?php echo $g_view_all_bug_update ?>">
<tr>
	<td class="form-title" colspan="8">
		<?php echo $s_viewing_bugs_title ?>
		<?php
			if ( $row_count > 0 ) {
				$v_start = $t_offset+1;
				$v_end   = $t_offset+$row_count;
			} else {
				$v_start = 0;
				$v_end   = 0;
			}
		?>
		(<?php echo $v_start ?> - <?php echo $v_end ?> / <?php echo $t_query_count ?>)
	</td>
	<td class="right">
		[
		<?php
			# print out a link for each page i.e.
			#     [ 1 2 3 ]
			#
			for ( $i = 1; $i <= $t_page_count; $i++ ) {
				if ( $i == $f_page_number ) {
					echo $i;
				} else {
		?>
				<a href="<?php echo $g_view_all_bug_page ?>?f_page_number=<?php echo $i ?>"><?php echo $i ?></a>
		<?php
				}
			}
		?>
		]
	</td>
</tr>
<tr class="row-category">
	<td class="center" width="2%">
		&nbsp;
	</td>
	<td class="center" width="5%">
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
	<td class="center" width="38%">
		<?php print_view_bug_sort_link( $link_page, $s_summary, "summary", $f_sort, $f_dir ) ?>
		<?php print_sort_icon( $f_dir, $f_sort, "summary" ) ?>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="9">
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
		$status_color = alternate_colors( $i );

		# choose color based on status only if not resolved
		# The code creates the appropriate variable name
		# then references that color variable
		# You could replace this with a bunch of if... then... else
		# statements
		if ( !( CLOSED == $v_status ) ) {
			$t_color_str = get_enum_element( $g_status_enum_string, $v_status );
			$t_color_variable_name = "g_".$t_color_str."_color";
			$status_color = $$t_color_variable_name;
		}

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
	<td>
		<?php	if ( access_level_check_greater_or_equal( UPDATER ) ) { ?>
			<input type="checkbox" name="f_bug_arr[]" value="<?php echo $v_id ?>">
		<?php } else { ?>
			&nbsp;
		<?php } ?>
	</td>
	<td class="center">
		<?php
			if ( ON == $g_show_priority_text ) {
				echo get_enum_element($s_priority_enum_string, $v_priority);
			} else {
				print_status_icon( $v_priority );
			}
		?>
	</td>
	<td class="center">
		<?php
			print_bug_link( $v_id );
			# type project name if viewing 'all projects'
			if (( ON == $g_show_bug_project_links )&&( "0000000" == $g_project_cookie_val )) {
				echo "<br />[";
				print_view_bug_sort_link( $link_page, "$project_name", "project_id", $f_sort, $f_dir );
				echo "]";
			}
		?>
	</td>
	<td class="center">
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
	<td class="center">
		<?php echo $v_category ?>
	</td>
	<td class="center">
		<?php print_formatted_severity_string( $v_status, $v_severity ) ?>
	</td>
	<td class="center">
		<?php
			# print username instead of status
			if ( ( ON == $g_show_assigned_names )&&( $v_handler_id > 0 ) ) {
				echo "(".get_user_info( $v_handler_id, "username" ).")";
			} else {
				echo get_enum_element( $s_status_enum_string, $v_status );
			}
		?>
	</td>
	<td class="center">
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
		<?php echo $v_summary ?>
	</td>
</tr>
<?php
	}
?>
<?php	if ( access_level_check_greater_or_equal( UPDATER ) ) { ?>
<tr>
	<td colspan="9">
		<select name="f_project_id">
		<?php print_project_option_list() ?>
		</select>
		<!--
		# @@@ not functional yet
		<select name="f_action">
		<?php print_all_bug_action_option_list() ?>
		</select>
		-->
		<input type="submit" value="<?php echo $s_move_bugs ?>">
	</td>
</tr>
</form>
<?php } ?>
</table>

<?php # Show NEXT and PREV links as needed ?>
<p>
<div align="center">
<?php
	# print the [ prev ] link
	if ($f_page_number > 1) {
		$t_prev_page_number = $f_page_number - 1;
		print_bracket_link( $link_page."?f_page_number=".$t_prev_page_number, $s_view_prev_link." ".$f_per_page );
	} else {
		print_bracket_link( "", $s_view_prev_link." ".$f_per_page );
	}

	# print the [ next ] link
	if ($f_page_number < $t_page_count) {
		$t_next_page_number = $f_page_number + 1;
		print_bracket_link( $link_page."?f_page_number=".$t_next_page_number, $s_view_next_link." ".$f_per_page );
	} else {
		print_bracket_link( "", $s_view_next_link." ".$f_per_page );
	}
?>
</div>

<?php print_status_colors() ?>
