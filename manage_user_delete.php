<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_user_delete.php,v 1.23 2003-02-11 09:08:47 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php require_once( 'core.php' ) ?>
<?php auth_ensure_user_authenticated() ?>
<?php
	check_access( config_get( 'manage_user_threshold' ) );
	
	$f_user_id	= gpc_get_int( 'user_id' );

	helper_ensure_confirmed( lang_get( 'delete_account_sure_msg' ),
							 lang_get( 'delete_account_button' ) );

	user_delete( $f_user_id );

    $t_redirect_url = 'manage_user_page.php';

	print_page_top1();

	print_meta_redirect( $t_redirect_url, $g_wait_time );

	print_page_top2();
?>

<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';

	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php print_page_bot1( __FILE__ ) ?>
