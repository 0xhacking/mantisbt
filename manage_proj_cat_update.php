<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

	# --------------------------------------------------------
	# $Id$
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'category_api.php' );

	helper_ensure_post();

	auth_reauthenticate();

	$f_category_id		= gpc_get_int( 'category_id' );
	$f_project_id		= gpc_get_int( 'project_id' );
	$f_name				= trim( gpc_get_string( 'name' ) );
	$f_assigned_to		= gpc_get_int( 'assigned_to', 0 );

	access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_project_id );

	if ( is_blank( $f_name ) ) {
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}

	$t_row = category_get_row( $f_category_id );
	$t_old_name = $t_row['name'];
	$t_project_id = $t_row['project_id'];

	# check for duplicate
	if ( strtolower( $f_name ) != strtolower( $t_old_name ) ) {
		category_ensure_unique( $t_project_id, $f_name );
	}
	
	category_update( $f_category_id, $f_name, $f_assigned_to );

	if ( $f_project_id == ALL_PROJECTS ) {
		$t_redirect_url = 'manage_proj_page.php';
	} else {
		$t_redirect_url = 'manage_proj_edit_page.php?project_id=' . $f_project_id;
	}

	html_page_top1();

	html_meta_redirect( $t_redirect_url );

	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';

	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
