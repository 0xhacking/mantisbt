<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Id: string_api.php,v 1.6 2002-09-20 21:07:57 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# String Processing API
	###########################################################################

	# --------------------
	# every string that comes from a textarea should be processed through this
	# function *before* insertion into the database.
	#
	# @@@ Old function.  Use db_prepare_string() now.  Delete this at some point
	#
	function string_prepare_textarea( $p_string ) {
		global $g_allow_href_tags, $g_allow_html_tags;

		$p_string = htmlspecialchars( $p_string );

		if ( ON == $g_allow_html_tags ) {
			$p_string = filter_html_tags( $p_string );
		}

		if ( ON == $g_allow_href_tags ) {
			$p_string = filter_href_tags( $p_string );
		}

		$p_string = filter_img_tags( $p_string );
		$p_string = addslashes( $p_string );
		return $p_string;
	}
	# --------------------
	# every string that comes from a text field should be processed through this
	# function *before* insertion into the database.
	#
	# @@@ Old function.  Use db_prepare_string() now.  Delete this at some point
	#
	function string_prepare_text( $p_string ) {
		global $g_allow_href_tags, $g_allow_html_tags;

		$p_string = htmlspecialchars( $p_string );

		if ( ON == $g_allow_html_tags ) {
			$p_string = filter_html_tags( $p_string );
		}

		if ( ON == $g_allow_href_tags ) {
			$p_string = filter_href_tags( $p_string );
		}

		$p_string = filter_img_tags( $p_string );
		$p_string = addslashes( $p_string );
		return $p_string;
	}
	# --------------------
	# Use this to prepare a string for display to HTML
	function string_display( $p_string ) {
#		$p_string = stripslashes( $p_string );
		$p_string = string_process_bug_link( $p_string );
		$p_string = string_process_cvs_link( $p_string );
		$p_string = nl2br( $p_string );
		return $p_string;
	}
	# --------------------
	# Prepare a string for plain text display in email
	function string_email( $p_string ) {
#		$p_string = stripslashes( $p_string );
		$p_string = unfilter_href_tags( $p_string );
		$p_string = string_process_bug_link( $p_string, false );
		$p_string = string_process_cvs_link( $p_string, false );
		$p_string = str_replace( '&lt;', '<',  $p_string );
		$p_string = str_replace( '&gt;', '>',  $p_string );
		$p_string = str_replace( '&quot;', '"',  $p_string );
		$p_string = str_replace( '&amp;', '&',  $p_string );

		return $p_string;
	}
	# --------------------
	# Process a string for display in a textarea box
	function string_edit_textarea( $p_string ) {
#		$p_string = stripslashes( $p_string );
		$p_string = str_replace( '<br>', '',  $p_string );
		$p_string = unfilter_href_tags( $p_string );
		$p_string = str_replace( '<br />', '\n',  $p_string );
		$p_string = str_replace( '&lt;', '<',  $p_string );
		$p_string = str_replace( '&gt;', '>',  $p_string );
		$p_string = str_replace( '&quot;', '"',  $p_string );
		return $p_string;
	}
	# --------------------
	# Process a string for display in a text box
	function string_edit_text( $p_string ) {
#		$p_string = stripslashes( $p_string );
		$p_string = str_replace( '<br>', '',  $p_string );
		$p_string = unfilter_href_tags( $p_string );
		$p_string = str_replace( '&lt;', '<',  $p_string );
		$p_string = str_replace( '&gt;', '>',  $p_string );
		$p_string = str_replace( '&quot;', '\'',  $p_string );
		return $p_string;
	}

	# --------------------	
	# process the $p_string and convert filenames in the format
	#  cvs:filename.ext or cvs:filename.ext:n.nn to a html link
	# if $p_include_anchor is true, include an <a href="..."> tag,
	#  otherwise, just insert the URL as text
	function string_process_cvs_link( $p_string, $p_include_anchor=true ) {
		$t_cvs_web = config_get( 'cvs_web' );

		if ( $p_include_anchor ) {
			$t_replace_with = '[CVS] <a href="'.$t_cvs_web.'\\1?rev=\\4" target="_new">\\1</a>\\5';
		} else {
			$t_replace_with = '[CVS] '.$t_cvs_web.'\\1?rev=\\4\\5';
		}

		return preg_replace( '/cvs:([^\.\s:,\?!]+(\.[^\.\s:,\?!]+)*)(:)?(\d\.[\d\.]+)?([\W\s])?/i',
							 $t_replace_with,
							 $p_string );
	}
	# --------------------
	# process the $p_string and create links to bugs if warranted
	# Uses the bug_link_tag config variable to determine the bug link tag
	# eg. #45  or  bug:76
	# default is the # symbol.  You may substitue any pattern you want.
	# if $p_include_anchor is true, include an <a href="..."> tag,
	#  otherwise, just insert the URL as text
	function string_process_bug_link( $p_string, $p_include_anchor=true ) {
		$t_tag = config_get( 'bug_link_tag' );
		$t_path = config_get( 'path' );

		if ( ON == current_user_get_pref( 'advanced_view' ) ) {
			$t_page_name = 'view_bug_advanced_page.php';
		} else {
			$t_page_name = 'view_bug_page.php';
		}

		if ( $p_include_anchor ) {
			$t_replace_with = '<a href="'.$t_page_name.'?f_id=\\1">#\\1</a>';
		} else {
			$t_replace_with = $t_path.$t_page_name.'?f_id=\\1';
		}
		
		return preg_replace("/$t_tag([0-9]+)/",
								$t_replace_with,
								$p_string);
	}
	# --------------------
	# return an href anchor that links to a bug VIEW page for the given bug
	#  account for the user preference and site override
	function string_get_bug_link( $p_bug_id ) {
		$t_summary = bug_get_field( $p_bug_id, 'summary' );
		return '<a href="'.string_get_bug_link_plain( $p_bug_id ).'" title="'.$t_summary.'">'.$p_bug_id.'</a>';
	}
	# --------------------
	# return the name and GET parameters of a bug VIEW page for the given bug
	#  account for the user preference and site override
	function string_get_bug_link_plain( $p_bug_id ) {
		return string_get_bug_view_page().'?f_id='.$p_bug_id;
	}
	# --------------------
	# return the name of a bug VIEW page for the user
	#  account for the user preference and site override
	function string_get_bug_view_page() {
		switch ( config_get( 'show_view' ) ) {
		case BOTH:
			if ( ON == current_user_get_pref( 'advanced_view' ) ) {
				return 'view_bug_advanced_page.php';
			} else {
				return 'view_bug_page.php';
			}
			break;
		case SIMPLE_ONLY:
				return 'view_bug_page.php';
			break;
		case ADVANCED_ONLY:
				return 'view_bug_advanced_page.php';
			break;
		}
	}
	# --------------------
	# return an href anchor that links to a bug UPDATE page for the given bug
	#  account for the user preference and site override
	function string_get_bug_update_link( $p_bug_id ) {
		$t_summary = bug_get_field( $p_bug_id, 'summary' );
		return '<a href="'.string_get_bug_update_link_plain( $p_bug_id ).'" title="'.$t_summary.'">'.$p_bug_id.'</a>';
	}
	# --------------------
	# return the name and GET parameters of a bug UPDATE page for the given bug
	#  account for the user preference and site override
	function string_get_bug_update_link_plain( $p_bug_id ) {
		return string_get_bug_update_page().'?f_id='.$p_bug_id;
	}
	# --------------------
	# return the name of a bug UPDATE page for the user
	#  account for the user preference and site override
	function string_get_bug_update_page() {
		switch ( config_get( 'show_update' ) ) {
		case BOTH:
			if ( ON == current_user_get_pref( 'advanced_update' ) ) {
				return 'bug_update_advanced_page.php';
			} else {
				return 'bug_update_page.php';
			}
			break;
		case SIMPLE_ONLY:
				return 'bug_update_page.php';
			break;
		case ADVANCED_ONLY:
				return 'bug_update_advanced_page.php';
			break;
		}
	}
?>
