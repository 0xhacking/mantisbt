<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

	if ( !access_level_check_greater_or_equal( "viewer" ) ) {
		# should be an access error page
		header( "Location: $g_logout_page" );
		exit;
	}
	# need access level check

	### validating input
	$check_failed = false;
	if ( ( $f_category=="" ) ||
		 ( $f_severity=="" ) ||
		 ( $f_reproducibility=="" ) ||
		 ( $f_summary=="" ) ||
		 ( $f_description=="" ) ) {
		$check_failed = true;
	}

	### required fields ok, proceeding
	if ( !$check_failed ) {
		### Get user id
		$u_id = get_current_user_field( "id" );

		### Make strings safe for database
		$f_summary = string_safe( $f_summary );
		$f_description = string_safe( $f_description );
		$f_additional_info = string_safe( $f_additional_info );
		$f_steps_to_reproduce = string_safe( $f_steps_to_reproduce );
		$f_build = string_safe( $f_build );

		$f_platform = string_safe( $f_platform );
		$f_os = string_safe( $f_os );
		$f_osbuild = string_safe( $f_osbuild );

		### if a profile was selected then let's use that information
		if ( !empty( $f_id ) ) {
			### Get profile data and prefix with v_
			$query = "SELECT id, platform, os, os_build, default_profile
				FROM $g_mantis_user_profile_table
				WHERE id='$f_id'";
		    $result = db_query( $query );
		    $profile_count = db_num_rows( $result );

			$row = db_fetch_array( $result );
			extract( $row, EXTR_PREFIX_ALL, "v" );
			$f_platform	= string_unsafe( $v_platform );
			$f_os		= string_unsafe( $v_os );
			$f_osbuild	= string_unsafe( $v_os_build );
		}

		### Insert text information
		$query = "INSERT
				INTO $g_mantis_bug_text_table
				( id, description, steps_to_reproduce, additional_information )
				VALUES
				( null, '$f_description', '$f_steps_to_reproduce',
				'$f_additional_info' )";
		$result = db_query( $query );

		### Get the id of the text information we just inserted
		### NOTE: this is guarranteed to be the correct one.
		### The value LAST_INSERT_ID is stored on a per connection basis.

		### Use this for MS SQL: SELECT @@IDENTITY AS 'id'
		$query = "select LAST_INSERT_ID()";
		$result = db_query( $query );
		if ( $result ) {
			$t_id = db_result( $result, 0 );
		}

		### check to see if we want to assign this right ff
		$t_status = "new";
		if ( $f_assign_id != "0000000" ) {
			$t_status = "assigned";
		}

		### Insert the rest of the data
		$query = "INSERT
				INTO $g_mantis_bug_table
				( id, project_id, reporter_id, handler_id, duplicate_id, priority, severity,
				reproducibility, status, resolution, projection, category,
				date_submitted, last_updated, eta, bug_text_id, os, os_build,
				platform, version, build, votes, profile_id, summary )
				VALUES
				( null, '$g_project_cookie_val', '$u_id', '0000000', '0000000', 'normal', '$f_severity',
				'$f_reproducibility', '$t_status', 'open', 'minor fix', '$f_category',
				NOW(), NOW(), NOW(), '$t_id', '$f_os', '$f_osbuild',
				'$f_platform', '$f_version', '$f_build',
				1, '$f_profile_id', '$f_summary' )";
		$result = db_query( $query );
	}
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $result ) {
		print_meta_redirect( $g_view_bug_all_page, $g_wait_time );
	}
?>
<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>

<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
<?
	### FORM ERROR
	### required fields not entered
	if ( $check_failed ) {
		PRINT "<b>$s_report_add_error_msg</b><p>";
		if ( $f_category=="" ) {
			PRINT "$s_must_enter_category<br>";
		}
		if ( $f_severity=="" ) {
			PRINT "$s_must_enter_severity<br>";
		}
		if ( $f_reproducibility=="" ) {
			PRINT "$s_must_enter_reproducibility<br>";
		}
		if ( $f_summary=="" ) {
			PRINT "$s_must_enter_summary<br>";
		}
		if ( $f_description=="" ) {
			PRINT "$s_must_enter_description<br>";
		}
		PRINT "<p>";
		PRINT "$s_hit_back_msg";
	}
	### MYSQL ERROR
	else if ( !$result ) {
		PRINT "$s_sql_error_detected <a href=\"<? echo $g_administrator_email ?>\">administrator</a><p>";
		PRINT $query;
	}
	### OK!!!
	else {
		PRINT "$s_submission_thanks_msg<p>";
	}
?>
<p>
<a href="<? echo $g_view_bug_all_page ?>"><? echo $s_proceed ?></a>
</div>

<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>