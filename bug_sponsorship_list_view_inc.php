<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Id: bug_sponsorship_list_view_inc.php,v 1.1 2004-05-09 02:24:19 vboctor Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the list of users sponsoring the current
	# bug.	$f_bug_id must be set to the bug id

	require_once( $t_core_path . 'sponsorship_api.php' );
?>

<?php  ?>
<?php
	if ( ( config_get( 'enable_sponsorship' ) == ON ) && ( access_has_bug_level( config_get( 'view_sponsorship_total_threshold' ), $f_bug_id ) ) ) {
		$t_sponsorship_ids = sponsorship_get_all_ids( $f_bug_id );

		echo '<a name="sponsorships" id="sponsorships" /><br />';
 		echo '<table class="width100" cellspacing="1">';
?>
		<tr>
			<td class="form-title" colspan="2">
				<?php
					echo lang_get( 'users_sponsoring_bug' );
					$t_details_url = lang_get( 'sponsorship_process_url' );
					if ( !is_blank( $t_details_url ) ) {
						echo '&nbsp;[<a href="' . $t_details_url . '" target="_blank">' . lang_get( 'sponsorship_more_info' ) . '</a>]';
					}
				?>
			</td>
		</tr>

	<?php 
		if ( !bug_is_readonly( $f_bug_id ) && !current_user_is_anonymous() ) {
	?>
			<tr class="row-1">
				<td class="category" width="15%"><?php echo lang_get( 'sponsor_issue' ) ?></td>
				<td>
					<form method="POST" action="bug_set_sponsorship.php">
						<?php echo sponsorship_get_currency() ?>
						<input type="hidden" name="bug_id" value="<?php echo $f_bug_id ?>" size="4" />
						<input type="text" name="amount" value="<?php echo config_get( 'minimum_sponsorship_amount' )  ?>" size="4" />
						<input type="submit" name="sponsor" value="Sponsor" />
					</form>
				</td>
			</tr>
	<?php
		}

		$t_total_sponsorship = bug_get_field( $f_bug_id, 'sponsorship_total' );
		if ( $t_total_sponsorship > 0 ) {
	?>

		<tr class="row-2">
			<td class="category" width="15%"><?php echo lang_get( 'sponsors_list' ) ?></td>
			<td>
				<?php
					echo sprintf( lang_get( 'total_sponsorship_amount' ), sponsorship_format_amount( $t_total_sponsorship ) );
					if ( access_has_bug_level( config_get( 'view_sponsorship_details_threshold' ), $f_bug_id ) ) {
						echo '<br /><br />';
						$i = 0;
						foreach ( $t_sponsorship_ids as $id ) {
							$t_sponsorship = sponsorship_get( $id );
							$t_date_added = date( config_get( 'normal_date_format' ), db_unixtimestamp( $t_sponsorship->date_added ) );

							echo ($i > 0) ? '<br />' : '';
							$i++;

							echo $t_date_added . ': ';
							print_user( $t_sponsorship->user_id );
							echo ' (' . sponsorship_format_amount( $t_sponsorship->amount ) . ')';
						}
					}
				?>
			</td>
		</tr>
<?php
		}

	 	echo '</table>';
	}
?>