<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

	### grab the user id
	$u_id = get_current_user_field( "id " );

	### Grab the data
    $query = "SELECT *
    		FROM $g_mantis_user_pref_table
			WHERE user_id='$u_id'";
    $result = db_query($query);

    ## OOPS, No entry in the database yet.  Lets make one
    if ( db_num_rows( $result )==0 ) {

		### Create row
	    $query = "INSERT
	    		INTO $g_mantis_user_pref_table
	    		(id, user_id, advanced_report, advanced_view)
	    		VALUES
	    		(null, '$u_id',
	    		'$g_default_advanced_report', '$g_default_advanced_view')";
	    $result = db_query($query);

		### Rerun select query
	    $query = "SELECT *
	    		FROM $g_mantis_user_pref_table
				WHERE user_id='$u_id'";
	    $result = db_query($query);
    }

    ### prefix data with u_
	$row = db_fetch_array($result);
	extract( $row, EXTR_PREFIX_ALL, "u" );
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

<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
	[ <a href="<? echo $g_account_page ?>"><? echo $s_account_link ?></a> ]
	[ <a href="<? echo $g_account_profile_manage_page ?>"><? echo $s_manage_profiles_link ?></a> ]
	[ <? echo $s_change_preferences_link ?> ]
</div>

<p>
<div align=center>
<table width=50% bgcolor=<? echo $g_primary_border_color." ".$g_primary_table_tags ?>>
<tr>
	<td bgcolor=<? echo $g_white_color ?>>
	<table width=100% cols=2>
	<form method=post action="<? echo $g_account_prefs_update ?>">
		<input type=hidden name=f_id value="<? echo $u_id ?>">
		<input type=hidden name=f_user_id value="<? echo $u_user_id ?>">
	<tr>
		<td colspan=2 bgcolor=<? echo $g_table_title_color ?>>
			<b><? echo $s_default_account_preferences_title ?></b>
		</td>
	</tr>
	<tr bgcolor=<? echo $g_primary_color_light ?>>
		<td>
			<? echo $s_advanced_report ?>
		</td>
		<td>
			<input type=checkbox name=f_advanced_report <? if ( $u_advanced_report=="on" ) echo "CHECKED"?>
		</td>
	</tr>
	<tr bgcolor=<? echo $g_primary_color_dark ?>>
		<td>
			<? echo $s_advanced_view ?>
		</td>
		<td>
			<input type=checkbox name=f_advanced_view <? if ( $u_advanced_view=="on" ) echo "CHECKED"?>
		</td>
	</tr>
	<tr align=center>
		<td>
			<input type=submit value="<? echo $s_update_prefs_button ?>">
		</td>
		</form>
		<form method=post action="<? echo $g_account_prefs_reset ?>">
			<input type=hidden name=f_id value="<? echo $u_id ?>">
		<td>
			<input type=submit value="<? echo $s_reset_prefs_button ?>">
		</td>
		</form>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>