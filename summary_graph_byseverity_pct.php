<?php
	require_once( 'core.php' );

	login_cookie_check();

	# if user is below view summary threshold, then re-direct to mainpage.
	if ( !access_level_check_greater_or_equal( config_get( 'view_summary_threshold' ) ) ) {
		access_denied();
	}

	#centers the chart
	$center = 0.30;

	#position of the legend
	$poshorizontal = 0.10;
	$posvertical = 0.09;

	create_bug_enum_summary_pct( lang_get( 'severity_enum_string' ), 'severity');
	graph_bug_enum_summary_pct( lang_get( 'by_severity_pct' ) );
?>