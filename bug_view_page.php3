<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
	<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>
<?
	db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

    $query = "SELECT *
    		FROM $g_mantis_user_table
			WHERE cookie_string='$g_string_cookie_val'";
    $result = db_mysql_query($query);
	$row = mysql_fetch_array($result);
	if ( $row ) {
		extract( $row, EXTR_PREFIX_ALL, "u" );
	}

    $query = "SELECT *
    		FROM $g_mantis_bug_table
    		WHERE id='$f_id'";
    $result = db_mysql_query( $query );
	$row_count = mysql_num_rows( $result );

	$row = mysql_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, "v" );

    $query = "SELECT username, email
    		FROM $g_mantis_user_table
    		WHERE id='$v_handler_id'";
    $result = db_mysql_query( $query );
    if ( $result ) {
   		$row = mysql_fetch_array( $result );
		$t_handler_name		= $row["username"];
		$t_handler_email	= $row["email"];
	}

    $query = "SELECT username, email
    		FROM $g_mantis_user_table
    		WHERE id='$v_reporter_id'";
    $result = db_mysql_query( $query );
    if ( $result ) {
   		$row = mysql_fetch_array( $result );
		$t2_handler_name		= $row["username"];
		$t2_handler_email		= $row["email"];
	}

    $query = "SELECT *
    		FROM $g_mantis_bug_text_table
    		WHERE id='$v_bug_text_id'";
    $result = db_mysql_query( $query );
	$row_count = mysql_num_rows( $result );

	$row = mysql_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, "v2" );

	$v_summary = string_unsafe( $v_summary );
	$v2_description = string_unsafe( $v2_description );
	$v2_steps_to_reproduce = string_unsafe( $v2_steps_to_reproduce );
	$v2_additional_information = string_unsafe( $v2_additional_information );
	$v_date_submitted = date( "m-d H:i", sql_to_unix_time( $v_date_submitted ) );
	$v_last_updated = date( "m-d H:i", sql_to_unix_time( $v_last_updated ) );
?>

<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
[ <a href="<? echo $g_bug_view_advanced_page ?>?f_id=<? echo $f_id?>">Advanced View</a> ]
</div>

<p>
<table width=100% bgcolor=<? echo $g_primary_border_color ?>aa>
<tr>
	<td bgcolor=<? echo $g_white_color ?>>
	<table width=100% bgcolor=<? echo $g_white_color ?>>
	<tr>
		<td bgcolor=<? echo $g_white_color ?> colspan=2>
			<b>Viewing Bug Details</b>
		</td>
	</tr>
	<tr bgcolor=<? echo $g_category_title_color ?> align=center>
		<td width=15%>
			<b>ID</b>
		</td>
		<td width=20%>
			<b>Category</b>
		</td>
		<td width=15%>
			<b>Severity</b>
		</td>
		<td width=20%>
			<b>Reproducibility</b>
		</td>
		<td width=15%>
			<b>Date Submitted</b>
		</td>
		<td width=15%>
			<b>Last Update</b>
		</td>
	</tr>
	<tr bgcolor=<? echo $g_primary_color_light ?> align=center>
		<td>
			<? echo $v_id ?>
		</td>
		<td>
			<? echo $v_category ?>
		</td>
		<td>
			<? echo $v_severity ?>
		</td>
		<td>
			<? echo $v_reproducibility ?>
		</td>
		<td>
			<? echo $v_date_submitted ?>
		</td>
		<td>
			<? echo $v_last_updated ?>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?> align=center>
			<b>Reporter</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?> colspan=5>
			<?
				if ( isset( $t2_handler_name ) ) {
					echo "<a href=\"mailto:$t2_handler_email\">".$t2_handler_name."</a>";
				}
				else {
					echo "user no longer exists";
				}
			?>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?> align=center>
			<b>Assigned To</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?> colspan=5>
			<?
				if ( isset( $t_handler_email ) ) {
					echo "<a href=\"mailto:$t_handler_email\">".$t_handler_name."</a>";
				}
				else if ( "0000000"==$v_handler_id ) {
					echo "";
				}
				else {
					echo "user no longer exists";
				}
			?>
		</td>
	</tr>
	<tr align=center>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b>Priority</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?>>
			<? echo $v_priority ?>
		</td>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b>Resolution</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?>>
			<? echo $v_resolution ?>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?> colspan=2>

		</td>
	</tr>
	<tr align=center>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b>Status</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?>>
			<? echo $v_status ?>
		</td>
		<td bgcolor=<? echo $g_category_title_color ?>>
			<b>Duplicate ID</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?>>
			<?
				if ( $v_duplicate_id!='0000000' ) {
					echo "<a href=\"$g_bug_view_page?f_id=$v_duplicate_id\">".$v_duplicate_id."</a>";
				}
			?>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?> colspan=2>

		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?> align=center>
			<b>Summary</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?> colspan=5>
			<? echo $v_summary ?>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?> align=center>
			<b>Description</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_light ?> colspan=5>
			<? echo $v2_description ?>
		</td>
	</tr>
	<tr>
		<td bgcolor=<? echo $g_category_title_color ?> align=center>
			<b>Additional<br>Information</b>
		</td>
		<td bgcolor=<? echo $g_primary_color_dark ?> colspan=5>
			<? echo $v2_additional_information ?>
		</td>
	</tr>
<?
	if ( ( $u_access_level=="administrator" ) ||
		 ( $u_access_level=="developer" ) ||
		 ( $u_access_level=="updater" ) ) {
?>
	<tr align=center>
	<form method=post action="<? echo $g_bug_update_page ?>">
		<input type=hidden name=f_id value="<? echo $f_id ?>">
		<input type=hidden name=f_bug_text_id value="<? echo $v_bug_text_id ?>">
		<td valign=top bgcolor=<? echo $g_white_color ?> colspan=4>
			<input type=submit value=" Update Bug Information ">
		</td>
		</form>
		<form method=post action="<? echo $g_bug_delete_page ?>">
		<input type=hidden name=f_id value="<? echo $f_id ?>">
		<td valign=top bgcolor=<? echo $g_white_color ?> colspan=2>
			<input type=submit value=" Delete Bug ">
		</td>
	</form>
	</tr>
<?
	}
?>
	</table>
	</td>
</tr>
</table>

<p>
<? include( $g_bugnote_include_file ) ?>

<? print_footer() ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>