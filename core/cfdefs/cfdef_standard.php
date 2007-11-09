<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

	# --------------------------------------------------------
	# $Id$
	# --------------------------------------------------------


	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_STRING ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => null,
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_textbox',
		'#function_string_value' => null,
		'#function_string_value_for_email' => null,
	);
	
	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_NUMERIC ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => null,
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_textbox',
		'#function_string_value' => null,
		'#function_string_value_for_email' => null,
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_FLOAT ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => null,
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_textbox',
		'#function_string_value' => null,
		'#function_string_value_for_email' => null,
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_ENUM ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => 'cfdef_prepare_list_distinct_values',
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_list',
		'#function_string_value' => 'cfdef_prepare_list_value',
		'#function_string_value_for_email' => 'cfdef_prepare_list_value_for_email',
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_EMAIL ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => null,
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_textbox',
		'#function_string_value' => 'cfdef_prepare_email_value',
		'#function_string_value_for_email' => 'cfdef_prepare_email_value_for_email',
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_CHECKBOX ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => 'cfdef_prepare_list_distinct_values',
		'#function_value_to_database' => 'cfdef_prepare_list_value_to_database',
		'#function_database_to_value' => 'cfdef_prepare_list_database_to_value',
		'#function_print_input' => 'cfdef_input_checkbox',
		'#function_string_value' => 'cfdef_prepare_list_value',
		'#function_string_value_for_email' => 'cfdef_prepare_list_value_for_email',
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_LIST ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => 'cfdef_prepare_list_distinct_values',
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_list',
		'#function_string_value' => 'cfdef_prepare_list_value',
		'#function_string_value_for_email' => 'cfdef_prepare_list_value_for_email',
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_MULTILIST ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => 'cfdef_prepare_list_distinct_values',
		'#function_value_to_database' => 'cfdef_prepare_list_value_to_database',
		'#function_database_to_value' => 'cfdef_prepare_list_database_to_value',
		'#function_print_input' => 'cfdef_input_list',
		'#function_string_value' => 'cfdef_prepare_list_value',
		'#function_string_value_for_email' => 'cfdef_prepare_list_value_for_email',
	);

	$g_custom_field_type_definition[ CUSTOM_FIELD_TYPE_DATE ] = array ( 
		'#display_possible_values' => TRUE,
		'#display_valid_regexp' => TRUE,
		'#display_length_min' => TRUE,
		'#display_length_max' => TRUE,
		'#display_default_value' => TRUE,
		'#function_return_distinct_values' => null,
		'#function_value_to_database' => null,
		'#function_database_to_value' => null,
		'#function_print_input' => 'cfdef_input_date',
		'#function_string_value' => 'cfdef_prepare_date_value',
		'#function_string_value_for_email' => 'cfdef_prepare_date_value_for_email',
	);

	function cfdef_prepare_list_database_to_value($p_value) {
		return str_replace( '||', '', '|' . $p_value . '|' );
	}

	function cfdef_prepare_list_value_for_email($p_value) {
		return str_replace( '|', ', ', $p_value );
	}

	function cfdef_prepare_email_value_for_email($p_value) {
		return 'mailto:'.$p_value;
	}

	function cfdef_prepare_date_value_for_email($p_value) {
		if ($p_value != null) {
			return date( config_get( 'short_date_format' ), $p_value) ;
		}		
	}

	#string_custom_field_value
	function cfdef_prepare_list_value($p_value) {
		return str_replace( '|', ', ', $p_value );
	}

	function cfdef_prepare_email_value($p_value) {
		return "<a href=\"mailto:$p_value\">$p_value</a>";
	}

	function cfdef_prepare_date_value($p_value) {
		if ($p_value != null) {
			return date( config_get( 'short_date_format'), $p_value);
		}		
	}

	
	#print_custom_field_input

	function cfdef_input_list($p_field_def, $t_custom_field_value) {
		$t_values = explode( '|', custom_field_prepare_possible_values( $p_field_def['possible_values'] ) );
		$t_list_size = $t_possible_values_count = count( $t_values );
		
		if ( $t_possible_values_count > 5 ) {
			$t_list_size = 5;
		}

		if ( $p_field_def['type'] == CUSTOM_FIELD_TYPE_ENUM ) {
			$t_list_size = 0;	# for enums the size is 0
		}

		if ( $p_field_def['type'] == CUSTOM_FIELD_TYPE_MULTILIST ) {
			echo '<select ', helper_get_tab_index(), ' name="custom_field_' . $p_field_def['id'] . '[]" size="' . $t_list_size . '" multiple="multiple">';
		} else {
			echo '<select ', helper_get_tab_index(), ' name="custom_field_' . $p_field_def['id'] . '" size="' . $t_list_size . '">';
		}
		
		$t_selected_values = explode( '|', $t_custom_field_value );
 		foreach( $t_values as $t_option ) {
			if( in_array( $t_option, $t_selected_values, true ) ) {
 				echo '<option value="' . $t_option . '" selected="selected"> ' . $t_option . '</option>';
 			} else {
 				echo '<option value="' . $t_option . '">' . $t_option . '</option>';
 			}
 		}
 		echo '</select>';
	}
	
	function cfdef_input_checkbox($p_field_def, $t_custom_field_value) {
		$t_values = explode( '|', custom_field_prepare_possible_values( $p_field_def['possible_values'] ) );
		$t_checked_values = explode( '|', $t_custom_field_value );
		foreach( $t_values as $t_option ) {
			echo '<input ', helper_get_tab_index(), ' type="checkbox" name="custom_field_' . $p_field_def['id'] . '[]"';
			if( in_array( $t_option, $t_checked_values, true ) ) {
				echo ' value="' . $t_option . '" checked="checked">&nbsp;' . $t_option . '&nbsp;&nbsp;';
			} else {
				echo ' value="' . $t_option . '">&nbsp;' . $t_option . '&nbsp;&nbsp;';
			}
		}
	}

	function cfdef_input_textbox($p_field_def, $t_custom_field_value) {
		echo '<input ', helper_get_tab_index(), ' type="text" name="custom_field_' . $p_field_def['id'] . '" size="80"';
		if( 0 < $p_field_def['length_max'] ) {
			echo ' maxlength="' . $p_field_def['length_max'] . '"';
		} else {
			echo ' maxlength="255"';
		}
		echo ' value="' . $t_custom_field_value .'"></input>';
	}	

	function cfdef_input_date($p_field_def, $t_custom_field_value) {
		print_date_selection_set("custom_field_" . $p_field_def['id'], config_get('short_date_format'), $t_custom_field_value, false, true) ;
	}

	#value to database
	function cfdef_prepare_list_value_to_database($p_value) {
		if ( '' == $p_value ) {
			return '';
		} else {
			return '|' . $p_value . '|';
		}
	}

	function cfdef_prepare_list_distinct_values($p_field_def) {
		$t_custom_field_table = config_get_global( 'mantis_custom_field_table' );

		$query = "SELECT possible_values
				  FROM $t_custom_field_table
				  WHERE id=" . db_param(0);
		$result = db_query_bound( $query, Array( $p_field_def['id'] ) );

		$t_row_count = db_num_rows( $result );
		if ( 0 == $t_row_count ) {
			return false;
		}
		$row = db_fetch_array( $result );

		$t_possible_values = custom_field_prepare_possible_values( $row['possible_values'] );
		$t_values_arr = explode( '|', $t_possible_values );
		$t_return_arr = array();
		
		foreach( $t_values_arr as $t_option ) {
			array_push( $t_return_arr, $t_option );
		}
		return $t_return_arr;
	}	
?>
