<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: csv_export.php,v 1.17 2004-03-23 14:00:35 vboctor Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );
	
	$t_core_path = config_get( 'core_path' );
	
	require_once( $t_core_path . 'filter_api.php' );
	require_once( $t_core_path . 'csv_api.php' );
?>
<?php auth_ensure_user_authenticated() ?>
<?php
	# check to see if the cookie does not exist
	if ( !filter_is_cookie_valid() ) {
		print_header_redirect( 'view_all_set.php?type=0' );
	}

        $t_filename = csv_get_default_filename();

	# Send headers to browser to activate mime loading
	header( 'Content-Type: text/plain; name=' . $t_filename );
	header( 'Content-Transfer-Encoding: BASE64;' );
	header( 'Content-Disposition: attachment; filename=' . $t_filename );

	$t_page_number = 1;
	$t_per_page = -1;
	$t_bug_count = null;
	$t_page_count = null;

        $t_nl = csv_get_newline();
        $t_sep = csv_get_separator();

	# Get bug rows according to the current filter
	$rows = filter_get_bug_rows( $t_page_number, $t_per_page, $t_page_count, $t_bug_count );

	# Get columns to be exported
	$t_columns = csv_get_columns();

	# export the titles
	$t_titles = array();
	foreach ( $t_columns as $column => $title ) {
		$t_titles[] = lang_get( $title );
	}
	echo implode( $t_sep, $t_titles ) . $t_nl;

	# export the rows
	foreach ( $rows as $row ) {
		$t_values = array();
		foreach ( $t_columns as $key => $title ) {
			# check if column should be visible
			if ( !isset( $row[$key] ) ) {
				$t_values[] = '';
				continue;
			}

			$t_function = 'csv_format_' . $key;
			$t_values[] = $t_function( $row[ $key ] );
		}

		echo implode( $t_sep, $t_values ) . $t_nl;
	}
?>