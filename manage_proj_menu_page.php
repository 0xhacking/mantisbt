<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	check_access( MANAGER );

	$f_sort = gpc_get_string( 'f_sort', 'name' );
	$f_dir = gpc_get_string( 'f_sort', 'ASC' );

	$c_sort = db_prepare_string( $f_sort );

	if ( 'ASC' == $f_dir ) {
		$c_dir = 'ASC';
	} else {
		$c_dir = 'DESC';
	}

?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<?php print_manage_menu( 'manage_proj_menu_page.php' ) ?>

<?php if ( access_level_check_greater_or_equal ( ADMINISTRATOR ) ) { # Add Project Form BEGIN ?>
<br />
<div align="center">
<form method="post" action="manage_proj_add.php">
<table class="width75" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<?php echo lang_get( 'add_project_title' ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="25%">
		<?php echo lang_get( 'project_name' )?>
	</td>
	<td width="75%">
		<input type="text" name="f_name" size="64" maxlength="128" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'status' ) ?>
	</td>
	<td>
		<select name="f_status">
		<?php print_enum_string_option_list( 'project_status' ) ?>
		</select>
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
		<select name="f_view_state">
			<?php print_enum_string_option_list( 'view_state' ) ?>
		</select>
	</td>
</tr>
<?php
	if ( config_get( 'allow_file_upload' ) ) {
	?>
		<tr class="row-2">
			<td class="category">
				<?php echo lang_get( 'upload_file_path' ) ?>
			</td>
			<td>
				<input type="text" name="f_file_path" size="70" maxlength="250" />
			</td>
		</tr>
		<?php
	}
?>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td>
		<textarea name="f_description" cols="60" rows="5" wrap="virtual"></textarea>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" value="<?php echo lang_get( 'add_project_button' ) ?>" />
	</td>
</tr>
</table>
</form>
</div>
<?php } # Add Project Form END ?>

<?php # Project Menu Form BEGIN ?>
<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="5">
		<?php echo lang_get( 'projects_title' ) ?>
	</td>
</tr>
<tr class="row-category">
	<td width="20%">
		<?php print_manage_project_sort_link(  'manage_proj_menu_page.php', lang_get( 'name' ), 'name', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'name' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_menu_page.php', lang_get( 'status' ), 'status', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'status' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_menu_page.php', lang_get( 'enabled' ), 'enabled', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'enabled' ) ?>
	</td>
	<td width="10%">
		<?php print_manage_project_sort_link(  'manage_proj_menu_page.php', lang_get( 'view_status' ), 'view_state', $c_dir, $c_sort ) ?>
		<?php print_sort_icon( $c_dir, $c_sort, 'view_state' ) ?>
	</td>
	<td width="40%">
		<?php print_manage_project_sort_link(  'manage_proj_menu_page.php', lang_get( 'description' ), 'description', $c_dir, $c_sort ) ?>
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
		<a href="manage_proj_edit_page.php?f_project_id=<?php echo $v_id ?>"><?php echo $v_name ?></a>
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

<?php # Attribute customization is for all projects at the moment
	  # Place the following code in manage_proj_edit_page.php when Per Project Settings are ok ?>
<?php # Attributes customization BEGIN
if (($g_customize_attributes)&&(access_level_check_greater_or_equal( ADMINISTRATOR ) ) ) {
	?>
	<br />
	<div align="center">
	<table class="width75" cellspacing="1">
	<tr>
		<td class="form-title" colspan="2">
			<?php echo lang_get( 'attribute_customization_title' ) ?>
		</td>
	</tr>
<?php
	# to be deleted when moving to manage_project_page.php
	$t_project_id = '0000000';

	#priorities
	attribute_display('priorities', lang_get( 'priority' ), $t_project_id) ;
	#severities
	attribute_display('severities', lang_get( 'severity' ), $t_project_id) ;
	#reproducibilities
	attribute_display('reproducibilities', lang_get( 'reproducibility' ), $t_project_id) ;
	#status
	attribute_display('states', lang_get( 'status' ), $t_project_id) ;
	#resolutions
	attribute_display('resolutions', lang_get( 'resolution' ), $t_project_id) ;
	#projections
	attribute_display('projections', lang_get( 'projection' ), $t_project_id) ;
	#etas
	attribute_display('etas', lang_get( 'eta' ), $t_project_id) ;
}
?>
</table>
</div>
<?php # Attributes customization END ?>

<?php print_page_bot1( __FILE__ ) ?>
