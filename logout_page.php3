<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?
	# Removes all the cookies and then redirect to $g_logout_redirect_page
?>
<? include( "core_API.php" ); ?>
<?
	# delete cookies then redirect to $g_logout_redirect_page
	setcookie( $g_string_cookie );
	setcookie( $g_project_cookie );
	setcookie( $g_view_all_cookie );
	setcookie( $g_manage_cookie );

	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
?>
<? print_page_top1() ?>
<? print_meta_redirect( $g_logout_redirect_page, $g_wait_time ) ?>
<? print_page_top2a() ?>

<p>
<div align="center">
<table class="width50" cellspacing="0">
<tr>
	<td class="form-title">
		<? echo $s_logged_out_title ?>
	</td>
</tr>
<tr class="row-1">
	<td class="right">
		<? echo $s_redirecting ?> <a href="<? echo $g_logout_redirect_page ?>"><? echo $s_here ?></a>
	</td>
</tr>
</table>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>