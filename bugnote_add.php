<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bugnote_add.php,v 1.31 2002-12-30 10:46:24 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php
	# Insert the bugnote into the database then redirect to the bug page
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	$f_bug_id		= gpc_get_int( 'bug_id' );
	$f_private		= gpc_get_bool( 'private' );
	$f_bugnote_text	= gpc_get_string( 'bugnote_text', '' );

	project_access_check( $f_bug_id );
	check_access( config_get( 'add_bugnote_threshold' ) );
	bug_ensure_exists( $f_bug_id );

	$f_bugnote_text = trim( $f_bugnote_text );

	# check for blank bugnote
	if ( !is_blank( $f_bugnote_text ) ) {
		bugnote_add( $f_bug_id, $f_bugnote_text, $f_private );
	}

	print_header_redirect_view( $f_bug_id );
?>
