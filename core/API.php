<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	###########################################################################
	# INCLUDES
	###########################################################################

	# Include compatibility file before anything else
	include( 'php_API.php' );

	# Should be eventually moved to the admin scripts, but keep it here for a while
	# to make sure people don't miss it.
	function obsolete_config_variable($var, $replace) {
		global $$var;
		if (isset($$var)) {
			PRINT '$' . $var . ' is now obsolete';
			if ($replace != '') {
				PRINT ', please use $' . $replace;
			}
			exit;
		}
	}

	# Check for obsolete variables
	obsolete_config_variable('g_notify_developers_on_new', 'g_notify_flags');
	obsolete_config_variable('g_notify_on_new_threshold', 'g_notify_flags');
	obsolete_config_variable('g_notify_admin_on_new', 'g_notify_flags');

	include( 'timer_API.php' );

	# initialize our timer
	$g_timer = new BC_Timer;

	# seed random number generator
	list($usec,$sec)=explode(' ',microtime());
	mt_srand($sec*$usec);

	# DATABASE WILL BE OPENED HERE!!  THE DATABASE SHOULDN'T BE EXPLICITLY
	# OPENED ANYWHERE ELSE.
	require( 'database_API.php' );

	# Nasty code to select the proper language file
	if ( !empty( $g_string_cookie_val ) ) {
		$query = "SELECT DISTINCT language
				FROM $g_mantis_user_pref_table pref, $g_mantis_user_table user
				WHERE user.cookie_string='$g_string_cookie_val' AND
						user.id=pref.user_id";
		$result = db_query( $query );
		$g_active_language = db_result( $result, 0 , 0 );
		if (empty( $g_active_language )) {
			$g_active_language = $g_default_language;
		}
	} else {
		$g_active_language = $g_default_language;
	}

	include( 'lang/strings_'.$g_active_language.'.txt' );

	# Allow overriding strings declared in the language file.
	# custom_strings_inc.php can use $g_active_language
	if ( file_exists( 'custom_strings_inc.php' ) ) {
		include ( 'custom_strings_inc.php' );
	}

	require( 'config_API.php' );
	require( 'gpc_API.php' );
	require( 'error_API.php' );
	require( 'security_API.php' );
	require( 'html_API.php' );
	require( 'print_API.php' );
	require( 'helper_API.php' );
	require( 'summary_API.php' );
	require( 'date_API.php' );
	require( 'user_API.php' );
	require( 'email_API.php' );
	require( 'news_API.php' );
	require( 'icon_API.php' );
	require( 'ldap_API.php' );
	require( 'history_API.php' );
	require( 'proj_user_API.php' );
	require( 'category_API.php' );
	require( 'version_API.php' );
	require( 'compress_API.php' );
	require( 'relationship_API.php' );
	require( 'file_API.php' );
	require( 'custom_attribute_API.php' );
	require( 'bugnote_API.php' );
	require( 'bug_API.php' );

	if (ON == $g_use_jpgraph) {
		require( 'graph_API.php' );
		require( $g_jpgraph_path . 'jpgraph.php' );
		require( $g_jpgraph_path . 'jpgraph_line.php' );
		require( $g_jpgraph_path . 'jpgraph_bar.php' );
		require( $g_jpgraph_path . 'jpgraph_pie.php' );
		require( $g_jpgraph_path . 'jpgraph_pie3d.php' );	
	}
	# --------------------
?>
