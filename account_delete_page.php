<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: account_delete_page.php,v 1.17 2002-09-22 05:57:18 jfitzell Exp $
	# --------------------------------------------------------

	# CALLERS
	#	This page is submitted to by the following pages:
	#	- account_page.php

	# EXPECTED BEHAVIOUR
	#	- Prompt the user, asking whether they wish to delete their account
	#	- Upon confirmation, submit to account_delete.php

	# RESTRICTIONS & PERMISSIONS
	#	- User must be authenticated
	#	- allow_account_delete config option must be enabled
	#	- The user's account must not be protected

	require_once( 'core.php' );

	#============ Variables ============
	# (none)

	#============ Permissions ============
	login_cookie_check();

	if ( OFF == config_get( 'allow_account_delete' ) ) {
		print_header_redirect( 'account_page.php' );
	}

	current_user_ensure_unprotected();
?>
<?php

	print_page_top1();
	print_page_top2();

?>
<br />
<div align="center">
<?php

	print_hr();
	
	echo lang_get( 'confirm_delete_msg' );

?>
<form method="post" action="account_delete.php">
	<input type="submit" value="<?php echo lang_get( 'delete_account_button' ) ?>" />
</form>
<?php

	print_hr();

?>
</div>

<?php print_page_bot1( __FILE__ ) ?>
