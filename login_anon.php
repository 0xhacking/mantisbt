<?php
/**
 * anon_login.php
 *
 # Mantis - a php based bugtracking system
 # Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * This file copyright (C) 2002 August Zajonc - augustz@users.sourceforge.net
 * This program is distributed under the terms and conditions of the GPL
 * See the README and LICENSE files for details
 *
 * login_anon.php logs a user in anonymously without having to enter a username
 * or password.
 *
 * Depends on two global configuration variables:
 * $g_allow_anonymous_login - bool which must be true to allow anonymous login.
 * $g_anonymous_account - name of account to login with.
 *
 * TODO:
 * Check how manage account is impacted.
 * Might be extended to allow redirects for bug links etc.
 *
 * @author  August Zajonc - augustz@users.sourceforge.net
 */
	include('core_API.php');

	print_header_redirect( $g_login.'?f_username='.$g_anonymous_account.'&amp;f_perm_login=false' );
?>