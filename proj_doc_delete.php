<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( "core_API.php" ) ?>
<?php login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	check_access( MANAGER );  # @@@ Need to check that the person is assigned to this project
  $f_id = (integer)$f_id;

	if ( DISK == $g_file_upload_method ) {
		# grab the file name
		$query = "SELECT diskfile
				FROM $g_mantis_project_file_table
				WHERE id='$f_id'";
		$result = db_query( $query );
		$t_diskfile = db_result( $result );

		# in windows replace with system("del $t_diskfile");
		chmod( $t_diskfile, 0775 );
		unlink( $t_diskfile );
	}

	$query = "DELETE FROM $g_mantis_project_file_table
			WHERE id='$f_id'";
	$result = db_query( $query );

	$t_redirect_url = $g_proj_doc_page;
	if ( $result ) {
		print_header_redirect( $t_redirect_url );
	} else {
		print_mantis_error( ERROR_GENERIC );
	}
?>