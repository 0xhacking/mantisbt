<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	###########################################################################
	# INCLUDES
	###########################################################################

	# Before doing anything else, start output buffering so we don't prevent
	#  headers from being sent if there's a blank line in an included file
	ob_start();

	$t_core_path = dirname ( __FILE__ ) . '/core/';
	require_once( $t_core_path . 'php_api.php' );

	# Load constants and configuration files
  	require_once( 'constant_inc.php' );
	if ( file_exists( 'custom_constant_inc.php' ) ) {
		require_once( 'custom_constant_inc.php' );
	}
	require_once( 'config_defaults_inc.php' );
	if ( file_exists( 'custom_config_inc.php' ) ) {
		require_once( 'custom_config_inc.php' );
	}
	# for backward compatability
	if ( file_exists( 'config_inc.php' ) ) {
		require_once( 'config_inc.php' );
	}
	
	# Load rest of core in seperate directory.
	require_once( $g_core_path . 'API.php');
	# --------------------
?>
