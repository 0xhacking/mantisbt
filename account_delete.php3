<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<? include( "core_API.php" ) ?>
<? login_cookie_check() ?>
<?
	db_mysql_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );

	### If an account is protected then no one can change the information
	### This is useful for shared accounts or for demo purposes
	if ( $f_protected!="on" ) {
	    ### Remove aaccount
    	$query = "DELETE
    			FROM $g_mantis_user_table
    			WHERE id='$f_id'";
	    $result = mysql_query( $query );

	    ### Remove associated profiles
	    $query = "DELETE
	    		FROM $g_mantis_user_profile_table
	    		WHERE user_id='$f_id'";
	    $result = mysql_query( $query );

		### Remove associated preferences
    	$query = "DELETE
    			FROM $g_mantis_user_pref_table
    			WHERE user_id='$f_id'";
    	$result = mysql_query( $query );
	} ### end if protected
?>
<? print_html_top() ?>
<? print_head_top() ?>
<? print_title( $g_window_title ) ?>
<? print_css( $g_css_include_file ) ?>
<?
	if ( $result ) {
		print_meta_redirect( $g_logout_page, $g_wait_time );
	}
?>
<? include( $g_meta_include_file ) ?>
<? print_head_bottom() ?>
<? print_body_top() ?>
<? print_header( $g_page_title ) ?>
<p>
<? print_menu( $g_menu_include_file ) ?>

<p>
<div align=center>
<?
	### PROTECTED
	if ( $f_protected=="on" ) {
		PRINT "$s_account_protected<p>";
	}
	### SUCCESS
	else if ( $result ) {
		PRINT "$s_account_removed<p>";
	}
	### FAILURE
	else {
		PRINT "ERROR!!!  An error has occured.  Email the <a href=\"mailto:$g_administrator_email\">administrator</a> with this query:<p>";
		echo $query;
	}
?>
<p>

<? if ( $f_protected=="on" ) { ?>
<a href="<? echo $g_account_page ?>"><? echo $s_go_back ?></a>
<? } else { ?>
<a href="<? echo $g_logout_page ?>"><? echo $s_proceed ?></a>
<? } ?>
</div>

<? print_footer() ?>
<? print_body_bottom() ?>
<? print_html_bottom() ?>