<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_close.php,v 1.26 2002-10-23 00:50:53 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php
	# This file sets the bug to the chosen resolved state then gives the
	# user the opportunity to enter a reason for the closure
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	$f_bug_id		= gpc_get_int( 'f_bug_id' );
	$f_bugnote_text	= gpc_get_string( 'f_bugnote_text', '' );

	project_access_check( $f_bug_id );
	check_access( config_get( 'close_bug_threshold' ) );
	bug_ensure_exists( $f_bug_id );

	bug_close( $f_bug_id, $f_bugnote_text );

	print_header_redirect( 'view_all_bug_page.php' );
?>
