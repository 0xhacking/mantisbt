<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: compress_api.php,v 1.7 2003-02-16 23:16:34 vboctor Exp $
	# --------------------------------------------------------

	###########################################################################
	# Compression API
	#
	# Starts the buffering/compression (only if the compression option is ON)
	# This method should be called after all possible re-directs and
	#  access level checks.
	###########################################################################

	$g_compression_started = false;

	# ----------------
	# Check if compression should be enabled.
	function compress_is_enabled() {
		return ( ON == config_get( 'compress_html' ) );
	}
	# ----------------
	# Start output buffering with compression.
	function compress_start() {
		global $g_compression_started;

		if ($g_compression_started) {
			# @@@ consider triggering an error
			return;
		}

		if  ( compress_is_enabled() ) {
			ob_implicit_flush( 0 );
			ob_start( 'ob_gzhandler' );
			$g_compression_started = true;
		}
	}
	# ----------------
	# Stop buffering and flush buffer contents.
	function compress_stop() {
		global $g_compression_started;

		if (!$g_compression_started) {
			# @@@ consider triggering an error
			return;
		}

		if  ( compress_is_enabled() ) {
			ob_end_flush();
			ob_implicit_flush();
		}
	}
	# ----------------
?>