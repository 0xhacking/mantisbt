<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php
	# This file sets the bug to the chosen resolved state then gives the
	# user the opportunity to enter a reason for the closure
?>
<?php include( "core_API.php" ) ?>
<?php login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	project_access_check( $f_id );
	check_access( UPDATER );
	check_bug_exists( $f_id );

	$t_handler_id = get_current_user_field( "id " );

	# Update fields
	$t_clo_val = CLOSED;
	$query = "UPDATE $g_mantis_bug_table
			SET status='$t_clo_val'
			WHERE id='$f_id'";
	$result = db_query($query);

	# updated the last_updated date
	bug_date_update( $f_id );

	email_close( $f_id );

	$t_redirect_url = $g_view_all_bug_page;
?>
<?php print_page_top1() ?>
<?php
	if ( $result ) {
		print_meta_redirect( $t_redirect_url );
	}
?>
<?php print_page_top2() ?>

<?php print_proceed( $result, $query, $t_redirect_url ) ?>

<?php print_page_bot1( __FILE__ ) ?>