<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? #login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

	$valid_project = 1;
	### Check for invalid project_id selection
	if ( empty( $f_project_id ) || ( $f_project_id=="0000000" ) ) {
		$valid_project = 0;
	}

	### Add item
	setcookie( $g_project_cookie, $f_project_id, time()+$g_cookie_time_length );
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $valid_project==1 ) {
		print_meta_redirect( $g_main_page, $g_wait_time );
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
	if ( $valid_project==1 ) {		### SUCCESS
		PRINT "$s_selected_project<p>";
	} else {						### FAILURE
		PRINT "$s_valid_project_msg";
	}

	print_bracket_link( $g_main_page, $s_proceed );
?>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>