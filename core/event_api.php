<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.

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

/**
 * Event API
 * Handles the event system.
 *
 * @author John Reese
 */

##### Cache variables #####

$g_event_cache = array();

##### Public API #####

/**
 * Declare an event of a given type.
 * Will do nothing if event already exists.
 * @param string Event name
 * @param int Event type
 */
function event_declare( $p_name, $p_type=EVENT_TYPE_DEFAULT ) {
	global $g_event_cache;

	if ( !isset( $g_event_cache[$p_name] ) ) {

		$g_event_cache[$p_name] = array( 
			'type' => $p_type, 
			'callbacks' => array()
		);
	}	
}

/**
 * Convenience function for decleare multiple events.
 * @param array Events
 */
function event_declare_many( $p_events ) {
	foreach ( $p_events as $t_name => $t_type ) {
		event_declare( $t_name, $t_type );
	}
}

/**
 * Hook a callback function to a given event.
 * A plugin's basename must be specified for proper handling of plugin callbacks.
 * @param string Event name
 * @param string Callback function
 * @param string Plugin basename
 */
function event_hook( $p_name, $p_callback, $p_plugin=false ) {
	global $g_event_cache;

	if ( !isset( $g_event_cache[$p_name] ) ) { 
		error_parameters( $p_name );
		trigger_error( ERROR_EVENT_UNDECLARED, WARNING );
		return null;
	}

	$g_event_cache[$p_name]['callbacks'][$p_callback] = $p_plugin;
}

/**
 * Hook multiple callback functions to multiple events.
 * @param array Event name/callback pairs
 * @param string Plugin basename
 */
function event_hook_many( $p_hooks, $p_plugin=false ) {
	if ( ! is_array( $p_hooks ) ) {
		return;
	}

	foreach( $p_hooks as $t_name => $t_callbacks ) {
		if ( !is_array( $t_callbacks ) ) {
			event_hook( $t_name, $t_callback, $p_plugin );
			continue;
		}

		foreach( $t_callbacks as $t_callback ) {
			event_hook( $t_name, $t_callback, $p_plugin );
		}
	}
}

/**
 * Signal an event to execute and handle callbacks as necessary.
 * @param string Event name
 * @param multi Event parameters
 * @param int Event type override
 * @return multi Null if event undeclared, appropriate return value otherwise
 */
function event_signal( $p_name, $p_params=null, $p_type=null ) {
	global $g_event_cache;

	if ( !isset( $g_event_cache[$p_name] ) ) {
		error_parameters( $p_name );
		trigger_error( ERROR_EVENT_UNDECLARED, WARNING );
		return null;
	}

	if ( is_null( $p_type ) ) {
		$t_type = $g_event_cache[$p_name]['type'];
	} else {
		$t_type = $p_type;
	}
	$t_callbacks = $g_event_cache[$p_name]['callbacks'];

	switch ( $t_type ) {
		case EVENT_TYPE_EXECUTE:
			return event_type_execute( $p_name, $t_callbacks );

		case EVENT_TYPE_OUTPUT:
			return event_type_output( $p_name, $t_callbacks, $p_params );

		case EVENT_TYPE_CHAIN:
			return event_type_chain( $p_name, $t_callbacks, $p_params );
		
		default:
			return event_type_default( $p_name, $t_callbacks, $p_params );
	}
}

##### Event-handling functions #####

/**
 * Executes a plugin's callback function for a given event.
 * @param string Event name
 * @param string Callback name
 * @param string Plugin basename
 * @param multi Parameters for event callback
 * @return multi Null if callback not found, value from callback otherwise
 */
function event_callback( $p_event, $p_callback, $p_plugin, $p_params=null ) {
	if ( $p_plugin !== false ) {
		plugin_include( $p_plugin, true );
		plugin_push_current( $p_plugin );
	}

	$t_value = null;
	if ( function_exists( $p_callback ) ) {
		if ( !is_array( $p_params ) ) {
			$p_params = array( $p_params );
		}
		$t_value = call_user_func_array( $p_callback, array_merge( array( $p_event ), $p_params ) );
	}

	if ( $p_plugin !== false ) {
		plugin_pop_current();
	}

	return $t_value;
}

/**
 * Process an execute event type.
 * All callbacks will be called with no parameters, and their
 * return values will be ignored.
 * @param string Event name
 * @param array Array of callback function/plugin basename key/value pairs
 */
function event_type_execute( $p_event, $p_callbacks ) {
	foreach( $p_callbacks as $t_callback => $t_plugin ) {
		event_callback( $p_event, $t_callback, $t_plugin );
	}
}

/**
 * Process an output event type.
 * All callbacks will be called with the given parameters, and their
 * return values will be echoed to the client, separated by a given string.
 * If there are no callbacks, then nothing will be sent as output.
 * @param string Event name
 * @param array Array of callback function/plugin basename key/value pairs
 * @param multi Output separator (if single string) or indexed array of pre, mid, and post strings
 */
function event_type_output( $p_event, $p_callbacks, $p_params=null ) {
	$t_prefix = '';
	$t_separator = '';
	$t_postfix = '';

	if ( is_array( $p_params ) ) {
		switch ( count( $p_params ) ) {
			case 3:
				$t_postfix = $p_params[2];
			case 2:
				$t_separator = $p_params[1];
			case 1:
				$t_prefix = $p_params[0];
		}
	} else {
		$t_separator = $p_params;
	}

	$t_output = array();
	foreach( $p_callbacks as $t_callback => $t_plugin ) {
		$t_output[] = event_callback( $p_event, $t_callback, $t_plugin, $p_params );
	}
	if ( count( $p_callbacks ) > 0 ) {
		echo $t_prefix, implode( $t_separator, $t_output ), $t_postfix;
	}
}

/**
 * Process a chained event type.
 * The first callback with be called with the given input.  All following
 * callbacks will be called with the previous's output as its input.  The
 * final callback's return value will be returned to the event origin.
 * @param string Event name
 * @param array Array of callback function/plugin basename key/value pairs
 * @param string Input string
 * @return string Output string
 */
function event_type_chain( $p_event, $p_callbacks, $p_input ) {
	$t_output = $p_input;
	foreach( $p_callbacks as $t_callback => $t_plugin ) {
		$t_output = event_callback( $p_event, $t_callback, $t_plugin, $t_output );
	}
	return $t_output;
}

/**
 * Process a default event type.
 * All callbacks will be called with the given data parameters.  The
 * return value of each callback will be appended to an array with the callback's
 * basename as the key.  This array will then be returned to the event origin.
 * @param string Event name
 * @param array Array of callback function/plugin basename key/value pairs
 * @param multi Data
 * @return array Array of callback/return key/value pairs
 */
function event_type_default( $p_event, $p_callbacks, $p_data ) {
	$t_output = array();	
	foreach( $p_callbacks as $t_callback => $t_plugin ) {
		$t_output[$t_callback] = event_callback( $p_event, $t_callback, $t_plugin, $p_data );
	}
	return $t_output;
}

