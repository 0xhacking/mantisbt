<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php require_once( 'core.php' ) ?>
<?php
	# Check for invalid access to signup page
	if ( OFF == $g_allow_signup ) {
		print_header_redirect( 'login_page.php' );
		exit;
	}
?>
<?php print_page_top1() ?>
<?php
	print_head_bottom();
	print_body_top();
	print_header( $g_page_title );
	print_top_page( $g_top_include_page );
?>

<br />
<div align="center">
<?php echo lang_get( 'signup_info' ) ?>
</div>

<?php # Signup form BEGIN ?>
<br />
<div align="center">
<form method="post" action="signup.php">
<table class="width50" cellspacing="1">
<tr>
	<td class="form-title">
		<?php echo lang_get( 'signup_title' ) ?>
	</td>
	<td class="right">
		<?php print_bracket_link( 'login_page.php', lang_get( 'go_back' ) ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="30%">
		<?php echo lang_get( 'username' ) ?>:
	</td>
	<td width="70%">
		<input type="text" name="username" size="32" maxlength="32" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'email' ) ?>:
	</td>
	<td>
		<?php print_email_input( 'email', '' ) ?>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" value="<?php echo lang_get( 'signup_button' ) ?>" />
	</td>
</tr>
</table>
</form>
</div>
<?php # Signup form END ?>

<?php print_page_bot1( __FILE__ ) ?>
