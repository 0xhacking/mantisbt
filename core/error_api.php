<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Id: error_api.php,v 1.9 2002-09-21 23:00:43 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# Error API
	###########################################################################

	# set up error_handler() as the new default error handling function
	set_error_handler( 'error_handler' );

	#########################################
	# SECURITY NOTE: these globals are initialized here to prevent them
	#   being spoofed if register_globals is turned on
	#
	$g_error_parameters = array();
	$g_error_handled = false;

	# ---------------
	# Default error handler
	#
	# This handler will not receive E_ERROR, E_PARSE, E_CORE_*, or E_COMPILE_*
	#  errors.
	#
	# E_USER_* are triggered by us and will contain an error constant in $p_error
	# The others, being system errors, will come with a string in $p_error
	#
	function error_handler( $p_type, $p_error, $p_file, $p_line, $p_context ) {
		global $g_error_parameters, $g_error_handled;

		$t_short_file = basename( $p_file );
		$t_method = 'none';

		# build an appropriate error string
		switch ( $p_type ) {
			case E_WARNING:
				$t_string = "SYSTEM WARNING: $p_error <br /> ($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_warnings' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_NOTICE:
				$t_string = "SYSTEM NOTICE: $p_error <br /> ($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_notices' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_USER_ERROR:
				$t_string = "MANTIS ERROR #$p_error: " .
							error_string( $p_error ) .
							"<br />($t_short_file: line $p_line)";
				$t_method = 'halt';
				break;
			case E_USER_WARNING:
				$t_string = "MANTIS WARNING #$p_error: " .
							error_string( $p_error ) .
							"<br />($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_warnings' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_USER_NOTICE:
				# used for debugging
				$t_string = $p_error;

				if ( ON == config_get( 'show_notices' ) ) {
					$t_method = 'inline';
				}
				break;
			default:
				#shouldn't happen, just display the error just in case
				$t_string = $p_error;
		}

		if ( 'halt' == $t_method ) {
			$t_old_contents = ob_get_contents();
			ob_end_clean();

			print_page_top1();
			print_page_top2a();

			echo "<p class=\"center\" style=\"color:red\">$t_string</p>";

			# @@@ temp until we get parameterized errors
			for ( $i = 0 ; $i < sizeof( $g_error_parameters ) ; $i = $i + 1 ) {
				echo $g_error_parameters[$i].'<br />';
			}

			if ( ON == config_get( 'show_detailed_errors' ) ) {
				error_print_details( $p_file, $p_line, $p_context );
				echo '<br />';
				error_print_stack_trace();
			}

			if ( $g_error_handled ) {
				echo '<p>Previous non-fatal errors occurred.  Page contents follow.</p>';

				echo '<div style="border: solid 1px black;padding: 4px">';
				echo $t_old_contents;
				echo '</div>';
			}

			die();
		} else if ( 'inline' == $t_method ) {
			echo "<p style=\"color:red\">$t_string</p>";
			# @@@ temp until we get parameterized errors
			for ( $i = 0 ; $i < sizeof( $g_error_parameters ) ; $i = $i + 1 ) {
				echo $g_error_parameters[$i].'<br />';
			}
		} else {
			# do nothing
		}

		$g_error_parameters = array();
		$g_error_handled = true;
	}

	# ---------------
	# helper function to print out the error details including context
	function error_print_details( $p_file, $p_line, $p_context ) {
	?>
		<center>
			<table class="width75">
				<tr>
					<td>Full path: <?php echo $p_file ?></td>
				</tr>
				<tr>
					<td>Line: <?php echo $p_line ?></td>
				</tr>
				<tr>
					<td>
						<?php error_print_context( $p_context ) ?>
					</td>
				</tr>
			</table>
		</center>
	<?php
	}

	# ---------------
	# helper function to print out the variable context
	function error_print_context( $p_context ) {
		echo '<table class="width100"><tr><th>Variable</th><th>Value</th><th>Type</th></tr>';
		while ( list( $t_var, $t_val ) = each( $p_context ) ) {
			echo "<tr><td>$t_var</td><td>$t_val</td><td>" . gettype( $t_val ) . "</td></tr>\n";
		}
		echo '</table>';
	}

	# ---------------
	# helper function to print out a stack trace if 'xdebug' module is loaded
	function error_print_stack_trace() {
		if ( extension_loaded( 'xdebug' ) ) { #check for xdebug presence
			$t_stack = xdebug_get_function_stack();

			# reverse the array in a separate line of code so the
			#  array_reverse() call doesn't appear in the stack
			$t_stack = array_reverse( $t_stack );
			array_shift( $t_stack ); #remove the call to this function from the stack trace

			echo '<center><table class="width75">';

			for ( $i = 0 ; $i < sizeof( $t_stack ) ; $i = $i + 1 ) {
				echo '<tr bgcolor="'.alternate_colors( $i).'"><td>'.$t_stack[$i].'</td></tr>';
			}

			echo '</table></center>';
		}
	}

	# ---------------
	# return an error string (in the current language) for the given error
	function error_string( $p_error ) {
		global $MANTIS_ERROR;

		return $MANTIS_ERROR[$p_error];
	}

	# ---------------
	# return true if we have handled an error on this page
	function error_handled() {
		global $g_error_handled;

		return ( true == $g_error_handled );
	}

	function error_parameters() {
		global $g_error_parameters;

		$g_error_parameters = func_get_args();
	}
?>
