<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php
	# Add file and redirect to the referring page
?>
<?php include( "core_API.php" ) ?>
<?php login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	project_access_check( $f_id );
	check_access( REPORTER );

	$result = 0;
	$good_upload = 0;
	if ( is_uploaded_file( $f_file ) ) {
		$good_upload = 1;
		# grab the file path
		$query = "SELECT file_path
				FROM $g_mantis_project_table
				WHERE id='$g_project_cookie_val'";
		$result = db_query( $query );
		$t_file_path = db_result( $result );

		if ( !file_exists( $t_file_path ) ) {
			echo $MANTIS_ERROR[ERROR_NO_DIRECTORY];
			exit;
		}

		# prepare variables for insertion
		$f_file_name = $f_id."-".$f_file_name;
		$t_file_size = filesize( $f_file );

		switch ( $g_file_upload_method ) {
			case DISK:	umask( 0333 );  # make read only
						copy($f_file, $t_file_path.$f_file_name);
						$query = "INSERT INTO $g_mantis_bug_file_table
								(id, bug_id, title, description, diskfile, filename, folder, filesize, file_type, date_added, content)
								VALUES
								(null, $f_id, '', '', '$t_file_path$f_file_name', '$f_file_name', '$t_file_path', $t_file_size, '$f_file_type', NOW(), '')";
			case DATABASE:
						$t_content = addslashes( fread ( fopen( $f_file, "r" ), $t_file_size ) );
						$query = "INSERT INTO $g_mantis_bug_file_table
								(id, bug_id, title, description, diskfile, filename, folder, filesize, file_type, date_added, content)
								VALUES
								(null, $f_id, '', '', '$t_file_path$f_file_name', '$f_file_name', '$t_file_path', $t_file_size, '$f_file_type', NOW(), '$t_content')";
		}
		$result = db_query( $query );

		# updated the last_updated date
		bug_date_update( $f_id );
	}

	# Determine which view page to redirect back to.
	$t_redirect_url = get_view_redirect_url( $f_id );
?>
<?php print_page_top1() ?>
<?php
	if ( $result ) {
		print_meta_redirect( $t_redirect_url );
	}
?>
<?php print_page_top2() ?>

<p>
<div align="center">
<?php
	if ( $result ) {					# SUCCESS
		PRINT "$s_operation_successful<p>";
	} else {							# FAILURE
		if ( 0 == $good_upload ) {
			PRINT $MANTIS_ERROR[ERROR_NO_FILE_SPECIFIED]."<p>";
		} else {
			print_sql_error( $query );
		}
	}

	print_bracket_link( $t_redirect_url, $s_proceed );
?>
</div>

<?php print_page_bot1( __FILE__ ) ?>