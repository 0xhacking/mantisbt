<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Revision: 1.1 $
	# $Author: jfitzell $
	# $Date: 2002-08-25 07:04:40 $
	#
	# $Id: error_api.php,v 1.1 2002-08-25 07:04:40 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# Error API
	###########################################################################

	# set up error_handler() as the new default error handling function
	set_error_handler( 'error_handler' );

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
		$t_short_file = basename( $p_file );
		$t_method = 'none';

		# build an appropriate error string
		switch ( $p_type ) {
			case E_WARNING:
				$t_string = "SYSTEM WARNING: $p_error <br> ($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_warnings' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_NOTICE:
				$t_string = "SYSTEM NOTICE: $p_error <br> ($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_notices' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_USER_ERROR:
				$t_string = "MANTIS ERROR #$p_error: " .
							error_string( $p_error ) .
							"<br>($t_short_file: line $p_line)";
				$t_method = 'halt';
				break;
			case E_USER_WARNING:
				$t_string = "MANTIS WARNING #$p_error: " .
							error_string( $p_error ) .
							"<br>($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_warnings' ) ) {
					$t_method = 'inline';
				}
				break;
			case E_USER_NOTICE:
				$t_string = "MANTIS NOTICE #$p_error: " .
							error_string( $p_error ) .
							"<br>($t_short_file: line $p_line)";
				if ( ON == config_get( 'show_notices' ) ) {
					$t_method = 'inline';
				}
				break;
			default:
				#shouldn't happen, just display the error just in case
				$t_string = $p_error;
		}

		if ( 'halt' == $t_method ) {
			# clear the output buffer so we can start from scratch
			ob_end_clean();

			print_page_top1();
			print_page_top2a();

			echo "<p class=\"center\" style=\"color:red\">$t_string</p>";

			if ( ON == config_get( 'show_detailed_errors' ) ) {
				if (isset($php_errormsg))
					echo "okie dokie";
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
								<table class="width100">
									<tr>
										<th>Variable</th>
										<th>Value</th>
										<th>Type</th>
									</tr>
			<?php
				while ( list( $t_var, $t_val ) = each( $p_context ) ) {
					echo "<tr><td>$t_var</td><td>$t_val</td><td>" . gettype( $t_val ) . "</td></tr>\n";
				}
			?>
								</table>
							</td>
						</tr>
					</table>
				</center>
			<?php
			}

			die();
		} else if ( 'inline' == $t_method ) {
			echo "<p style=\"color:red\">$t_string</p>";
		} else {
			# do nothing
		}
	}
	# ---------------
	# returns an error string (in the current language) for the given error
	function error_string( $p_error ) {
		global $MANTIS_ERROR;

		return $MANTIS_ERROR[$p_error];
	}
?>
