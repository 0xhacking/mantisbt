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

	$f_project_id = gpc_get_int( 'f_project_id' );
	$f_version = gpc_get_string( 'f_version' );
?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<br />
<div align="center">
	<?php print_hr( $g_hr_size, $g_hr_width ) ?>
	<?php echo lang_get( 'version_delete_sure' ) ?>

	<form method="post" action="manage_proj_ver_delete.php">
		<input type="hidden" name="f_project_id" value="<?php echo $f_project_id ?>" />
		<input type="hidden" name="f_version" value="<?php echo $f_version ?>" />
		<input type="submit" value="<?php echo lang_get( 'delete_version_button' ) ?>" />
	</form>

	<?php print_hr( $g_hr_size, $g_hr_width ) ?>
</div>

<?php print_page_bot1( __FILE__ ) ?>
