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

	# Either generate a random password and email it or make a blank one.

	### Go with random password and email it to the user
    if ( $f_protected==0 ) {
		if ( $g_allow_signup==1 ) {
			### Create random password
			$t_password = create_random_password( $f_email );

			### create the almost unique string for each user then insert into the table
			$t_password2 = process_plain_password( $t_password );
		    $query = "UPDATE $g_mantis_user_table
		    		SET password='$t_password2'
		    		WHERE id='$f_id'";
		    $result = db_query( $query );

			### Send notification email
			email_reset( $f_id, $t_password );
		} else {  		### use blank password, no emailing
			### password is blank password
		    $query = "UPDATE $g_mantis_user_table
		    		SET password='4nPtPLdAFdoxA'
		    		WHERE id='$f_id'";
		    $result = db_query( $query );
		}
	}
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $result ) {
		print_meta_redirect( $g_manage_page, $g_wait_time );
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
	if ( $f_protected==1 ) {				### PROTECTED
		PRINT "$s_account_reset_protected_msg<p>";
	} else if ( $result ) {					### SUCCESS
		PRINT "$s_account_reset_msg<p>";
	} else {								### FAILURE
		print_sql_error( $query );
	}

	print_bracket_link( $g_manage_page, $s_proceed );
?>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>