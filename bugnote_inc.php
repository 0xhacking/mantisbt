<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details
?>
<?php
	# This include file prints out the list of bugnotes attached to the bug
	# $f_id must be set and be set to the bug id
?>
<?php
	# grab the user id currently logged in
	$t_user_id = get_current_user_field( 'id' );

	# get the bugnote data
	$query = "SELECT *,UNIX_TIMESTAMP(date_submitted) as date_submitted
			FROM $g_mantis_bugnote_table
			WHERE bug_id='$f_id'
			ORDER BY date_submitted $g_bugnote_order";
	$result = db_query($query);
	$num_notes = db_num_rows($result);
?>

<?php # Bugnotes BEGIN ?>
<a name="bugnotes"><p>
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
		$v3_date_submitted = date( $g_normal_date_format, ( $v3_date_submitted ) );

		# do not print private bugnotes for non-developers
		if (( PRIVATE == $v3_view_state ) &&
			( !access_level_check_greater_or_equal( DEVELOPER ) )) {
			continue;
		}

		# grab the bugnote text and id and prefix with v3_
		$query = "SELECT id, note
				FROM $g_mantis_bugnote_text_table
				WHERE id='$v3_bugnote_text_id'";
		$result2 = db_query( $query );
		$row = db_fetch_array( $result2 );

		$v3_bugnote_text_id = $row['id'];
		$v3_note = $row['note'];

		$v3_note = string_display( $v3_note );
?>
<tr>
	<td class="nopad" valign="top" width="100%">
		<table class="hide" cellspacing="1">
		<tr valign="top">
			<td class="category" colspan="2" width="25%">
				<?php print_user( $v3_reporter_id ) ?><br />
				<hr color="#eeeeee" size="1">
				<?php if ( PRIVATE == $v3_view_state ) { ?>
				<span class="small"><?php echo $s_private ?></span><br />
				<hr color="#eeeeee" size="1">
				<?php } ?>
				<span class="small"><?php echo $v3_date_submitted ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
					# only admins and the bugnote creator can edit/delete this bugnote
					# bug must be open to be editable
					if ( get_bug_field( $f_id, 'status' ) < RESOLVED ) {
						if (( access_level_check_greater_or_equal( ADMINISTRATOR ) ) ||
							( $v3_reporter_id == $t_user_id )) {
							print_bracket_link( 'bugnote_edit_page.php?f_bugnote_text_id='.$v3_bugnote_text_id.'&amp;f_id='.$f_id.'&amp;f_bugnote_id='.$v3_id, $s_bugnote_edit_link );
							print_bracket_link( 'bugnote_delete.php?f_bugnote_id='.$v3_id.'&amp;f_id='.$f_id, $s_delete_link );
						}
					}
				?>
				</span>
			</td>
			<td class="col-2" width="75%">
				<?php echo $v3_note ?>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="spacer">
		&nbsp;
	</td>
</tr>
<?php
		} # end for loop
	} # end else
?>
</table>
<?php # Bugnotes END ?>

<?php if ( ( ( $v_status < RESOLVED ) ||
		  ( isset( $f_resolve_note ) ) ) &&
		( access_level_check_greater_or_equal( REPORTER ) ) ) { ?>
<?php # Bugnote Add Form BEGIN ?>
<p>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title">
		<form method="post" action="bugnote_add.php">
		<input type="hidden" name="f_id" value="<?php echo $f_id ?>">
		<?php echo $s_add_bugnote_title ?>
	</td>
</tr>
<tr class="row-1">
	<td class="center">
		<textarea name="f_bugnote_text" cols="80" rows="10" wrap="virtual"></textarea>
	</td>
</tr>
<tr>
	<td class="center">
		<input type="submit" value="<?php echo $s_add_bugnote_button ?>">
		</form>
	</td>
</tr>
</table>
<?php # Bugnote Add Form END ?>
<?php } ?>