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

	if ( isset( $f_protected ) ) {
		$f_protected = 1;
	} else {
		$f_protected = 0;
	}

	if ( isset( $f_enabled ) ) {
		$f_enabled = 1;
	} else {
		$f_enabled = 0;
	}

	# update action
	# administrator is not allowed to change access level or enabled
	# this is to prevent screwing your own account
	if ( ON == $f_protected ) {
	    $query = "UPDATE $g_mantis_user_table
	    		SET username='$f_username', email='$f_email',
	    			protected='$f_protected'
	    		WHERE id='$f_id'";
	} else {
	    $query = "UPDATE $g_mantis_user_table
	    		SET username='$f_username', email='$f_email',
	    			access_level='$f_access_level', enabled='$f_enabled',
	    			protected='$f_protected'
	    		WHERE id='$f_id'";
	}

    $result = db_query( $query );
    $t_redirect_url = $g_manage_page;
?>
<? print_page_top1() ?>
<?
	if ( $result ) {
		print_meta_redirect( $t_redirect_url );
	}
?>
<? print_page_top2() ?>

<p>
<div align="center">
<?
	if ( ON == $f_protected ) {				# PROTECTED
		PRINT "$s_manage_user_protected_msg<p>";
	} else if ( $result ) {					# SUCCESS
		PRINT "$s_operation_successful<p>";
	} else {								# FAILURE
		print_sql_error( $query );
	}

	print_bracket_link( $t_redirect_url, $s_proceed );
?>
</div>

<? print_page_bot1( __FILE__ ) ?>