<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: account_page.php,v 1.26 2002-09-22 09:35:06 jfitzell Exp $
	# --------------------------------------------------------

	# CALLERS
	#	This page is called from:
	#	- print_menu()
	#	- print_account_menu()
	#	- header redirects from account_*.php

	# EXPECTED BEHAVIOUR
	#	- Display the user's current settings
	#	- Submit changes in these settings to account_update.php

	# RESTRICTIONS & PERMISSIONS
	#	- User must be authenticated
	#	- The user's account must not be protected

	require_once( 'core.php' );

	#============ Variables ============
	# (none)

	#============ Permissions ============
	login_cookie_check();

	current_user_ensure_unprotected();
?>
<?php

	# extracts the user information for the currently logged in user
	# and prefixes it with u_
    $row = user_get_row( auth_get_current_user_id() );
	extract( $row, EXTR_PREFIX_ALL, 'u' );

	$t_ldap = ( LDAP == config_get( 'login_method' ) );

	# In case we're using LDAP to get the email address... this will pull out
	#  that version instead of the one in the DB
	$u_email = user_get_email( $u_id, $u_username );

	print_page_top1();
	print_page_top2();
?>

<!-- # Edit Account Form BEGIN -->
<br />
<div align="center">
<table class="width75" cellspacing="1">

	<!-- Headings -->
	<tr>
		<td class="form-title">
			<form method="post" action="account_update.php">
			<?php echo lang_get( 'edit_account_title' ) ?>
		</td>
		<td class="right">
			<?php print_account_menu( 'account_page.php' ) ?>
		</td>
	</tr>

<?php if ( $t_ldap ) { ?> <!-- With LDAP -->

	<!-- Username -->
	<tr class="row-1">
		<td class="category" width="25%">
			<?php echo lang_get( 'username' ) ?>:
		</td>
		<td width="75%">
			<?php echo $u_username ?>
		</td>
	</tr>

	<!-- Password -->
	<tr class="row-2">
		<td colspan="2">
			The password settings are controlled by your LDAP entry,<br />
			hence cannot be edited here.
		</td>
	</tr>

<?php } else { ?> <!-- Without LDAP -->

	<!-- Username -->
	<tr class="row-1">
		<td class="category" width="25%">
			<?php echo lang_get( 'username' ) ?>:
		</td>
		<td width="75%">
			<?php echo $u_username ?>
		</td>
	</tr>

	<!-- Password -->
	<tr class="row-2">
		<td class="category">
			<?php echo lang_get( 'password' ) ?>:
		</td>
		<td>
			<input type="password" size="32" maxlength="32" name="f_password" />
		</td>
	</tr>

	<!-- Password confirmation -->
	<tr class="row-2">
		<td class="category">
			<?php echo lang_get( 'confirm_password' ) ?>:
		</td>
		<td>
			<input type="password" size="32" maxlength="32" name="f_password_confirm" />
		</td>
	</tr>

<?php } ?> <!-- End LDAP conditional -->

<?php if ( $t_ldap && ON == config_get( 'use_ldap_email' ) ) { ?> <!-- With LDAP Email-->

	<!-- Email -->
	<tr class="row-1">
		<td class="category">
			<?php echo lang_get( 'email' ) ?>:
		</td>
		<td>
			<?php echo $u_email ?>
		</td>
	</tr>

<?php } else { ?> <!-- Without LDAP Email -->

	<!-- Email -->
	<tr class="row-1">
		<td class="category">
			<?php echo lang_get( 'email' ) ?>:
		</td>
		<td>
			<?php print_email_input( 'f_email', $u_email ) ?>
		</td>
	</tr>

<?php } ?> <!-- End LDAP Email conditional -->

	<!-- Access level -->
	<tr class="row-2">
		<td class="category">
			<?php echo lang_get( 'access_level' ) ?>:
		</td>
		<td>
			<?php echo get_enum_element( 'access_levels', $u_access_level ) ?>
		</td>
	</tr>

	<!-- Project access level -->
	<tr class="row-1">
		<td class="category">
			<?php echo lang_get( 'access_level_project' ) ?>:
		</td>
		<td>
			<?php echo get_enum_element( 'access_levels', current_user_get_access_level() ) ?>
		</td>
	</tr>

	<!-- Assigned project list -->
	<tr class="row-2" valign="top">
		<td class="category">
			<?php echo lang_get( 'assigned_projects' ) ?>:
		</td>
		<td>
			<?php print_project_user_list( current_user_get_field( 'id' ) ) ?>
		</td>
	</tr>

	<!-- BUTTONS -->
	<tr>
		<!-- Update Button -->
		<td class="left">
			<input type="submit" value="<?php echo lang_get( 'update_user_button' ) ?>" />
			</form>
		</td>
		<?php
		# check if users can't delete their own accounts
		if ( ON == config_get( 'allow_account_delete' ) ) { ?>
			<!-- Delete Button -->
			<td class="right">
				<form method="post" action="account_delete_page.php">
				<input type="submit" value="<?php echo lang_get( 'delete_account_button' ) ?>" />
				</form>
			</td>
		<?php } ?>
	</tr>

</table>
</div>

<?php print_page_bot1( __FILE__ ) ?>
