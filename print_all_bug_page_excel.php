<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: print_all_bug_page_excel.php,v 1.32 2004-02-05 00:34:37 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	# Excel (97 and above) export page
	# The bugs displayed in print_all_bug_page.php are saved in a .xls file
	# The IE icon allows to see or directly print the same result
?>
<?php
	require_once( 'core.php' );
	
	$t_core_path = config_get( 'core_path' );
	
	require_once( $t_core_path.'current_user_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'string_api.php' );
?>
<?php require( 'print_all_bug_options_inc.php' ) ?>
<?php auth_ensure_user_authenticated() ?>
<?php
	$f_type_page	= gpc_get_string( 'type_page', 'excel' );
	$f_search		= gpc_get_string( 'search', false ); # @@@ need a better default
	$f_offset		= gpc_get_int( 'offset', 0 );
	$f_export		= gpc_get_string( 'export' );
	$f_show_flag	= gpc_get_bool( 'show_flag' );

	# excel or html export
	if ( $f_type_page != 'html' ) {
		$t_export_title = $g_page_title."_excel";
		$t_export_title = ereg_replace( '[\/:*?"<>|]', '', $t_export_title );
		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment; filename="' . $t_export_title . '.xls"' );
	}

	$t_cookie_value = gpc_get_cookie( config_get( 'view_all_cookie' ), '' );

	# check to see if the cookie does not exist
	if ( is_blank( $t_cookie_value ) ) {
		print_header_redirect( 'view_all_set.php?type=0&amp;print=1' );
	}

	# check to see if new cookie is needed
	$t_setting_arr 			= explode( '#', $t_cookie_value );
	if ( $t_setting_arr[0] != $g_cookie_version ) {
		print_header_redirect( 'view_all_set.php?type=0&amp;print=1' );
	}

	#settings for choosing the fields to print
	# get the fields list
	$t_field_name_arr = get_field_names();

	# This is where we used to do the entire actual filter ourselves
	$t_page_number = gpc_get_int( 'page_number', 1 );
	$t_per_page = null;
	$t_bug_count = null;
	$t_page_count = null;
	
	$result = filter_get_bug_rows( $t_page_number, $t_per_page, $t_page_count, $t_bug_count );
	$row_count = sizeof( $result );

	#settings for choosing the fields to print
	# get the fields list
	$t_field_name_arr = get_field_names();
	$field_name_count = count( $t_field_name_arr );

	# get printing preferences
	$t_user_id = auth_get_current_user_id();
	$query3 ="SELECT print_pref ".
			"FROM $g_mantis_user_print_pref_table ".
			"WHERE user_id='$t_user_id' ";

	$result3 = db_query( $query3 );
	$row = db_fetch_array( $result3 );
	$t_prefs = $row[0];
?>

<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">

<?php html_page_top1() ?>
<style id="Classeur1_16681_Styles">
</style>


<?php html_head_end() ?>
<?php html_body_begin() ?>

<div id="Classeur1_16681" align=center x:publishsource="Excel">

<table x:str border=0 cellpadding=0 cellspacing=0 width=100% style='border-collapse:
 collapse'>

<tr>
	<?php
		# titles desactivated for html pages
		if ( $f_type_page != 'html' ) {
			for ( $i=0 ; $i <$field_name_count ; $i++ ) {
				if ( isset( $t_prefs[$i] ) && ( 1 == $t_prefs[$i] ) ) {  ?>
	<td class=xl2316681 style='border-left:none'><?php echo $t_field_name_arr[$i] ?></td>
<?php
				} //if isset
			} //for
		} //if
?>
</tr>

<?php
	$field_name_count = $field_name_count;

	$f_bug_arr = explode_enum_string( $f_export );

	for( $i=0; $i < $row_count; $i++ ) {
		if ( isset( $f_bug_arr[$i] ) ) {
			$index = $f_bug_arr[$i];
			$t_bug_arr_sort[$index]=1;
		}
	}

	for( $i=0; $i < $row_count; $i++ ) {

		# prefix bug data with v_
		extract( $result[$i], EXTR_PREFIX_ALL, 'v' );

		$t_last_updated = date( $g_short_date_format, $v_last_updated );

		# grab the bugnote count
		$bugnote_count = bug_get_bugnote_count( $v_id );

		# grab the project name
		$project_name = project_get_field( $v_project_id, 'name' );

		$query4 = "SELECT *
			FROM $g_mantis_bug_text_table
			WHERE id='$v_bug_text_id'";
		$result4 = db_query( $query4 );
		$row = db_fetch_array( $result4 );
		extract( $row, EXTR_PREFIX_ALL, 'v2' );

		$v_os 						= string_display( $v_os );
		$v_os_build					= string_display( $v_os_build );
		$v_platform					= string_display( $v_platform );
		$v_version 					= string_display( $v_version );
		$v_summary 					= string_display_links( $v_summary );

		# line feeds are desactivated in case of excel export, to avoid multiple lines
		if ( $f_type_page != 'html' ) {
				$v2_description = stripslashes( str_replace( '\n',' ',$v2_description ));
				$v2_steps_to_reproduce  = stripslashes( str_replace( '\n',' ',$v2_steps_to_reproduce ) );
				$v2_additional_information = stripslashes( str_replace( '\n',' ',$v2_additional_information ));
		}
		else {
			$v2_description 			= string_display_links( $v2_description );
			$v2_steps_to_reproduce 		= string_display_links( $v2_steps_to_reproduce );
			$v2_additional_information 	= string_display_links( $v2_additional_information );
		}

		# an index for incrementing the array position
		$name_index=0;

		if ( isset( $t_bug_arr_sort[$i] ) || ( $f_show_flag==0 ) ) {
?>
<tr>
	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_id;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_category;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'severity', $v_severity );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'reproducibility', $v_reproducibility );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo date( config_get( 'normal_date_format' ),$v_date_submitted );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo date( config_get( 'normal_date_format' ),$v_last_updated );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php print_user_with_subject( $v_reporter_id, $v_id ) ;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php print_user_with_subject( $v_handler_id, $v_id ) ;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'priority', $v_priority );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'status', $v_status );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_build;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'projection', $v_projection );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'eta', $v_eta );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_platform;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_os;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_os_build;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_version;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo get_enum_element( 'resolution', $v_resolution );
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_duplicate_id;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v_summary;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v2_description ;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v2_steps_to_reproduce;
			}
	$name_index++;  ?>
	</td>

	<?php if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v2_additional_information;
			}
	$name_index++;  ?>
	</td>

<?php
	if ( ( $name_index < $field_name_count ) && ( !isset( $t_prefs[$name_index] ) || ( 1 == $t_prefs[$name_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
<?php
			$query5 = "SELECT *
					FROM $g_mantis_bug_file_table
					WHERE bug_id='$v_id'";
			$result5 = db_query( $query5 );
			$num_files = db_num_rows( $result5 );
			for ( $j=0;$j<$num_files;$j++ ) {
				$row = db_fetch_array( $result5 );
				extract( $row, EXTR_PREFIX_ALL, 'v2' );
				$v2_filesize = round( $v2_filesize / 1024 );
				$v2_date_added = date( config_get( 'normal_date_format' ), ( $v2_date_added ) );
					switch ( $g_file_upload_method ) {
					case DISK:	PRINT "$v2_filename ($v2_filesize KB) $v2_date_added";
							break;
					case DATABASE:	PRINT "$v2_filename ($v2_filesize KB) $v2_date_added";
							break;
				} #case
				if ( $j != ( $num_files - 1 ) &&  ( $f_type_page == 'html' ) ) {
					PRINT '<br />';
				}
				else {
					PRINT '&nbsp';
				} #if
			} #for loop
		}# if index
	$name_index++;
?>
	</td>
<?php # Bugnotes BEGIN (3 rows) ?>
<td colspan=3>
<?php  # print bugnotes
		# get the bugnote data
		$query6 = "SELECT *,UNIX_TIMESTAMP(date_submitted) as date_submitted
				FROM $g_mantis_bugnote_table
				WHERE bug_id='$v_id'
				ORDER BY date_submitted $g_bugnote_order";
		$result6 = db_query( $query6 );
		$num_notes = db_num_rows( $result6 );

		# save the index, and use an own bugnote_index
		$bugnote_index = $name_index ;

		for ( $k=0; $k < $num_notes; $k++ ) {
			# prefix all bugnote data with v3_
			$row = db_fetch_array( $result6 );
			extract( $row, EXTR_PREFIX_ALL, 'v3' );
			$v3_date_submitted = date( config_get( 'normal_date_format' ), ( $v3_date_submitted ) );

			# grab the bugnote text and id and prefix with v3_
			$query6 = "SELECT note, id
					FROM $g_mantis_bugnote_text_table
					WHERE id='$v3_bugnote_text_id'";
			$result7 = db_query( $query6 );
			$v3_note = db_result( $result7, 0, 0 );
			$v3_bugnote_text_id = db_result( $result7, 0, 1 );

			if ( $f_type_page != 'html' ) {
				$v3_note = stripslashes( str_replace( '\n','|',$v3_note ));
				}
			else {
					$v3_note = string_display_links( $v3_note );
				}
	?>
<table>
<tr>
	<?php if ( ( $bugnote_index < $field_name_count ) && ( !isset( $t_prefs[$bugnote_index] )||( 1 == $t_prefs[$bugnote_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php print_user( $v3_reporter_id ) ;
			}
	$bugnote_index++;  ?>
	</td>

	<?php if ( ( $bugnote_index < $field_name_count ) && ( !isset( $t_prefs[$bugnote_index] )||( 1 == $t_prefs[$bugnote_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v3_date_submitted;
			}
	$bugnote_index++;  ?>
	</td>

	<?php if ( ( $bugnote_index < $field_name_count ) && ( !isset( $t_prefs[$bugnote_index] )||( 1 == $t_prefs[$bugnote_index] ) ) ) { ?>
	<td class=xl2216681 nowrap style='border-top:none;border-left:none'>
	<?php echo $v3_note;
			}
	$bugnote_index++;  ?>
	</td>
</tr>
</table>
	<?php
		# get back
		$bugnote_index = $name_index ;
			} # end for bugnote
	?>
<?php # Bugnotes END ?>

</tr>
<?php
		} #isset
} #for loop
?>
</table>
</div>
