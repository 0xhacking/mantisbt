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

	### check whether the user wanted to hide the results
	$f_hide_resolved = get_user_value( $g_mantis_user_pref_table, "hide_resolved" );
	$f_limit_view = get_user_value( $g_mantis_user_pref_table, "limit_view" );

	if ( isset( $g_hide_resolved_val ) ) {
		$f_hide_resolved = $g_hide_resolved_val;
	}
	if ( isset( $g_view_limit_val ) ) {
		$f_limit_view = $g_view_limit_val;
	}

	### basically we toggle between ASC and DESC if the user clicks the
	### same sort order
	if ( isset( $f_dir ) ) {
		if ( $f_dir=="ASC" ) {
			$f_dir = "DESC";
		}
		else {
			$f_dir = "ASC";
		}
	}
	else {
		$f_dir = "DESC";
	}
?>
<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
[ <a href="<? echo $g_view_prefs_page ?>">Viewing Preferences</a> ]
</div>

<p>
<table width=100% bgcolor=<? echo $g_primary_border_color." ".$g_primary_table_tags ?>>
<tr>
	<td bgcolor=<? echo $g_white_color ?>>
	<table width=100% cols=7>
	<tr>
		<td colspan=7 bgcolor=<? echo $g_table_title_color ?>>
			<b>Viewing Bugs</b>
		</td>
	</tr>
	<tr bgcolor=<? echo $g_category_title_color2 ?> align=center>
		<td width=8%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=id&f_dir=<? echo $f_dir?>">ID</a>
			</b>
		</td>
		<td width=3%>
			<b>
				#
			</b>
		</td>
		<td width=12%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=category&f_dir=<? echo $f_dir?>">Category</a>
			</b>
		</td>
		<td width=10%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=severity&f_dir=<? echo $f_dir?>">Severity</a>
			</b>
		</td>
		<td width=10%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=status&f_dir=<? echo $f_dir?>">Status</a>
			</b>
		</td>
		<td width=12%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=last_updated&f_dir=<? echo $f_dir?>">Updated</a>
			</b>
		</td>
		<td width=45%>
			<b>
				<a href="<? echo $g_view_bug_all_page ?>?f_sort=summary&f_dir=<? echo $f_dir?>">Summary</a>
			</b>
		</td>
	</tr>
	<tr height=5>
		<td bgcolor=<? echo $g_white_color ?> colspan=7>
		</td>
	</tr>
	<?
		if ( !isset( $f_offset ) ) {
			$f_offset = 0;
		}
		### build our query string based on our viewing criteria
		$query = "SELECT * FROM $g_mantis_bug_table";
		if ( $f_hide_resolved=="on"  ) {
			$query = $query." WHERE status<>'resolved'";
		}
		if ( !isset( $f_sort ) ) {
				$f_sort="id";
		}
		$query = $query." ORDER BY '$f_sort' $f_dir";
		if ( isset( $f_limit_view ) ) {
			$query = $query." LIMIT $f_offset, $f_limit_view";
		}

		### perform query
	    $result = db_mysql_query( $query );
		$row_count = mysql_num_rows( $result );

		for($i=0; $i < $row_count; $i++) {
			### prefix bug data with v_
			$row = mysql_fetch_array($result);
			extract( $row, EXTR_PREFIX_ALL, "v" );

			$v_summary = string_display( $v_summary );
			$lastupdated = date( "m-d", sql_to_unix_time( $v_last_updated ) );

			### alternate row colors
			if ($i % 2== 0) {
				$status_color=$g_primary_color_light;
			}
			else {
				$status_color=$g_primary_color_dark;
			}

			### choose color based on status only if not resolved
			### The code creates the appropriate variable name
			### then references that color variable
			### You could replace this with a bunch of if... then... else
			#### statments
			if ( $v_status!="resolved" ) {
				$t = "g_".$v_status."_color";
				$status_color = $$t;
			}

			### grab the bugnote count
			$query2 = "SELECT COUNT(id)
						FROM $g_mantis_bugnote_table
						WHERE bug_id ='$v_id'";
			$result2 = db_mysql_query( $query2 );
			$bugnote_count = mysql_result( $result2, 0 );
	?>
	<tr bgcolor=<? echo $status_color ?> align=center>
		<td>
			<a href="<? echo $g_view_bug_page ?>?f_id=<? echo $v_id ?>"><? echo $v_id ?></a>
		</td>
		<td>
			<? if ($bugnote_count > 0) echo $bugnote_count ?>
		</td>
		<td>
			<? echo $v_category ?>
		</td>
		<td>
			<?
				if ( $v_status=="resolved" ) {
					echo $v_severity;
				}
				else {
					if ( ( $v_severity=="major" ) ||
						 ( $v_severity=="crash" ) ||
						 ( $v_severity=="block" ) ) {
						PRINT "<b>";
						echo $v_severity;
						PRINT "</b>";
					}
					else {
						echo $v_severity;
					}
				}
			?>
		</td>
		<td>
			<? echo $v_status ?>
		</td>
		<td>
			<?
				if ( isset( $g_last_access_cookie_val ) ) {
					if ( $v_last_updated >
						$g_last_access_cookie_val ) {
						PRINT "<b>";
						echo $lastupdated;
						PRINT "</b>";
					}
					else {
						echo $lastupdated;
					}
				}
				else {
					echo $lastupdated;
				}
			?>
		</td>
		<td>
			<? echo $v_summary ?>
		</td>
	</tr>
	<?
		}
	?>
	</table>
	</td>
</tr>
</table>

<?
	$f_offset_next = $f_offset + $f_limit_view;
	$f_offset_prev = $f_offset - $f_limit_view;

	if ( $f_offset_prev < 0 ) {
		$f_offset_prev = -1;
	}
?>

<div align=center>
<? if ( $f_offset_prev >= 0 ) { ?>
<a href="<? echo $g_view_bug_all_page ?>?f_offset=<? echo $f_offset_prev ?>">View Prev <? echo $f_limit_view ?></a>
<? } ?>
<? if ( $row_count == $f_limit_view ) { ?>
<a href="<? echo $g_view_bug_all_page ?>?f_offset=<? echo $f_offset_next ?>">View Next <? echo $f_limit_view ?></a>
<? } ?>
</div>

<? print_footer() ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>