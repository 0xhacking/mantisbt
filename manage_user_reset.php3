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

	# Either generate a random password and email it or make a blank one.

	### Go with random password and email it to the user
	if ( $g_allow_signup==1 )
		### Create random password
		$t_password = create_random_password( $p_email );

		### create the almost unique string for each user then insert into the table
		$t_password2 = crypt( $t_password );
	    $query = "UPDATE $g_mantis_user_table
	    		SET password='$t_password2'
	    		WHERE id='$f_id'";
	    $result = db_query( $query );

		### Send notification email
		email_reset( $f_id, $t_password );
	} else {  ### use blank password, no emailing
	    if ( $f_protected!="on" ) {
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

<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
<?
	if ( $f_protected=="on" ) {
		PRINT "$s_account_reset_protected_msg<p>";
	}
	else if ( $result ) {
		PRINT "$s_account_reset_msg<p>";
	}
	else {
		PRINT "$s_sql_error_detected <a href=\"<? echo $g_administrator_email ?>\">administrator</a><p>";
		echo $query;
	}
?>
<p>
<a href="<? echo $g_manage_page ?>"><? echo $s_proceed ?></a>
</div>

<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>