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
	check_access( ADMINISTRATOR );

	# Delete the users who have never logged in and are older than 1 week
	$days_old = 7;
	$query = "DELETE
			FROM $g_mantis_user_table
			WHERE login_count=0 AND TO_DAYS(NOW()) - '$days_old' > TO_DAYS(date_created)";
	$result = db_query($query);

	$t_redirect_url = $g_manage_page;
?>
<? print_page_top1() ?>
<?
	if ( $result ) {
		print_meta_redirect( $t_redirect_url );
	}
?>
<? print_page_top2() ?>

<? print_proceed( $result, $query, $t_redirect_url ) ?>

<? print_page_bot1( __FILE__ ) ?>