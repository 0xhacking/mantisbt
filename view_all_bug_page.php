<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Revision: 1.43 $
	# $Author: jlatour $
	# $Date: 2003-01-25 13:52:44 $
	#
	# $Id: view_all_bug_page.php,v 1.43 2003-01-25 13:52:44 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );
	
	require_once( $g_core_path . 'compress_api.php' );
	require_once( $g_core_path . 'filter_api.php' );
?>
<?php login_cookie_check() ?>
<?php
	$f_page_number		= gpc_get_int( 'page_number', 1 );

	# check to see if the cookie does not exist
	if ( !filter_is_cookie_valid() ) {
		print_header_redirect( 'view_all_set.php?type=0' );
	}

	$t_bug_count = null;
	$t_page_count = null;

	$rows = filter_get_bug_rows( &$f_page_number, null, &$t_page_count, &$t_bug_count );

	compress_start();

	print_page_top1();

	if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
		print_meta_redirect( 'view_all_bug_page.php?page_number='.$f_page_number, current_user_get_pref( 'refresh_delay' )*60 );
	}

	print_page_top2();

	include( $g_view_all_include_file );

	print_page_bot1( __FILE__ );

	compress_stop();
?>
