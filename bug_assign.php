<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_assign.php,v 1.27 2003-01-23 23:02:51 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	# Assign bug to user then redirect to viewing page
?>
<?php
	require_once( 'core.php' );
	
	require_once( $g_core_path . 'bug_api.php' );
	require_once( $g_core_path . 'project_api.php' );
?>
<?php login_cookie_check() ?>
<?php
	$f_bug_id = gpc_get_int( 'bug_id' );

	project_access_check( $f_bug_id );
	check_access( config_get( 'handle_bug_threshold' ) );

	bug_ensure_exists( $f_bug_id );

	bug_assign( $f_bug_id, auth_get_current_user_id() );

	print_header_redirect_view( $f_bug_id );
?>