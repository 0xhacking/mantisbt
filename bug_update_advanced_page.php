<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_update_advanced_page.php,v 1.34 2002-10-27 23:35:40 jfitzell Exp $
	# --------------------------------------------------------
?>
<?php
	# Show the advanced update bug options
?>
<?php require_once( 'core.php' ) ?>
<?php login_cookie_check() ?>
<?php
	if ( SIMPLE_ONLY == config_get( 'show_update' ) ) {
		print_header_redirect ( 'bug_update_page.php?f_bug_id='.$f_bug_id );
	}

	$f_bug_id		= gpc_get_int( 'f_bug_id' );

	project_access_check( $f_bug_id );
	check_access( config_get( 'update_bug_threshold' ) );
	bug_ensure_exists( $f_bug_id );

	$c_bug_id = (integer)$f_bug_id;

	$t_bug_table = config_get( 'mantis_bug_table' );

	$query = "SELECT *, UNIX_TIMESTAMP(date_submitted) as date_submitted,
			UNIX_TIMESTAMP(last_updated) as last_updated
			FROM $t_bug_table
			WHERE id='$c_bug_id'";
	$result = db_query( $query );
	$row = db_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, 'v' );

	# if bug is private, make sure user can view private bugs
	access_bug_check( $f_bug_id, $v_view_state );

	$t_bug_text_table = config_get( 'mantis_bug_text_table' );

	$query = "SELECT *
    		FROM $t_bug_text_table
    		WHERE id='$v_bug_text_id'";
    $result = db_query( $query );
	$row = db_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, 'v2' );

	$v_os 						= string_display( $v_os );
	$v_os_build 				= string_display( $v_os_build );
	$v_platform					= string_display( $v_platform );
	$v_version 					= string_display( $v_version );
	$v_summary					= string_edit_text( $v_summary );
	$v2_description 			= string_edit_textarea( $v2_description );
	$v2_steps_to_reproduce 		= string_edit_textarea( $v2_steps_to_reproduce );
	$v2_additional_information 	= string_edit_textarea( $v2_additional_information );
?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<br />
<form method="post" action="bug_update.php">
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="3">
		<input type="hidden" name="f_bug_id" value="<?php echo $v_id ?>" />
		<input type="hidden" name="f_old_status" value="<?php echo $v_status ?>" />
		<input type="hidden" name="f_old_handler_id" value="<?php echo $v_handler_id ?>" />
		<?php echo lang_get( 'updating_bug_advanced_title' ) ?>
	</td>
	<td class="right" colspan="3">
<?php
	print_bracket_link( string_get_bug_view_url( $f_bug_id ), lang_get( 'back_to_bug_link' ) );

	if ( BOTH == config_get( 'show_update' ) ) {
		print_bracket_link( 'bug_update_page.php?f_bug_id='.$f_bug_id, lang_get( 'update_simple_link' ) );
	}
?>
	</td>
</tr>
<tr class="row-category">
	<td width="15%">
		<?php echo lang_get( 'id' ) ?>
	</td>
	<td width="20%">
		<?php echo lang_get( 'category' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'severity' ) ?>
	</td>
	<td width="20%">
		<?php echo lang_get( 'reproducibility' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'date_submitted' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'last_update' ) ?>
	</td>
</tr>
<tr class="row-2">
	<td>
		<?php echo $v_id ?>
	</td>
	<td>
		<select name="f_category">
			<?php print_category_option_list( $v_category ) ?>
		</select>
	</td>
	<td>
		<select name="f_severity">
			<?php print_enum_string_option_list( 'severity', $v_severity ) ?>
		</select>
	</td>
	<td>
		<select name="f_reproducibility">
			<?php print_enum_string_option_list( 'reproducibility', $v_reproducibility ) ?>
		</select>
	</td>
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $v_date_submitted ) ?>
	</td>
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $v_last_updated ) ?>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="6">
		&nbsp;
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'reporter' ) ?>
	</td>
	<td>
		<select name="f_reporter_id">
			<?php print_reporter_option_list( $v_reporter_id ) ?>
		</select>
	</td>
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
		<select name="f_view_state">
			<?php print_enum_string_option_list( 'view_state', $v_view_state) ?>
		</select>
	</td>
	<td colspan="2">
		&nbsp;
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'assigned_to' ) ?>
	</td>
	<td colspan="5">
		<select name="f_handler_id">
			<option value="0"></option>
			<?php print_assign_to_option_list( $v_handler_id ) ?>
		</select>
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'priority' ) ?>
	</td>
	<td align="left">
		<select name="f_priority">
			<?php print_enum_string_option_list( 'priority', $v_priority ) ?>
		</select>
	</td>
	<td class="category">
		<?php echo lang_get( 'resolution' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'resolution', $v_resolution ) ?>
	</td>
	<td class="category">
		<?php echo lang_get( 'platform' ) ?>
	</td>
	<td>
		<input type="text" name="f_platform" size="16" maxlength="32" value="<?php echo $v_platform ?>" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'status' ) ?>
	</td>
	<td bgcolor="<?php echo get_status_color( $v_status ) ?>">
		<select name="f_status">
			<?php print_enum_string_option_list( 'status', $v_status ) ?>
		</select>
	</td>
	<td class="category">
		<?php echo lang_get( 'duplicate_id' ) ?>
	</td>
	<td>
		<?php echo $v_duplicate_id ?>
	</td>
	<td class="category">
		<?php echo lang_get( 'os' ) ?>
	</td>
	<td>
		<input type="text" name="f_os" size="16" maxlength="32" value="<?php echo $v_os ?>" />
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'projection' ) ?>
	</td>
	<td>
		<select name="f_projection">
			<?php print_enum_string_option_list( 'projection', $v_projection ) ?>
		</select>
	</td>
	<td colspan="2">
		&nbsp;
	</td>
	<td class="category">
		<?php echo lang_get( 'os_version' ) ?>
	</td>
	<td>
		<input type="text" name="f_os_build" size="16" maxlength="16" value="<?php echo $v_os_build ?>" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'eta' ) ?>
	</td>
	<td>
		<select name="f_eta">
			<?php print_enum_string_option_list( 'eta', $v_eta ) ?>
		</select>
	</td>
	<td colspan="2">
		&nbsp;
	</td>
	<td class="category">
		<?php echo lang_get( 'product_version' ) ?>
	</td>
	<td>
		<select name="f_version">
			<?php print_version_option_list( $v_version ) ?>
		</select>
	</td>
</tr>
<tr class="row-1">
	<td colspan="4">
		&nbsp;
	</td>
	<td class="category">
		<?php echo lang_get( 'build' ) ?>
	</td>
	<td>
		<input type="text" name="f_build" size="16" maxlength="32" value="<?php echo $v_build ?>" />
	</td>
</tr>
<tr class="row-2">
	<td colspan="4">
		&nbsp;
	</td>
	<td class="category">
		<?php echo lang_get( 'votes' ) ?>
	</td>
	<td>
		<?php echo $v_votes ?>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="6">
		&nbsp;
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'summary' ) ?>
	</td>
	<td colspan="5">
		<input type="text" name="f_summary" size="80" maxlength="128" value="<?php echo $v_summary ?>" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td colspan="5">
		<textarea cols="60" rows="5" name="f_description" wrap="virtual"><?php echo $v2_description ?></textarea>
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'steps_to_reproduce' ) ?>
	</td>
	<td colspan="5">
		<textarea cols="60" rows="5" name="f_steps_to_reproduce" wrap="virtual"><?php echo $v2_steps_to_reproduce ?></textarea>
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'additional_information' ) ?>
	</td>
	<td colspan="5">
		<textarea cols="60" rows="5" name="f_additional_information" wrap="virtual"><?php echo $v2_additional_information ?></textarea>
	</td>
</tr>
<tr>
	<td class="spacer" colspan="6">
		&nbsp;
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'add_bugnote_title' ) ?>
	</td>
	<td colspan="5">
		<textarea name="f_bugnote_text" cols="80" rows="10" wrap="virtual"></textarea>
	</td>
</tr>
<?php if ( access_level_check_greater_or_equal( config_get( 'private_bugnote_threshold' ) ) ) { ?>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'private' ) ?>
	</td>
	<td>
		<input type="checkbox" name="f_private" />
	</td>
</tr>
<?php } ?>
<tr>
	<td class="center" colspan="6"">
		<input type="submit" value="<?php echo lang_get( 'update_information_button' ) ?>" />
	</td>
</tr>
</table>
</form>

<?php print_page_bot1( __FILE__ ) ?>
