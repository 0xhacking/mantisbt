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

	if ( !access_level_check_greater_or_equal( "manager" ) ) {
		### need to replace with access error page
		header( "Location: $g_logout_page" );
		exit;
	}

	### " character poses problem when editting so let's just convert them to '
	$f_headline	= string_safe( str_replace( "\"", "'", $f_headline ) );
	$f_body		= string_safe( $f_body );

	### Update entry
	$query = "UPDATE $g_mantis_news_table
			SET headline='$f_headline', body='$f_body',
				date_posted='$f_date_posted', last_modified=NOW()
    		WHERE id='$f_id'";
    $result = db_query( $query );
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
<?
	### SUCCESS
	if ( $result ) {
		PRINT "$s_news_updated_msg<p>";

		$t_headline  = string_display( $f_headline );
		$t_body      = stripslashes( string_display_with_br( $f_body ) );
?>
<p>
<div align=center>
<table width=75% bgcolor=<? echo $g_primary_border_color." ".$g_primary_table_tags ?>>
<tr>
	<td bgcolor=<? echo $g_primary_color_dark ?>>
		<b><? echo string_unsafe( $t_headline ) ?></b>
	</td>
</tr>
<tr>
	<td bgcolor=<? echo $g_primary_color_light ?>>
		<br>
		<blockquote>
			<? echo $t_body ?>
		</blockquote>
	</td>
</tr>
</table>
</div>
<?
	}
	### FAILURE
	else {
		PRINT "$s_sql_error_detected <a href=\"mailto:<? echo $g_administrator_email ?>\">administrator</a><p>";
	}
?>
<p>
<a href="<? echo $g_news_menu_page ?>"><? echo $s_proceed ?></a>
</div>

<? print_bottom_page( $g_bottom_include_page ) ?>
<? print_footer(__FILE__) ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>