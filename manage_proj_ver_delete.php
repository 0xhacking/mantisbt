<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	check_access( config_get( 'manage_project_threshold' ) );

	$f_project_id = gpc_get_int( 'project_id' );
	$f_version = gpc_get_string( 'version' );

	helper_ensure_confirmed( lang_get( 'version_delete_sure' ),
							 lang_get( 'delete_version_button' ) );

	# delete version
	$result = version_delete( $f_project_id, $f_version );

    $t_redirect_url = 'manage_proj_edit_page.php?project_id='.$f_project_id;
	if ( $result ) {
		print_header_redirect( $t_redirect_url );
	} else {
		print_mantis_error( ERROR_GENERIC );
	}
?>
