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

	if ( !access_level_check_greater_or_equal( "administrator" ) ) {
		### need to replace with access error page
		header( "Location: $g_logout_page" );
		exit;
	}

	### Delete the bugs, bug text, bugnotes, and bugnote text
	### first select the bug ids
	$query = "SELECT id, bug_text_id
			FROM $g_mantis_bug_table
    		WHERE project_id='$f_project_id'";
    $result = db_query( $query );
    $bug_count = db_num_rows( $result );

	for ($i=0;$i<$bug_count;$i++) {
		$row = db_fetch_array( $result );
		$t_bug_id = $row["id"];
		$t_bug_text_id = $row["bug_text_id"];

		### Delete the bug texts
		$query2 = "DELETE
				FROM $g_mantis_bug_text_table
	    		WHERE id='$t_bug_text_id'";
	    $result2 = db_query( $query2 );

		### select bugnotes to delete
		$query3 = "SELECT id, bugnote_text_id
				FROM $g_mantis_bugnote_table
	    		WHERE bug_id='$t_bug_id'";
	    $result3 = db_query( $query3 );
	    $bugnote_count = db_num_rows( $result3 );

		for ($j=0;$j<$bugnote_count;$j++) {
			$row2 = db_fetch_array( $result3 );
			$t_bugnote_id = $row2["id"];
			$t_bugnote_text_id = $row2["bugnote_text_id"];

			### Delete the bugnotes
			$query = "DELETE
					FROM $g_mantis_bugnote_table
		    		WHERE id='$t_bugnote_id'";
		    $result = db_query( $query );

			### Delete the bugnote texts
			$query4 = "DELETE
					FROM $g_mantis_bugnote_text_table
		    		WHERE id='$t_bugnote_text_id'";
		    $result4 = db_query( $query4 );
		}
	}

	### now finally remove all bugs that are part of the project
	$query = "DELETE
			FROM $g_mantis_bug_table
    		WHERE project_id='$f_project_id'";
    $result = db_query( $query );

	### Delete the project entry
	$query = "DELETE
			FROM $g_mantis_project_table
    		WHERE id='$f_project_id'";
    $result = db_query( $query );

	### Delete the project categories
	$query = "DELETE
			FROM $g_mantis_project_category_table
    		WHERE project_id='$f_project_id'";
    $result = db_query( $query );

	### Delete the project versions
	$query = "DELETE
			FROM $g_mantis_project_version_table
    		WHERE project_id='$f_project_id'";
    $result = db_query( $query );
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $result ) {
		print_meta_redirect( $g_manage_project_menu_page, $g_wait_time );
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
	if ( $result ) {
		PRINT "$s_project_deleted_msg<p>";
	}
	else {
		PRINT "$s_sql_error_detected <a href=\"<? echo $g_administrator_email ?>\">administrator</a><p>";
	}
?>
<p>
<a href="<? echo $g_manage_project_menu_page ?>"><? echo $s_proceed ?></a>
</div>

<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>