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
	$t_res_val = RESOLVED;
	if ( isset( $f_close_now ) ) {
		$t_res_val = CLOSED;
	}
    $query = "UPDATE $g_mantis_bug_table
    		SET handler_id='$t_handler_id',
    			status='$t_res_val',
    			resolution='$f_resolution',
    			duplicate_id='$f_duplicate_id'
    		WHERE id='$f_id'";
   	$result = db_query($query);
?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<p>
<div align="center">
<?php
	if ( $result ) {					# SUCCESS
		PRINT "$s_operation_successful<p>";
	} else {							# FAILURE
		print_sql_error( $query );
	}
?>
</div>

<?php include( $g_view_bug_inc ) ?>

<?php $f_resolve_note = 1; # Must set this ?>
<?php include( $g_bugnote_include_file ) ?>

<?php print_page_bot1( __FILE__ ) ?>