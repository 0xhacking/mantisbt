<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( 'core_API.php' ) ?>
<?php
	# Check to see if signup is allowed
	if ( OFF == $g_allow_signup ) {
		print_header_redirect( 'login_page.php' );
		exit;
	}

	# check for empty username
	$f_username = trim( $f_username );
	if ( empty( $f_username ) ) {
		print_mantis_error( ERROR_EMPTY_FIELD );
	}
	$c_username = addslashes($f_username);

	# Check for a properly formatted email with valid MX record
	$result = 0;
	if ( !is_valid_email( $f_email ) ) {
		PRINT $f_email.' '.$s_invalid_email.'<p>';
		PRINT "<a href=\"signup_page.php\">$s_proceed</a>";
		exit;
	}

	# Check for duplicate username
	$query = "SELECT username
		FROM $g_mantis_user_table
		WHERE username='$c_username'";
    $result = db_query( $query );
    if ( db_num_rows( $result ) > 0 ) {
    	PRINT $f_username . $s_duplicate_username . '<p>';
	PRINT "<a href=\"signup_page.php\">$s_proceed</a>";
    	exit;
    }

	# Passed our checks.  Insert into DB then send email.
	if ( !signup_user( $f_username, $f_email ) ) {
		PRINT $s_account_create_fail.'<p>';
		PRINT "<a href=\"signup_page.php\">$s_proceed</a>";
		exit;
	}
?>
<?php print_page_top1() ?>
<?php
	print_head_bottom();
	print_body_top();
	print_header( $g_page_title );
	print_top_page( $g_top_include_page );
?>

<p>
<div align="center">
<?php
	if ( $result ) {						# SUCCESS
		PRINT "[$f_username - $f_email] $s_password_emailed_msg<p>$s_no_reponse_msg<p>";
	} else {								# FAILURE
		print_sql_error( $query );
	}

	print_bracket_link( 'login_page.php', $s_proceed );
?>
</div>

<?php print_page_bot1( __FILE__ ) ?>
