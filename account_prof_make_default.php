<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php
	# Make the specified profile the default
	# Redirect to account_prof_menu_page.php3
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	# get protected state
	$t_protected = current_user_get_field( 'protected' );

	# protected account check
	if ( ON == $t_protected ) {
		print_mantis_error( ERROR_PROTECTED_ACCOUNT );
	}

	$c_user_id = (integer)current_user_get_field( 'id' );
	$c_id = (integer)$f_id;

    # Set Defaults
	$query = "UPDATE $g_mantis_user_pref_table
    		SET default_profile='$c_id'
    		WHERE user_id='$c_user_id'";
    $result = db_query( $query );

    $t_redirect_url = 'account_prof_menu_page.php';
	if ( $result ) {
		print_header_redirect( $t_redirect_url );
	} else {
		print_mantis_error( ERROR_GENERIC );
	}
?>
