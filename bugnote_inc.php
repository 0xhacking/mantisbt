<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Id: bugnote_inc.php,v 1.76 2003-01-24 14:59:24 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the list of bugnotes attached to the bug
	# $f_bug_id must be set and be set to the bug id
?>
<?php
	require_once( 'core.php' );
	
	require_once( $g_core_path . 'current_user_api.php' );
?>
<?php
	# grab the user id currently logged in
	$t_user_id = current_user_get_field( 'id' );

	if ( !access_level_check_greater_or_equal( $g_private_bugnote_threshold ) ) {
		$t_restriction = 'AND view_state=' . PUBLIC;
	} else {
		$t_restriction = '';
	}

	# get the bugnote data
	$query = "SELECT *,UNIX_TIMESTAMP(date_submitted) as date_submitted
			FROM $g_mantis_bugnote_table
			WHERE bug_id='$f_bug_id' $t_restriction
			ORDER BY date_submitted $g_bugnote_order";
	$result = db_query($query);
	$num_notes = db_num_rows($result);
?>

<?php # Bugnotes BEGIN ?>
<a name="bugnotes"><br />
<table class="width100" cellspacing="1">
<?php
	# no bugnotes
	if ( 0 == $num_notes ) {
?>
<tr>
	<td class="center" colspan="2">
		<?php echo $s_no_bugnotes_msg ?>
	</td>
</tr>
<?php } else { # print bugnotes ?>
<tr>
	<td class="form-title" colspan="2">
		<?php echo $s_bug_notes_title ?>
	</td>
</tr>
<?php
	for ( $i=0; $i < $num_notes; $i++ ) {
		# prefix all bugnote data with v3_
		$row = db_fetch_array( $result );
		extract( $row, EXTR_PREFIX_ALL, 'v3' );
		$v3_date_submitted = date( config_get( 'normal_date_format' ), ( $v3_date_submitted ) );

		# grab the bugnote text and id and prefix with v3_
		$query = "SELECT note
				FROM $g_mantis_bugnote_text_table
				WHERE id='$v3_bugnote_text_id'";
		$result2 = db_query( $query );
		$row = db_fetch_array( $result2 );

		$v3_note = $row['note'];
		$v3_note = string_display( $v3_note );

		if ( PRIVATE == $v3_view_state ) {
			$t_bugnote_css		= 'bugnote-private';
			$t_bugnote_note_css	= 'bugnote-note-private';
		} else {
			$t_bugnote_css		= 'bugnote-public';
			$t_bugnote_note_css	= 'bugnote-note-public';
		}
?>
<tr class="bugnote">
	<td class="<?php echo $t_bugnote_css ?>">
		<?php print_user( $v3_reporter_id ) ?>
		<?php if ( PRIVATE == $v3_view_state ) { ?>
		<span class="small">[ <?php echo $s_private ?> ]</span>
		<?php } ?>
		<br />
		<span class="small"><?php echo $v3_date_submitted ?></span><br /><br />
		<span class="small">
		<?php
			# only admins and the bugnote creator can edit/delete this bugnote
			# bug must be open to be editable
			if ( bug_get_field( $f_bug_id, 'status' ) < config_get( 'bug_resolved_status_threshold' ) ) {
				if (( access_level_check_greater_or_equal( ADMINISTRATOR ) ) ||
					( $v3_reporter_id == $t_user_id )) {
					print_bracket_link( 'bugnote_edit_page.php?bugnote_id='.$v3_id, $s_bugnote_edit_link );
					print_bracket_link( 'bugnote_delete.php?bugnote_id='.$v3_id, $s_delete_link );
					if ( access_level_check_greater_or_equal( $g_private_bugnote_threshold ) ) {
						if ( PRIVATE == $v3_view_state ) {
							print_bracket_link('bugnote_set_view_state.php?private=0&bugnote_id='.$v3_id, $s_make_public);
						} else {
							print_bracket_link('bugnote_set_view_state.php?private=1&bugnote_id='.$v3_id, $s_make_private);
						}
					}
				}
			}
		?>
		</span>
	</td>
	<td class="<?php echo $t_bugnote_note_css ?>">
		<?php echo $v3_note ?>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="2">&nbsp;
		
	</td>
</tr>
<?php
		} # end for loop
	} # end else
?>
</table>
<?php # Bugnotes END ?>

<?php if ( ( ( $t_bug->status < config_get( 'bug_resolved_status_threshold' ) ) ||
		  ( isset( $f_resolve_note ) ) ) &&
		( access_level_check_greater_or_equal( config_get( 'add_bugnote_threshold' ) ) ) ) { ?>
<?php # Bugnote Add Form BEGIN ?>
<br />
<form method="post" action="bugnote_add.php">
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<input type="hidden" name="bug_id" value="<?php echo $f_bug_id ?>" />
		<?php echo $s_add_bugnote_title ?>
	</td>
</tr>
<tr class="row-2">
	<td class="category" width="25%">
		<?php echo $s_bugnote ?>
	</td>
	<td width="75%">
		<textarea name="bugnote_text" cols="80" rows="10" wrap="virtual"></textarea>
	</td>
</tr>
<?php if ( access_level_check_greater_or_equal( $g_private_bugnote_threshold ) ) { ?>
<tr class="row-1">
	<td class="category">
		<?php echo $s_private ?>
	</td>
	<td>
		<input type="checkbox" name="private" />
	</td>
</tr>
<?php } ?>
<tr>
	<td class="center" colspan="2">
		<input type="submit" value="<?php echo $s_add_bugnote_button ?>" />
	</td>
</tr>
</table>
</form>
<?php # Bugnote Add Form END ?>
<?php } ?>
