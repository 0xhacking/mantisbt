<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?
	### This page allos users to add a new profile which is POSTed to
	### account_prof_add.php3

	### Users can also manage their profiles
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	check_access( REPORTER );
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>
<? print_top_page( $g_top_include_page ) ?>

<? print_menu( $g_menu_include_file ) ?>

<? print_account_menu( $g_account_profile_menu_page ) ?>

<? ### Add Profile Form BEGIN ?>
<p>
<div align="center">
<table width="75%" bgcolor="<? echo $g_primary_border_color ?>" <? echo $g_primary_table_tags ?>>
<tr>
	<td bgcolor="<? echo $g_white_color ?>">
	<table cols="2" width="100%">
	<form method="post" action="<? echo $g_account_profile_add ?>">
	<input type="hidden" name="f_user_id" value="<? echo get_current_user_field( "id " ) ?>">
	<tr>
		<td colspan="2" bgcolor="<? echo $g_table_title_color ?>">
			<b><? echo $s_add_profile_title ?></b>
		</td>
	</tr>
	<tr bgcolor="<? echo $g_primary_color_dark ?>">
		<td width="25%">
			<? echo $s_platform ?>
		</td>
		<td width="75%">
			<input type="text" name="f_platform" size="32" maxlength="32">
		</td>
	</tr>
	<tr bgcolor="<? echo $g_primary_color_light ?>">
		<td>
			<? echo $s_operating_system ?>
		</td>
		<td>
			<input type="text" name="f_os" size="32" maxlength="32">
		</td>
	</tr>
	<tr bgcolor="<? echo $g_primary_color_dark ?>">
		<td>
			<? echo $s_version ?>
		</td>
		<td>
			<input type="text" name="f_os_build" size="16" maxlength="16">
		</td>
	</tr>
	<tr bgcolor="<? echo $g_primary_color_light ?>">
		<td>
			<? echo $s_additional_description ?>
		</td>
		<td>
			<textarea name="f_description" cols="60" rows="8" wrap="virtual"></textarea>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" value="<? echo $s_add_profile_button ?>">
		</td>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>
</div>
<? ### Add Profile Form END ?>

<? ### Edit or Delete Profile Form BEGIN ?>
<p>
<div align="center">
<table width="75%" bgcolor="<? echo $g_primary_border_color ?>" <? echo $g_primary_table_tags ?>>
<tr>
	<td bgcolor="<? echo $g_white_color ?>">
	<table width="100%">
	<form method="post" action="<? echo $g_account_profile_edit_page ?>">
	<tr>
		<td colspan="2" bgcolor="<? echo $g_table_title_color ?>">
			<b><? echo $s_edit_or_delete_profiles_title ?></b>
		</td>
	</tr>
	<tr bgcolor="<? echo $g_primary_color_dark ?>">
		<td align="center" colspan="2">
			<input type="radio" name="f_action" value="edit" CHECKED> <? echo $s_edit_profile ?>
			<input type="radio" name="f_action" value="make default"> <? echo $s_make_default ?>
			<input type="radio" name="f_action" value="delete"> <? echo $s_delete_profile ?>
		</td>
	</tr>
	<tr align="center" bgcolor="<? echo $g_primary_color_light ?>">
		<td valign="top" width="25%">
			<? echo $s_select_profile ?>
		</td>
		<td width="75%">
			<select name="f_id">
				<? print_profile_option_list( get_current_user_field( "id " ) ) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" value="<? echo $s_submit_button ?>">
		</td>
		</form>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
<? ### Edit or Delete Profile Form END ?>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>