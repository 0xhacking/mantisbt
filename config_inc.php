<?
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	###########################################################################
	### CONFIGURATION VARIABLES                                             ###
	###########################################################################

	#--------------------
	# database variables
	$g_hostname        = "localhost";    # set this
	$g_port            = 3306;           # set this if not default
	$g_db_username     = "root";         # set this
	$g_db_password     = "";             # set this
	$g_database_name   = "bugtracker";   # set this
	#--------------------

	#--------------------
	# path to your installation as seen from the web browser
	$g_path            = "/mantis/";   # requires trailing /
	#--------------------

	#--------------------
	# extensions for php 3 and php 4
	# set this to php for php4 or whatever your webserver needs
	$g_php             = ".php3";
	#--------------------

	#--------------------
	# table name prefix
	# if you change this remember to reflect the changes in the database
	$g_db_table_prefix = "mantis";
	#--------------------

	#--------------------
	# email variables
	$g_administrator_email  = "administrator@mydomain.com";   # set this
	$g_webmaster_email      = "webmaster@mydomain.com";       # set this
	$g_from_email           = "nobody@mydomain.com";          # set this
	#--------------------

	#--------------------
	# enter the exact url where the bugtracker is located
	# used in the emailing system
	$g_mantis_url            = "http://www.mydomain.com/mantis/";
	#--------------------

	#--------------------
	# sitewide titles
	$g_window_title         = "Mantis";     # browser window title
	$g_page_title           = "Mantis";     # title at top of html page
	#--------------------

	#--------------------
	# toggling advanced interfaces      # 1 to enable - 0 to disable
	$g_show_advanced_report      = 1;
	$g_show_advanced_update      = 1;

	### allow users to signup for their own accounts
	$g_allow_signup              = 1;

	$g_show_version              = 0;

	# display a link at the bottom of the page to show the PHP source
	# requires PHP4
	# 0 = disabled; 1 = admin only; 2 = anyone
	$g_show_source               = 0;

	# Footer Menu
	# 0 = disabled
	# 1 = show menu at bottom
	$g_show_footer_menu          = 0;

	# change to language you want... choices are:
	# english, spanish, portuguese
	$g_language                  = "english";

	# Allow email
	$g_enable_email_notification = 1;

	# set to 0 to disable email check
	$g_validate_email            = 1;
	#--------------------

	#--------------------
	# time for 'permanent' cookie to live in seconds
	$g_cookie_time_length        = 30000000;     # 1 year
	# time to delay between page redirects
	$g_wait_time                 = 2;            # in seconds
	#--------------------
	### minutes to wait before document is stale
	$g_content_expire            = 0;			 # in minutes

	#--------------------
	# limit the number of news items to be displayed on the main page
	$g_news_view_limit           = 5;
	#--------------------

	#--------------------
	# defaults for viewing preferences
	$g_default_limit_view       = 50;
	$g_default_show_changed     = 6;
	$g_default_advanced_report  = "";  # set to on to enable
	$g_default_advanced_view    = "";  # set to on to enable
	#--------------------

	#--------------------
	# bugnote ordering
	# change to ASC or DESC
	$g_bugnote_order = "ASC";
	#--------------------

	#--------------------
	# date lengths to count bugs by
	# folows the english required by strtotime()
	$g_date_partitions = array("1 day","3 days","1 week","1 month","1 year");
	#--------------------

	#--------------------
	# date format strings
	# go to http://www.php.net/manual/en/function.date.php
	# for detailed instructions on date formatting
	$g_short_date_format    = "m-d";
	$g_normal_date_format   = "m-d H:i";
	$g_complete_date_format = "m-d-y H:i T";
	#--------------------

	#--------------------
	# html table appearance variables
	$g_primary_table_tags          = "";
	#--------------------

	#--------------------
	# color values
	$g_white_color                 = "#ffffff";    # white

	$g_primary_color_dark          = "#d8d8d8";    # gray
	$g_primary_color_light         = "#e8e8e8";    # light gray
	$g_primary_border_color        = "#aaaaaa";    # dark gray
	$g_category_title_color        = "#c8c8e8";    # bluish
	$g_category_title_color2       = "#c0c0c8";    # gray bluish

	$g_table_title_color           = "#ffffff";    # white

	$g_new_color                   = "#ffa0a0";    # red
	$g_feedback_color              = "#ff50a8";    # purple
	$g_acknowledged_color          = "#ffd850";    # orange
	$g_confirmed_color             = "#ffffb0";    # yellow
	$g_assigned_color              = "#c8c8ff";    # blue
	$g_resolved_color              = "#ffffff";    # not used in default
	#--------------------

	#--------------------
	# set this to a unique identifier
	$g_cookie_prefix     = "MANTIS";

	# cookie names
	$g_string_cookie                  = $g_cookie_prefix."_STRING_COOKIE";
	$g_project_cookie                 = $g_cookie_prefix."_PROJECT_COOKIE";
	$g_view_all_cookie                = $g_cookie_prefix."_VIEW_ALL_COOKIE";
	$g_view_reported_cookie           = $g_cookie_prefix."_VIEW_REPORTED_COOKIE";
	$g_view_assigned_cookie           = $g_cookie_prefix."_VIEW_ASSIGNED_COOKIE";

	# cookie values

	$g_string_cookie_val              = "";
	$g_project_cookie_val             = "";
	$g_view_all_cookie_val            = "";
	$g_view_reported_cookie_val       = "";
	$g_view_assigned_cookie_val       = "";

	if ( isset( $g_string_cookie ) ) {
		$g_string_cookie_val              = $HTTP_COOKIE_VARS[$g_string_cookie];
	}
	if ( isset( $g_project_cookie ) ) {
		$g_project_cookie_val             = $HTTP_COOKIE_VARS[$g_project_cookie];
	}
	if ( isset( $g_view_all_cookie ) ) {
		$g_view_all_cookie_val            = $HTTP_COOKIE_VARS[$g_view_all_cookie];
	}
	if ( isset( $g_view_reported_cookie ) ) {
		$g_view_reported_cookie_val       = $HTTP_COOKIE_VARS[$g_view_reported_cookie];
	}
	if ( isset( $g_view_assigned_cookie ) ) {
		$g_view_assigned_cookie_val       = $HTTP_COOKIE_VARS[$g_view_assigned_cookie];
	}
	#--------------------

	#--------------------
	# database table names
	$g_mantis_bug_table               = $g_db_table_prefix."_bug_table";
	$g_mantis_bug_text_table          = $g_db_table_prefix."_bug_text_table";
	$g_mantis_bugnote_table           = $g_db_table_prefix."_bugnote_table";
	$g_mantis_bugnote_text_table      = $g_db_table_prefix."_bugnote_text_table";
	$g_mantis_news_table              = $g_db_table_prefix."_news_table";
	$g_mantis_project_category_table  = $g_db_table_prefix."_project_category_table";
	$g_mantis_project_table           = $g_db_table_prefix."_project_table";
	#$g_mantis_project_user_list_table = $g_db_table_prefix."_project_user_list_table";
	$g_mantis_project_version_table   = $g_db_table_prefix."_project_version_table";
	$g_mantis_user_table              = $g_db_table_prefix."_user_table";
	$g_mantis_user_profile_table      = $g_db_table_prefix."_user_profile_table";
	$g_mantis_user_pref_table         = $g_db_table_prefix."_user_pref_table";
	#--------------------

	#--------------------
	# customizable pages

	# specifiy your top/bottom include file (logos, banners, etc)
	$g_bottom_include_page            = "";
	$g_top_include_page               = "";
	# css
	$g_css_include_file               = "css_inc.php";
	# meta tags
	$g_meta_include_file              = "meta_inc.php";

	#--------------------
	# core file variables
	$g_core_API_file                  = "core_API.php";
	$g_menu_include_file              = "menu_inc.php";
	#--------------------

	#--------------------
	# bugnote includes
	$g_bugnote_include_file           = "bugnote_inc.php";
	$g_bugnote_add_include_file       = "bugnote_add_inc.php";
	#--------------------

	#--------------------
	# page names
	$g_index                          = "index".$g_php;
	$g_main_page                      = "main_page".$g_php;

	# bug view/update
	$g_view_bug_all_page              = "view_bug_all_page".$g_php;
	$g_view_user_assigned_bug_page    = "view_user_assigned_bug_page".$g_php;
	$g_view_user_reported_bug_page    = "view_user_reported_bug_page".$g_php;

	$g_view_bug_page                  = "view_bug_page".$g_php;
	$g_view_bug_advanced_page         = "view_bug_advanced_page".$g_php;

	$g_bug_assign                     = "bug_assign".$g_php;

	$g_bug_delete_page                = "bug_delete_page".$g_php;
	$g_bug_delete                     = "bug_delete".$g_php;
	$g_bug_update_page                = "bug_update_page".$g_php;
	$g_bug_update_advanced_page       = "bug_update_advanced_page".$g_php;
	$g_bug_update                     = "bug_update".$g_php;

	$g_bug_reopen_page                = "bug_reopen_page".$g_php;

	$g_bug_resolve_page               = "bug_resolve_page".$g_php;
	$g_bug_resolve_page2              = "bug_resolve_page2".$g_php;

	# vote
	$g_bug_vote_add                   = "bug_vote_add".$g_php;

	# bugnote
	$g_bugnote_add_page               = "bugnote_add_page".$g_php;
	$g_bugnote_add                    = "bugnote_add".$g_php;
	$g_bugnote_delete                 = "bugnote_delete".$g_php;

	# documentation
	$g_documentation_html             = "documentation.html";

	# report bug
	$g_report_bug_page                = "report_bug_page".$g_php;
	$g_report_bug_advanced_page       = "report_bug_advanced_page".$g_php;
	$g_report_add                     = "report_add".$g_php;

	# summary
	$g_summary_page                   = "summary_page".$g_php;

	# user feedback
	$g_view_user_reported_bug_page    = "view_user_reported_bug_page".$g_php;
	$g_view_user_assigned_bug_page    = "view_user_assigned_bug_page".$g_php;

	#signup
	$g_signup_page                    = "signup_page".$g_php;
	$g_signup                         = "signup".$g_php;

	# account
	$g_account_page                   = "account_page".$g_php;
	$g_account_update                 = "account_update".$g_php;
	$g_account_delete_page            = "account_delete_page".$g_php;
	$g_account_delete                 = "account_delete".$g_php;

	$g_account_profile_manage_page    = "account_profile_manage_page".$g_php;
	$g_account_profile_add            = "account_profile_add".$g_php;
	$g_account_profile_edit_page      = "account_profile_edit_page".$g_php;
	$g_account_profile_update         = "account_profile_update".$g_php;
	$g_account_profile_delete         = "account_profile_delete".$g_php;
	$g_account_profile_make_default   = "account_profile_make_default".$g_php;

	$g_account_prefs_page             = "account_prefs_page".$g_php;
	$g_account_prefs_update           = "account_prefs_update".$g_php;
	$g_account_prefs_reset            = "account_prefs_reset".$g_php;

	# site management
	$g_manage_page                    = "manage_page".$g_php;
	$g_manage_create_new_user         = "manage_create_new_user".$g_php;
	$g_manage_create_user_page        = "manage_create_user_page".$g_php;

	$g_manage_user_page               = "manage_user_page".$g_php;
	$g_manage_user_update             = "manage_user_update".$g_php;
	$g_manage_user_reset              = "manage_user_reset".$g_php;
	$g_manage_user_delete_page        = "manage_user_delete_page".$g_php;
	$g_manage_user_delete             = "manage_user_delete".$g_php;

	$g_documentation_page             = "documentation_page".$g_php;

	# multiple projects
	$g_manage_project_menu_page       = "manage_project_menu_page".$g_php;
	$g_manage_project_add             = "manage_project_add".$g_php;
	$g_manage_project_edit_page       = "manage_project_edit_page".$g_php;
	$g_manage_project_update          = "manage_project_update".$g_php;
	$g_manage_project_delete          = "manage_project_delete".$g_php;
	$g_manage_project_delete_page     = "manage_project_delete_page".$g_php;

	$g_set_project                    = "set_project".$g_php;

	# unused
	#$g_choose_project_page            = "choose_project_page".$g_php;

	# manage multiple project users
	$g_manage_project_user_menu_page    = "manage_project_user_menu_page".$g_php;
	$g_manage_project_user_add          = "manage_project_user_add".$g_php;
	$g_manage_project_user_edit_page    = "manage_project_user_edit_page".$g_php;
	$g_manage_project_user_update       = "manage_project_user_update".$g_php;
	$g_manage_project_user_delete       = "manage_project_user_delete".$g_php;
	$g_manage_project_user_delete_page  = "manage_project_user_delete_page".$g_php;

	# project versions
	$g_manage_project_version_add       = "manage_project_version_add".$g_php;
	$g_manage_project_version_update    = "manage_project_version_update".$g_php;
	$g_manage_project_version_delete    = "manage_project_version_delete".$g_php;
	$g_manage_project_version_delete_page = "manage_project_version_delete_page".$g_php;
	$g_manage_project_version_edit_page  = "manage_project_version_edit_page".$g_php;

	# project category
	$g_manage_project_category_add       = "manage_project_category_add".$g_php;
	$g_manage_project_category_update    = "manage_project_category_update".$g_php;
	$g_manage_project_category_delete    = "manage_project_category_delete".$g_php;
	$g_manage_project_category_delete_page = "manage_project_category_delete_page".$g_php;
	$g_manage_project_category_edit_page  = "manage_project_category_edit_page".$g_php;

	# news
	$g_news_menu_page                 = "news_menu_page".$g_php;
	$g_news_edit_page                 = "news_edit_page".$g_php;
	$g_news_add                       = "news_add".$g_php;
	$g_news_update                    = "news_update".$g_php;
	$g_news_delete_page               = "news_delete_page".$g_php;
	$g_news_delete                    = "news_delete".$g_php;

	# userland documentation
	$g_usage_doc_page                 = "documentation.html";

	# login
	$g_login                          = "login".$g_php;
	$g_login_page                     = "login_page".$g_php;
	$g_login_error_page               = "login_error_page".$g_php;
	$g_login_success_page             = "index".$g_php;
	$g_logout_page                    = "logout_page".$g_php;
	$g_logout_redirect_page           = ".";

	# debug only
	$g_show_source_page               = "show_source_page".$g_php;

	# errors
	#--------------------

	#--------------------
	#version
	$g_mantis_version = "0.14.4";
	#--------------------
?>