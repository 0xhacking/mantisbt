<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Revision: 1.2 $
	# $Author: jfitzell $
	# $Date: 2002-08-24 21:35:38 $
	#
	# $Id: core_config_API.php,v 1.2 2002-08-24 21:35:38 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# Configuration API
	###########################################################################

	# ------------------
	# Retrieves the value of a config option
	#  This function will return one of (in order of preference):
	#    1. The user-defined value (if set)
	#    2. The default value (if known)
	#    3. The value passed as the second parameter of the function
	function config_get( $p_option, $p_default=null ) {

		# ------ global variable implementation ------
		# this function implements getting configuration
		#  from our current global variable scheme. This
		#  interface should remain constant but we could
		#  call out to other functions or replace this code
		#  to use a DB or some other method

		if ( isset( $GLOBALS['g_'.$p_option] ) ) {
			return $GLOBALS['g_'.$p_option];
		} else {
			# unless we were allowing for the option not to exist by passing
			#  a default, trigger a NOTICE
			if ( null == $p_default ) { 
				trigger_error( ERROR_CONFIG_OPT_NOT_FOUND, NOTICE );
			}
			return $p_default;
		}
	}

	# ------------------
	# Returns true if the specified config option exists (ie. a 
	#  value or default can be found), false otherwise
	function config_is_set( $p_option ) {
		if ( isset( $GLOBALS['g_'.$p_option] ) ) {
			return true;
		} else {
			return false;
		}
	}

	# ------------------
	# Sets the value of the given config option to the given value
	#  If the config option does not exist, an ERROR is triggered
	function config_set( $p_option, $p_value ) {
		if ( ! isset( $GLOBALS['g_'.$p_option] ) ) {
			trigger_error( ERROR_CONFIG_OPT_NOT_FOUND, ERROR );
		}

		$GLOBALS['g_'.$p_option] = $p_value;

		return true;
	}
?>