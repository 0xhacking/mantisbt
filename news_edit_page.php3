<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( "core_API.php" ) ?>
<?php login_cookie_check() ?>
<?
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	check_access( MANAGER );

	# If deleting item redirect to delete script
	if ( "delete" == $f_action ) {
		print_header_redirect( "$g_news_delete_page?f_id=$f_id" );
		exit;
	}

	# Retrieve news item data and prefix with v_
	$row = news_select_query( $f_id );
	if ( $row ) {
    	extract( $row, EXTR_PREFIX_ALL, "v" );
    }

   	$v_headline = string_edit_text( $v_headline );
   	$v_body 	= string_edit_textarea( $v_body );
?>
<?php print_page_top1() ?>
<?php print_page_top2() ?>

<?php # Edit News Form BEGIN ?>
<p>
<div align="center">
<form method="post" action="<?php echo $g_news_update ?>">
<input type="hidden" name="f_id" value="<?php echo $v_id ?>">
<table class="width75" cellspacing="0">
<tr>
	<td class="form-title">
		<?php echo $s_edit_news_title ?>
	</td>
	<td class="right">
		<?php print_bracket_link( $g_news_menu_page, $s_go_back ) ?>
	</td>
</tr>
<tr class="row-1">
	<td width="25%">
		<?php echo $s_headline ?>
	</td>
	<td width="75%">
		<input type="text" name="f_headline" size="64" maxlength="64" value="<?php echo $v_headline ?>">
	</td>
</tr>
<tr class="row-2">
	<td>
		<?php echo $s_body ?>
	</td>
	<td>
		<textarea name="f_body" cols="60" rows="10" wrap="virtual"><?php echo $v_body ?></textarea>
	</td>
</tr>
<tr class="row-1">
	<td>
		<?php echo $s_post_to ?>
	</td>
	<td>
		<select name="f_project_id">
			<?php if( ADMINISTRATOR == get_current_user_field( "access_level" ) ) { ?>
				<option value="0000000" <?php if ( "0000000" == $v_project_id ) echo "SELECTED"?>>Sitewide</option>
			<?php } ?>
			<?php print_news_project_option_list( $v_project_id ) ?>
		</select>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" value="<?php echo $s_update_news_button ?>">
	</td>
</tr>
</table>
</form>
</div>
<?php # Edit News Form END ?>

<?php print_page_bot1( __FILE__ ) ?>