<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: custom_function_api.php,v 1.3 2004-07-10 12:09:46 vboctor Exp $
	# --------------------------------------------------------

	### Custom Function API ###

	# --------------------
	# Checks the provided bug and determines whether it should be included in the changelog 
	# or not.
	# returns true: to include, false: to exclude.
	function custom_function_default_changelog_include_issue( $p_issue_id ) {
		$t_issue = bug_get( $p_issue_id );

		return ( ( $t_issue->duplicate_id == 0 ) && ( $t_issue->resolution == FIXED ) &&
			( $t_issue->status >= config_get( 'bug_resolved_status_threshold' ) ) );
	}


	# --------------------
	# Prints one entry in the changelog.
	function custom_function_default_changelog_print_issue( $p_issue_id ) {
		$t_bug = bug_get( $p_issue_id );
		echo '- ', string_process_bug_link( '#' . $p_issue_id ), ': <b>[', $t_bug->category, ']</b> ', string_display( $t_bug->summary ), ' (', user_get_name( $t_bug->handler_id ), ')<br />';
	}
?>