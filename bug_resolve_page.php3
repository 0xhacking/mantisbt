<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

/*	### get date submitted (weird bug in mysql)
	$query = "SELECT date_submitted
			FROM $g_mantis_bug_table
    		WHERE id='$f_id'";
   	$result = mysql_query( $query );
   	$t_date_submitted = mysql_result( $result, 0 );

	### Update fields
    $query = "UPDATE $g_mantis_bug_table
    		SET status='feedback',
				resolution='reopened',
				date_submitted='$t_date_submitted',
				last_updated=NOW()
    		WHERE id='$f_id'";
   	$result = mysql_query($query);*/
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>
<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
<table width=50% bgcolor=<? echo $g_primary_border_color." ".$g_primary_table_tags ?>>
<tr>
	<td bgcolor=<? echo $g_white_color ?>>
	<table cols=2 width=100%>
	<form method=post action="<? echo $g_bug_resolve_page2 ?>">
	<input type=hidden name=f_id value="<? echo $f_id ?>">
	<tr>
		<td colspan=2 bgcolor=<? echo $g_table_title_color ?>>
			<b><? echo $s_resolve_bug_title ?></b>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b><? echo $s_resolution ?></b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?>>
			<select name=f_resolution>
				<? print_field_option_list( "resolution", $v_resolution ) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b><? echo $s_duplicate_id ?></b>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?>>
			<select name=f_duplicate_id>
				<? print_duplicate_id_option_list( $v_duplicate_id ) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=<? echo $g_primary_color_light ?> align=center>
			<input type=submit value="<? echo $s_resolve_bug_button ?>">
		</td>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>
</div>

<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>