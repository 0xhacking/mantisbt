<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	###########################################################################
	# History API
	###########################################################################
	# --------------------
	# log the changes
	# events should be logged *after* the modification
	function history_log_event( $p_bug_id, $p_field_name, $p_old_value ) {
		global $g_mantis_bug_history_table;

		$t_user_id   = get_current_user_field( "id" );
		$t_new_value = get_bug_field( $p_bug_id, $p_field_name );

		# Only log events that change the value
		if ( $t_new_value != $p_old_value ) {
			$query = "INSERT INTO $g_mantis_bug_history_table
					( user_id, bug_id, date_modified, field_name, old_value, new_value )
					VALUES
					( '$t_user_id', '$p_bug_id', NOW(), '$p_field_name', '$p_old_value', '$t_new_value' )";
			$result = db_query( $query );
		}
	}
	# --------------------
	# log the changes
	# events should be logged *after* the modification
	# These are special case logs (new bug, deleted bugnote, etc.)
	function history_log_event_special( $p_bug_id, $p_type, $p_optional="",  $p_optional2="" ) {
		global $g_mantis_bug_history_table;

		$p_optional = string_prepare_text( $p_optional );

		$t_user_id   = get_current_user_field( "id" );
		$query = "INSERT INTO $g_mantis_bug_history_table
				( user_id, bug_id, date_modified, type, old_value, new_value )
				VALUES
				( '$t_user_id', '$p_bug_id', NOW(), '$p_type', '$p_optional', '$p_optional2' )";
		$result = db_query( $query );
	}
	# --------------------
	# return all bug history for a given bug id ordered by date
	function history_get_events( $p_bug_id ) {
		global $g_mantis_bug_history_table, $g_mantis_user_table;

		$query = "SELECT b.*, u.username
				FROM $g_bug_history_table b
				LEFT JOIN $g_mantis_user_table u
				ON b.user_id=u.id
				WHERE bug_id='$p_bug_id'
				ORDER BY date_modified DESC";
		$result = db_query( $query );
	}
	# --------------------
?>