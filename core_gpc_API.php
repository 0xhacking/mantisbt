<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Revision: 1.1 $
	# $Author: jfitzell $
	# $Date: 2002-08-24 09:25:59 $
	#
	# $Id: core_gpc_API.php,v 1.1 2002-08-24 09:25:59 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# GET, POST, and Cookie API
	###########################################################################

	# ---------------
	# Retrieves a GPC variable.
	# If the variable is not set, the default is returned. 
	# If magic_quotes_gpc is on, slashes will be stripped from the value before being returned.
	function gpc_get( $p_var_name, $p_default = null ) {
		# simulate auto-globals from PHP v4.1.0 (see also code in core_php_API.php)
		if ( ! php_version_at_least( '4.1.0' ) ) {
			global $_REQUEST;
		}

		if ( isset( $_REQUEST[$p_var_name] ) ) {
			$t_result = $_REQUEST[$p_var_name];
			if (get_magic_quotes_gpc() == 1) {
				$t_result = stripslashes( $t_result );
			}
		} else if ( null !== $p_default) {
			$t_result = $p_default;
		} else {
			trigger_error(ERROR_GPC_VAR_NOT_FOUND, E_USER_ERROR);
			$t_result = null;
		}
		
		return $t_result;
	}
	# -----------------
	# Retrieves a string GPC variable. Uses gpc_get().
	function gpc_get_string( $p_var_name, $p_default = null ) {
		return gpc_get( $p_var_name, $p_default );
	}
	# ------------------
	# Retrieves an integer GPC variable. Uses gpc_get().
	function gpc_get_int( $p_var_name, $p_default = null ) {
		return (integer)(gpc_get( $p_var_name, $p_default ));
	}
	# ------------------
	# Retrieves a boolean GPC variable. Uses gpc_get();
	function gpc_get_bool( $p_var_name, $p_default = null ) {
		$t_result = gpc_get( $p_var_name, $p_default );

		if ( 0 == strcasecmp( 'off', $t_result ) ||
			 0 == strcasecmp( 'no', $t_result ) ||
			 0 == strcasecmp( 'false', $t_result ) ||
			 0 == strcasecmp( '0', $t_result ) ) {
			return false;
		} else {
			return true;
		}
	}
?>
