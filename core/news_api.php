<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002         Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# --------------------------------------------------------
	# $Id: news_api.php,v 1.3 2002-08-27 19:59:18 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# News API
	###########################################################################

	# --------------------
	# Add a news item
	function news_create( $p_project_id, $p_poster_id, $p_view_state, $p_announcement, $p_headline, $p_body ) {
		$c_project_id	= db_prepare_int( $p_project_id );
		$c_poster_id	= db_prepare_int( $p_poster_id );
		$c_view_state	= db_prepare_int( $p_view_state );
		$c_announcement	= db_prepare_bool( $p_announcement );
		$c_headline		= db_prepare_string( $p_headline );
		$c_body			= db_prepare_string( $p_body );

		$t_news_table = config_get( 'mantis_news_table' );

		# Add item
		$query = "INSERT
				INTO $t_news_table
	    		  ( id, project_id, poster_id, date_posted, last_modified,
	    		    view_state, announcement, headline, body )
				VALUES
				  ( null, '$c_project_id', '$c_poster_id', NOW(), NOW(),
				    '$c_view_state', '$c_announcement', '$c_headline', '$c_body' )";
	    db_query( $query );

		# db_query() errors on failure so:
		return true;
	}
	# --------------------
	# Delete the news entry
	function news_delete( $p_news_id ) {
		$c_news_id = db_prepare_int( $p_news_id );

		$t_news_table = config_get( 'mantis_news_table' );

		$query = "DELETE
				  FROM $t_news_table
	    		  WHERE id='$c_news_id'";

	   db_query( $query );

		# db_query() errors on failure so:
		return true;
	}
	# --------------------
	# Update news item
	function news_update( $p_news_id, $p_project_id, $p_view_state, $p_announcement, $p_headline, $p_body ) {
		$c_news_id		= db_prepare_int( $p_news_id );
		$c_project_id	= db_prepare_int( $p_project_id );
		$c_view_state	= db_prepare_int( $p_view_state );
		$c_announcement	= db_prepare_bool( $p_announcement );
		$c_headline		= db_prepare_string( $p_headline );
		$c_body			= db_prepare_string( $p_body );

		$t_news_table = config_get( 'mantis_news_table' );

		# Update entry
		$query = "UPDATE $t_news_table
				  SET view_state='$c_view_state',
					announcement='$c_announcement',
					headline='$c_headline',
					body='$c_body',
					project_id='$c_project_id',
					last_modified=NOW()
				  WHERE id='$c_news_id'";
		
		db_query( $query );

		# db_query() errors on failure so:
		return true;
	}
	# --------------------
	# Selects the news item associated with the specified id
	function news_get_row( $p_news_id ) {
		$c_news_id = db_prepare_int( $p_news_id );

		$t_news_table = config_get( 'mantis_news_table' );

		$query = "SELECT *, UNIX_TIMESTAMP(date_posted) as date_posted
				  FROM $t_news_table
				  WHERE id='$c_news_id'";
	    $result = db_query( $query );

		if ( 0 == db_num_rows( $result ) ) {
			trigger_error( ERROR_NEWS_NOT_FOUND, ERROR );
		} else {
			return db_fetch_array( $result );
		}
	}
	# --------------------
	# get news count (selected project plus sitewide posts)
	function news_get_count( $p_project_id ) {
		$c_project_id = db_prepare_int( $p_project_id );

		$t_news_table = config_get( 'mantis_news_table' );

		$query = "SELECT COUNT(*)
				  FROM $t_news_table
				  WHERE project_id='$c_project_id' OR project_id='0000000'";

		$result = db_query( $query );

	    return db_result( $result, 0, 0 );
	}
	# --------------------
?>