<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
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
	check_access( DEVELOPER );
	check_bug_exists( $f_id );

	$c_id			= (integer)$f_id;
	$c_resolution	= (integer)$f_resolution;
	$c_duplicate_id	= (integer)$f_duplicate_id;

	$t_handler_id = get_current_user_field( "id" );

	# Update fields
	$t_res_val = RESOLVED;
	if ( isset( $f_close_now ) ) {
		$t_res_val = CLOSED;
	}
    $query = "UPDATE $g_mantis_bug_table
    		SET handler_id='$t_handler_id',
    			status='$t_res_val',
    			resolution='$c_resolution',
    			duplicate_id='$c_duplicate_id'
    		WHERE id='$c_id'";
   	$result = db_query($query);

	# get user information
	$u_id = get_current_user_field( "id " );

	$f_bugnote_text = trim( $f_bugnote_text );
	# check for blank bugnote
	if ( !empty( $f_bugnote_text ) ) {
		$c_bugnote_text = string_prepare_textarea( $f_bugnote_text );
		# insert bugnote text
		$query = "INSERT
				INTO $g_mantis_bugnote_text_table
				( id, note )
				VALUES
				( null, '$c_bugnote_text' )";
		$result = db_query( $query );

		# retrieve bugnote text id number
		$t_bugnote_text_id = db_insert_id();

		# insert bugnote info
		$query = "INSERT
				INTO $g_mantis_bugnote_table
				( id, bug_id, reporter_id, bugnote_text_id, date_submitted, last_modified )
				VALUES
				( null, '$c_id', '$u_id','$t_bugnote_text_id', NOW(), NOW() )";
		$result = db_query( $query );
	}
	# update bug last updated
	$result = bug_date_update( $f_id );

   	# notify reporter and handler
	email_resolved( $f_id );

	# Determine which view page to redirect back to.
	$t_redirect_url = get_view_redirect_url( $f_id, 1 );
	if ( $result ) {
		print_header_redirect( $t_redirect_url );
	} else {
		print_mantis_error( ERROR_GENERIC );
	}
?>