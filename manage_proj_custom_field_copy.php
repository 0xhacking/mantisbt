<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_proj_custom_field_copy.php,v 1.2 2007-09-25 23:52:11 nuclear_eclipse Exp $
 	# --------------------------------------------------------

	require_once('core.php');

	auth_reauthenticate();

	$f_project_id		= gpc_get_int( 'project_id' );
	$f_other_project_id	= gpc_get_int( 'other_project_id' );
	$f_copy_from		= gpc_get_bool( 'copy_from' );
	$f_copy_to			= gpc_get_bool( 'copy_to' );

	access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_project_id );
	access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_other_project_id );

	if ( $f_copy_from ) {
		$t_src_project_id = $f_other_project_id;
		$t_dst_project_id = $f_project_id;
	} else if ( $f_copy_to ) {
		$t_src_project_id = $f_project_id;
		$t_dst_project_id = $f_other_project_id;
	} else {
		trigger_error( ERROR_CATEGORY_NO_ACTION, ERROR );
	}
 
 	project_copy_custom_fields( $t_dst_project_id, $t_src_project_id );
 	
 	print_header_redirect( 'manage_proj_edit_page.php?project_id=' . $f_project_id );
?>
