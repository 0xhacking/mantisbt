<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_proj_page.php,v 1.3 2003-02-09 10:20:38 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );
	
	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'icon_api.php' );
?>
<?php login_cookie_check() ?>
<?php
	check_access( config_get( 'manage_project_threshold' ) );

	$f_sort	= gpc_get_string( 'sort', 'name' );
	$f_dir	= gpc_get_string( 'sort', 'ASC' );

	$c_sort = db_prepare_string( $f_sort );

	if ( 'ASC' == $f_dir ) {
		$c_dir = 'ASC';
	} else {
		$c_dir = 'DESC';
	}

?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<?php print_manage_menu( 'manage_proj_page.php' ) ?>

<?php # Project Menu Form BEGIN ?>
<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="5">
		<?php echo lang_get( 'projects_title' ) ?>
		<?php
		if ( access_level_check_greater_or_equal ( config_get( 'create_project_threshold' ) ) ) {
			print_bracket_link( 'manage_proj_create_page.php', lang_get( 'create_new_project_link' ) );
		}
		?>
	</td>
</tr>
<tr class="row-category">
	<td width="20%">
		<?php print_manage_project_sort_link(  'manage_proj_page.php', lang_get( 'name' ), 'name', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'name' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_page.php', lang_get( 'status' ), 'status', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'status' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_page.php', lang_get( 'enabled' ), 'enabled', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'enabled' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_page.php', lang_get( 'view_status' ), 'view_state', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'view_state' ) ?>
	</td>
	<td width="40%">
		<?php print_manage_project_sort_link(  'manage_proj_page.php', lang_get( 'description' ), 'description', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'description' ) ?>
	</td>
</tr>
<?php
	$query = "SELECT *
			FROM $g_mantis_project_table
			ORDER BY '$c_sort' $c_dir";
	$result = db_query( $query );
	$project_count = db_num_rows( $result );
	for ($i=0;$i<$project_count;$i++) {
		$row = db_fetch_array( $result );
		extract( $row, EXTR_PREFIX_ALL, 'v' );

        if ( !access_level_ge_no_default_for_private ( MANAGER, $v_id ) ) {
		  continue;
		}

		$v_name 		= string_display( $v_name );
		$v_description 	= string_display( $v_description );

?>
<tr <?php echo helper_alternate_class( $i ) ?>>
	<td>
		<a href="manage_proj_edit_page.php?project_id=<?php echo $v_id ?>"><?php echo $v_name ?></a>
	</td>
	<td>
		<?php echo get_enum_element( 'project_status', $v_status ) ?>
	</td>
	<td>
		<?php echo trans_bool( $v_enabled ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'project_view_state', $v_view_state ) ?>
	</td>
	<td>
		<?php echo $v_description ?>
	</td>
</tr>
<?php
	}
?>
</table>
<?php # Project Menu Form END ?>

<?php print_page_bot1( __FILE__ ) ?>
