<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# <SQLI>
	# This page allows the close / suppress / others mass treatments, and display the adequate page
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	# the queries
	function updateBugLite($p_id, $p_status, $p_request) {
		global $g_mantis_bug_table;

		$t_handler_id = current_user_get_field( 'id' );
		$t_query = '';

		# history treatment
		# extract current extended information into history variables
		$result = get_bug_row_ex ( $p_id );
		if ( 0 == db_num_rows( $result ) ) {
			# speed is not an issue in this case, so re-use code
			bug_ensure_exists( $p_id );
		}
		$row = db_fetch_array( $result );
		extract( $row, EXTR_PREFIX_ALL, 'h' );

		switch ( $p_status ) {
			case 'MOVED':
				$t_query = "project_id='$p_request'";
				break;
			case CLOSED:
				$t_query="status='$p_status'";
				break ;
			case ASSIGNED:
				$t_handler_id = $p_request ;
				$t_query="status='$p_status'";
				break ;
			case RESOLVED:
				$t_query=" status='$p_status', resolution='$p_request'";
				break ;
			case 'UP_PRIOR':
				$t_query="priority='$p_request'";
				break ;
			case 'UP_STATUS':
				$t_query="status='$p_request'";
				break ;
		}
		# Update fields
		$query = "UPDATE $g_mantis_bug_table ".
	    		"SET handler_id='$t_handler_id', $t_query ".
				"WHERE id='$p_id'";
	   	$result = db_query( $query );

		# history logging should be done after the field updates
		switch ( $p_status ) {
			case 'MOVED':
				history_log_event_direct( $p_id, 'project_id', $h_project_id, $p_request, $t_handler_id );
				break;
			case CLOSED:
				history_log_event_direct( $p_id, 'status', $h_status, $p_status, $t_handler_id );
				break ;
			case ASSIGNED:
				history_log_event_direct( $p_id, 'handler_id', $h_handler_id, $p_request, $t_handler_id );
				history_log_event_direct( $p_id, 'status', $h_status, $p_status, $t_handler_id );
				break ;
			case RESOLVED:
				history_log_event_direct( $p_id, 'resolution', $h_resolution, $p_request, $t_handler_id );
				history_log_event_direct( $p_id, 'status', $h_status, $p_status, $t_handler_id );
				break ;
			case 'UP_PRIOR':
				history_log_event_direct( $p_id, 'priority', $h_priority, $p_request, $t_handler_id );
				break ;
			case 'UP_STATUS':
				history_log_event_direct( $p_id, 'status', $h_status, $p_request, $t_handler_id );
				break ;
		}
		# update bug last updated
		bug_date_update( $p_id );
	} # updateBug

	if ( '1' == $f_actionconfirmed ) {
		foreach ( $f_bug_arr as $value ) {
			# get the id and the bug_text_id parameters
			# the bug_text_id is used only for the delete function
			$t_id_arr = explode( ',', $value );

			switch ( $f_action ) {
				case 'CLOSE':
					updateBugLite( $t_id_arr[0], CLOSED, '' );
					break;
				case 'DELETE':
					deleteBug( $t_id_arr[0], $t_id_arr[1] );
					break;
				case 'MOVE':
					updateBugLite( $t_id_arr[0], 'MOVED', $f_project_id );
					break;
				case 'ASSIGN':
					updateBugLite( $t_id_arr[0], ASSIGNED, $f_assign );
					break;
				case 'RESOLVE':
					updateBugLite( $t_id_arr[0], RESOLVED, $f_resolution );
					break;
				case 'UP_PRIOR':
					updateBugLite( $t_id_arr[0], 'UP_PRIOR', $f_priority );
					break;
				case 'UP_STATUS':
					updateBugLite( $t_id_arr[0], 'UP_STATUS', $f_status );
					break;
				}
		}
		print_meta_redirect( 'view_all_bug_page.php', 0 );
	}
?>