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
	check_access( ADMINISTRATOR );
	
	$f_id = gpc_get_int( 'f_id' );

	$t_result = user_reset_password( $f_id );

	$t_redirect_url = 'manage_page.php';
?>
<?php print_page_top1() ?>
<?php
	if ( $result ) {
		print_meta_redirect( $t_redirect_url );
	}
?>
<?php print_page_top2() ?>

<br />
<div align="center">
<?php
	if ( false == $t_result ) {				# PROTECTED
		echo lang_get( 'account_reset_protected_msg' ).'<br />';
	} else {					# SUCCESS
		if ( ON == config_get( 'send_reset_password' ) ) {
			echo lang_get( 'account_reset_msg' ).'<br />';
		} else {
			echo lang_get( 'account_reset_msg2' ).'<br />';
		}
	}

	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php print_page_bot1( __FILE__ ) ?>
