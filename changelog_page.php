<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: changelog_page.php,v 1.4 2004-07-05 14:45:27 vboctor Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );
	
	$t_core_path = config_get( 'core_path' );
	
	require_once( $t_core_path.'bug_api.php' );
?>
<?php
	# this page is invalid for the 'All Project' selection
	if ( ALL_PROJECTS == helper_get_current_project() ) {
		print_header_redirect( 'login_select_proj_page.php?ref=changelog_page.php' );
	}

	html_page_top1( lang_get( 'changelog' ) );  // title
	html_page_top2();

	$f_project_id	= helper_get_current_project();
	$c_project_id   = db_prepare_int( $f_project_id );
	$t_project_name = project_get_field( $f_project_id, 'name' );
	$t_can_view_private = access_has_project_level( config_get( 'private_bug_threshold' ), $f_project_id );

	$t_resolved = config_get( 'bug_resolved_status_threshold' );
	$t_bug_table	= config_get( 'mantis_bug_table' );

	$t_version_rows = version_get_all_rows( $f_project_id );

	echo '<br /><span class="pagetitle">', lang_get( 'changelog' ), '</span><br /><br />';
	echo '<tt>';

	$i = 0;

	foreach( $t_version_rows as $t_version_row ) {
		if ( $i > 0 ) {
			echo '<br />';
		}
		$i++;

		$t_version = $t_version_row['version'];
		$c_version = db_prepare_string( $t_version );

		$query = "SELECT id, view_state FROM $t_bug_table WHERE project_id='$c_project_id' AND fixed_in_version='$c_version' ORDER BY last_updated DESC";

		$t_release_title = $t_project_name . ' - ' . $t_version;
		echo $t_release_title, '<br />';
		echo str_pad( '', strlen( $t_release_title ), '=' ), '<br />';
		
		for ( $t_result = db_query( $query ); !$t_result->EOF; $t_result->MoveNext() ) {
			# hide private bugs if user doesn't have access to view them.
			if ( !$t_can_view_private && ( $t_result->fields['view_state'] == VS_PRIVATE ) ) {
				continue;
			}

			$t_issue_id = $t_result->fields['id'];

			if ( !helper_call_custom_function( 'changelog_include_issue', array( $t_issue_id ) ) ) {
				continue;
			}

			helper_call_custom_function( 'changelog_print_issue', array( $t_issue_id ) );
		}
	}

	echo '</tt>';

	html_page_bottom1( __FILE__ );
?>