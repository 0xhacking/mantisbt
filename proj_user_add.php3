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
	check_access( MANAGER );

	$query = "SELECT id
			FROM $g_mantis_user_table
			WHERE username='$f_username'";
	$result = db_query( $query );
	if ( db_num_rows( $result ) > 0 ) {
		$t_user_id = db_result( $result, 0, 0 );

		### Add user to project
		$query = "INSERT
				INTO $g_mantis_project_user_list_table
				(project_id, user_id, access_level)
				VALUES
				('$g_project_cookie_val', '$t_user_id', '$f_access_level' )";
		$result = db_query($query);
	} else {
		$result = 0;
	}
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $result ) {
		print_meta_redirect( $g_proj_user_menu_page, $g_wait_time );
	}
?>
<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>
<? print_top_page( $g_top_include_page ) ?>

<? print_menu( $g_menu_include_file ) ?>

<p>
<div align="center">
<?
	if ( $result ) {				### SUCCESS
		PRINT "$s_project_user_added_msg<p>";
	} else {						### FAILURE
		print_sql_error( $query );
	}

	print_bracket_link( $g_proj_user_menu_page, $s_proceed );
?>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>