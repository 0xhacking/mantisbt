<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: file_api.php,v 1.4 2002-08-27 12:44:10 vboctor Exp $
	# --------------------------------------------------------

	###########################################################################
	# File API
	###########################################################################

	# --------------------
	# Gets the filename without the bug id prefix.
	function file_get_display_name( $p_filename ) {
		$t_array = explode ('-', $p_filename, 2);
		return $t_array[1];
	}
	# --------------------
	# List the attachments belonging to the specified bug.  This is used from within
	# view_bug_page.php and view_bug_advanced_page.php
	function file_list_attachments ( $p_bug_id ) {
		$c_id = (integer) $p_bug_id;

		$query = "SELECT *, UNIX_TIMESTAMP(date_added) as date_added ".
				"FROM " . config_get('mantis_bug_file_table') . ' '.
				"WHERE bug_id='$c_id'";
		$result = db_query( $query );
		$num_files = db_num_rows( $result );
		for ($i = 0; $i < $num_files; $i++) {
			$row = db_fetch_array( $result );
			extract( $row, EXTR_PREFIX_ALL, 'v' );
			$v_filesize = number_format( $v_filesize );
			$v_date_added = date( config_get( 'normal_date_format' ), ( $v_date_added ) );

			PRINT "<a href=\"file_download.php?f_id=$v_id&amp;f_type=bug\">".file_get_display_name($v_filename)."</a> ($v_filesize bytes) <span class=\"italic\">$v_date_added</span>";

			if ( access_level_check_greater_or_equal( config_get( 'handle_bug_threshold' ) ) ) {
				PRINT " [<a class=\"small\" href=\"bug_file_delete.php?f_id=$p_bug_id&amp;f_file_id=$v_id\">" . lang_get('delete_link') . '</a>]';
			}
			
			if ( ( FTP == config_get( 'file_upload_method' ) ) && file_exists ( $v_diskfile ) ) {
				PRINT ' (' . lang_get( 'cached' ) . ')';
			}

			if ( $i != ($num_files - 1) ) {
				PRINT '<br />';
			}
		}
	}
	# --------------------
	# Delete all cached files that are older than configured number of days.
	function file_ftp_cache_cleanup() {
		
	}
	# --------------------
	# Connect to ftp server using configured server address, user name, and password.
	function file_ftp_connect() {
		$conn_id = ftp_connect( config_get( 'file_upload_ftp_server' ) ); 
		$login_result = ftp_login( $conn_id, config_get( 'file_upload_ftp_user' ), config_get( 'file_upload_ftp_pass' ) );

		if ( ( !$conn_id ) || ( !$login_result ) ) {
			trigger_error( ERROR_FTP_CONNECT_ERROR, ERROR );
		}

		return $conn_id;
	}
	# --------------------
	# Put a file to the ftp server.
	function file_ftp_put ( $p_conn_id, $p_remote_filename, $p_local_filename ) {
		set_time_limit(0);
		$upload = ftp_put( $p_conn_id, $p_remote_filename, $p_local_filename, FTP_BINARY);
	}
	# --------------------
	# Get a file from the ftp server.
	function file_ftp_get ( $p_conn_id, $p_local_filename, $p_remote_filename ) {
		set_time_limit(0);
		$download = ftp_get( $p_conn_id, $p_local_filename, $p_remote_filename, FTP_BINARY);
	}
	# --------------------
	# Delete a file from the ftp server
	function file_ftp_delete ( $p_conn_id, $p_filename ) {
		@ftp_delete( $p_conn_id, $p_filename );
	}
	# --------------------
	# Disconnect from the ftp server
	function file_ftp_disconnect( $p_conn_id ) {
		ftp_quit( $p_conn_id ); 
	}
	# --------------------
	# Delete a local file even if it is read-only.
	function file_delete_local( $p_filename ) {
		# in windows replace with system("del $t_diskfile");
		if ( file_exists( $p_filename ) ) {
			chmod( $p_filename, 0775 );
			unlink( $p_filename );
		}
	}
?>