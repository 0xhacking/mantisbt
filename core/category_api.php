<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: category_api.php,v 1.5 2003-01-30 09:41:31 jfitzell Exp $
	# --------------------------------------------------------

	###########################################################################
	# Category API
	###########################################################################

	# --------------------
	# checks to see if the category is a duplicate
	# we do it this way because each different project can have the same category names
	# The old category name is excluded from the search for duplicate since a category
	# can re-take its name.  It is also useful when changing the case of a category name.
	# For example, "category" -> "Category".
	function is_duplicate_category( $p_project_id, $p_category , $p_old_category = '' ) {
		global $g_mantis_project_category_table;

		$c_project_id	= (integer)$p_project_id;
		$c_category		= addslashes($p_category);

		$query = "SELECT COUNT(*)
				FROM $g_mantis_project_category_table
				WHERE project_id='$c_project_id' AND
				category='$c_category'";

		if (strlen($p_old_category) != 0) {
			$c_old_category = addslashes($p_old_category);
			$query = $query . " AND category <> '$c_old_category'";
		}

		$result = db_query( $query );
		$category_count =  db_result( $result, 0, 0 );

		return ( $category_count > 0 );
	}
	# --------------------
	function category_add( $p_project_id, $p_category ) {
		global $g_mantis_project_category_table;

		$c_project_id	= (integer)$p_project_id;
		$c_category		= addslashes($p_category);

		$query = "INSERT
				INTO $g_mantis_project_category_table
				( project_id, category )
				VALUES
				( '$c_project_id', '$c_category' )";
		return db_query( $query );
	}
	# --------------------
	function category_update( $p_project_id, $p_category, $p_orig_category, $p_assigned_to ) {
		global $g_mantis_project_category_table;

		$c_project_id		= (integer)$p_project_id;
		$c_category			= addslashes($p_category);
		$c_orig_category	= addslashes($p_orig_category);
		$c_assigned_to		= (integer)$p_assigned_to;

		$query = "UPDATE $g_mantis_project_category_table
				SET category='$c_category', user_id=$c_assigned_to
				WHERE category='$c_orig_category' AND
					  project_id='$c_project_id'";
		return db_query( $query );
	}
	# --------------------
	function category_delete( $p_project_id, $p_category ) {
		global $g_mantis_project_category_table;

		$c_project_id	= (integer)$p_project_id;
		$c_category		= addslashes($p_category);

		$query = "DELETE
				FROM $g_mantis_project_category_table
				WHERE project_id='$c_project_id' AND
					  category='$c_category'";
		return db_query( $query );
	}
	# --------------------
	# return all categories for the specified project id
	function category_get_all_rows( $p_project_id ) {
		global $g_mantis_project_category_table;

		$c_project_id = db_prepare_int( $p_project_id );

		$t_project_category_table = config_get( 'mantis_project_category_table' );

		$query = "SELECT category, user_id
				FROM $t_project_category_table
				WHERE project_id='$c_project_id'
				ORDER BY category";
		$result = db_query( $query );

		$count = db_num_rows( $result );

		$rows = array();

		for ( $i = 0 ; $i < $count ; $i++ ) {
			$row = db_fetch_array( $result );

			$rows[] = $row;
		}

		return $rows;
	}
	# --------------------
	# delete all categories associated with a project
	function category_delete_all( $p_project_id ) {
		$c_project_id = db_prepare_int( $p_project_id );

		$t_project_category_table = config_get( 'mantis_project_category_table' );

		$query = "DELETE
				  FROM $t_project_category_table
				  WHERE project_id='$c_project_id'";

		db_query( $query );

		# db_query() errors on failure so:
		return true;
	}
?>