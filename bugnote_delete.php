<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bugnote_delete.php,v 1.26 2002-12-30 10:46:24 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php
	# Remove the bugnote and bugnote text and redirect back to
	# the viewing page
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	$f_bugnote_id = gpc_get_int( 'bugnote_id' );

	bugnote_ensure_exists( $f_bugnote_id );
	$t_bug_id = bugnote_get_field( $f_bugnote_id, 'bug_id' );
	project_access_check( $t_bug_id );
	check_access( config_get( 'delete_bugnote_threshold' ) );
	bug_ensure_exists( $t_bug_id );

	helper_ensure_confirmed( lang_get( 'delete_bugnote_sure_msg' ),
							 lang_get( 'delete_bugnote_button' ) );

	bugnote_delete( $f_bugnote_id );

	print_header_redirect( string_get_bug_view_url( $t_bug_id ) . '#bugnotes' );
?>
