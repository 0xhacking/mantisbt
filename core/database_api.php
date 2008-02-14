<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

	# --------------------------------------------------------
	# $Id$
	# --------------------------------------------------------

	### Database ###

	# This is the general interface for all database calls.
	# Use this as a starting point to port to other databases

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'gpc_api.php' );

	# Do not explicitly include $t_core_dir to allow using system ADODB by including
	# it in include path and removing the one distributed with Mantis (see #7907).
	require_once( 'adodb/adodb.inc.php' );

	# An array in which all executed queries are stored.  This is used for profiling
	$g_queries_array = array();

	# Stores whether a database connection was succesfully opened.
	$g_db_connected = false;
	
	# Store whether to log queries ( used for show_queries_count/query list)
	$g_db_log_queries = config_get_global( 'show_queries_count' );

	# set adodb fetch mode
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	# --------------------
	# Make a connection to the database
	function db_connect( $p_dsn, $p_hostname = null, $p_username = null, $p_password = null, $p_database_name = null, $p_db_schema = null, $p_pconnect = false ) {
		global $g_db_connected, $g_db;

		if ( $p_dsn === false ) {
			$t_db_type = config_get_global( 'db_type' );
			$g_db = ADONewConnection( $t_db_type );

			if ( $p_pconnect ) {
				$t_result = $g_db->PConnect( $p_hostname, $p_username, $p_password, $p_database_name );
			} else {
				$t_result = $g_db->Connect( $p_hostname, $p_username, $p_password, $p_database_name );
			}
		} else {
			$g_db = ADONewConnection( $p_dsn );
			$t_result = $g_db->IsConnected();
		}

		if ( $t_result ) {
			# For MySQL, the charset for the connection needs to be specified.
			if ( db_is_mysql() ) {
				# @@@ Is there a way to translate any charset name to MySQL format? e.g. remote the dashes?
				# @@@ Is this needed for other databases?
				if ( strtolower( lang_get( 'charset' ) ) === 'utf-8' ) {
					db_query_bound( 'SET NAMES UTF8' );
				}
			} elseif ( db_is_db2() && $p_db_schema !== null && !is_blank( $p_db_schema ) ) {
				$t_result2 = db_query_bound( 'set schema ' . $p_db_schema );
				if ( $t_result2 === false ) {
					db_error();
					trigger_error( ERROR_DB_CONNECT_FAILED, ERROR );
					return false;
				}
			}
		} else {
			db_error();
			trigger_error( ERROR_DB_CONNECT_FAILED, ERROR );
			return false;
		}

		$g_db_connected = true;

		return true;
	}

	# --------------------
	# Make a persistent connection to the database
	function db_pconnect( $p_dsn, $p_hostname = null, $p_username = null, $p_password = null, $p_database_name = null, $p_db_schema = null ) {
		return db_connect( $p_dsn, $p_hostname, $p_username, $p_password, $p_database_name, $p_db_schema, /* $p_pconnect */ true );
	}

	# --------------------
	# Returns whether a connection to the database exists
	function db_is_connected() {
		global $g_db_connected;

		return $g_db_connected;
	}

	# --------------------
	# Checks if the database is MySQL
	function db_is_mysql() {
		$t_db_type = config_get_global( 'db_type' );

		switch( $t_db_type ) {
			case 'mysql':
			case 'mysqli':
				return true;
		}

		return false;
	}

	# --------------------
	# Check is the database is PostgreSQL
	function db_is_pgsql() {
		$t_db_type = config_get_global( 'db_type' );

		switch( $t_db_type ) {
			case 'postgres':
			case 'postgres64':
			case 'postgres7':
			case 'pgsql':
				return true;
		}

		return false;
	}

	# --------------------
	# Check is the database is DB2
	function db_is_db2() {
		$t_db_type = config_get_global( 'db_type' );

		switch( $t_db_type ) {
			case 'db2':
				return true;
		}

		return false;
	}

	# --------------------
	# execute query, requires connection to be opened
	# If $p_error_on_failure is true (default) an error will be triggered
	#  if there is a problem executing the query.
	function db_query( $p_query, $p_limit = -1, $p_offset = -1 ) {
		global $g_queries_array, $g_db, $g_db_log_queries;

		if ( ON == $g_db_log_queries ) {		
			$t_start = microtime_float();

			$t_backtrace = debug_backtrace();
			$t_caller = basename( $t_backtrace[0]['file'] );
			$t_caller .= ":" . $t_backtrace[0]['line'];
			
			# Is this called from another function?
			if ( isset( $t_backtrace[1] ) ) {
				$t_caller .= ' ' . $t_backtrace[1]['function'] . '()';
			} else {
				# or from a script directly?
				$t_caller .= ' ' . $_SERVER['PHP_SELF'];
			}
		}
		
		if ( ( $p_limit != -1 ) || ( $p_offset != -1 ) ) {
			$t_result = $g_db->SelectLimit( $p_query, $p_limit, $p_offset );
		} else {
			$t_result = $g_db->Execute( $p_query );
		}

		if ( ON == $g_db_log_queries ) {		
			$t_elapsed = number_format( microtime_float() - $t_start, 4);

			array_push ( $g_queries_array, array( $p_query, $t_elapsed, $t_caller ) );
		}
		
		if ( !$t_result ) {
			db_error($p_query);
			trigger_error( ERROR_DB_QUERY_FAILED, ERROR );
			return false;
		} else {
			return $t_result;
		}
	}

	function db_query_bound($p_query, $arr_parms = null, $p_limit = -1, $p_offset = -1 )
	{
		global $g_queries_array, $g_db, $g_db_log_queries;

		if ( ON == $g_db_log_queries ) {		
			$t_db_type = config_get_global( 'db_type' );

			$t_start = microtime_float();
			
			$t_backtrace = debug_backtrace();
			$t_caller = basename( $t_backtrace[0]['file'] );
			$t_caller .= ":" . $t_backtrace[0]['line'];
	
			# Is this called from another function?
			if ( isset( $t_backtrace[1] ) ) {
				$t_caller .= ' ' . $t_backtrace[1]['function'] . '()';
			} else {
				# or from a script directly?
				$t_caller .= ' ' . $_SERVER['PHP_SELF'];
			}
		}
		
		if ( ( $p_limit != -1 ) || ( $p_offset != -1 ) ) {
			$t_result = $g_db->SelectLimit( $p_query, $p_limit, $p_offset, $arr_parms );
		} else {
			$t_result = $g_db->Execute( $p_query, $arr_parms );
		}

		if ( ON == $g_db_log_queries ) {		
			$t_elapsed = number_format( microtime_float() - $t_start, 4);

			$lastoffset = 0; $i = 1;
			if ( !is_null( $arr_parms ) ) {
				if ($arr_parms[0] === null) {
				  debug_print_backtrace();
				}
				while (preg_match('/(\?)/', $p_query, $matches, PREG_OFFSET_CAPTURE, $lastoffset)) {
					if ( $i <= count($arr_parms)) {
						if (is_null($arr_parms[$i-1]))
							$replace = 'NULL';
						else if(is_string($arr_parms[$i-1]))
							$replace = "'" . $arr_parms[$i-1] . "'";
						else if(is_integer($arr_parms[$i-1]) || is_float($arr_parms[$i-1]))
							$replace = (float)$arr_parms[$i-1];
						else if(is_bool($arr_parms[$i-1]))
							switch( $t_db_type ) {
									case 'pgsql':
										$replace = "'" . $arr_parms[$i-1] . "'";
										break;
									default:
										$replace = $arr_parms[$i-1];
										break;
							}
						else {
							echo("Invalid argument type passed to query_bound(): $i");
							exit(1);
						}
						$p_query = substr($p_query, 0, $matches[1][1]) . $replace . substr($p_query, $matches[1][1] + strlen($matches[1][0]));
						$lastoffset = $matches[1][1] + strlen($replace);
					} else {
						$lastoffset = $matches[1][1] + 1;
					}
					$i++;
				}
			}

			array_push ( $g_queries_array, array( $p_query, $t_elapsed, $t_caller ) );
		}

		if ( !$t_result ) {
			db_error($p_query);
			trigger_error( ERROR_DB_QUERY_FAILED, ERROR );
			return false;
		} else {
			return $t_result;
		}
	}

	# --------------------
	function db_param($p_param) {
		global $g_db;
		return $g_db->Param($p_param);
	}

	# --------------------
	function db_num_rows( $p_result ) {
		global $g_db;

		return $p_result->RecordCount( );
	}

	# --------------------
	function db_affected_rows() {
		global $g_db;

		return $g_db->Affected_Rows( );
	}

	# --------------------
	function db_fetch_array( & $p_result ) {
		global $g_db, $g_db_type;

		if ( $p_result->EOF ) {
			return false;
		}

		# mysql obeys FETCH_MODE_BOTH, hence ->fields works, other drivers do not support this
		if( $g_db_type == 'mysql' ) {
			$t_array = $p_result->fields;
 			$p_result->MoveNext();
			return $t_array;
		} else {
			$t_row = $p_result->GetRowAssoc(false);

			for( $i = 0 ; $i < $p_result->FieldCount() ; $i++ ) {
				$t_field = $p_result->FetchField( $i );
				switch( $t_field->type ) {
					case 'bool':
						switch( $t_row[$t_field->name] ) {
							case 'f':
								$t_row[$t_field->name] = false;
								break;
							case 't':
								$t_row[$t_field->name] = true;
								break;
						}
						break;

					default:
						break;
				}
			}
			$p_result->MoveNext();
			return $t_row;
		}
	}

	# --------------------
	function db_result( $p_result, $p_index1=0, $p_index2=0 ) {
		global $g_db;

		if ( $p_result && ( db_num_rows( $p_result ) > 0 ) ) {
			$p_result->Move( $p_index1 );
			$t_result = $p_result->GetArray();

			if ( isset( $t_result[0][$p_index2] ) ) {
				return $t_result[0][$p_index2];
			}

			// The numeric index doesn't exist. FETCH_MODE_ASSOC may have been used.
			// Get 2nd dimension and make it numerically indexed
			$t_result = array_values( $t_result[0] );
			return $t_result[$p_index2];
		}

		return false;
	}

	# --------------------
	# return the last inserted id
	function db_insert_id($p_table = null) {
		global $g_db;

		if ( isset($p_table) && db_is_pgsql() ) {
			$query = "SELECT currval('".$p_table."_id_seq')";
			$result = db_query_bound( $query );
			return db_result($result);
		}
		return $g_db->Insert_ID( );
	}

	# --------------------
	# Check if the specified table exists.
	# @param $p_table_name  Table name.
	# @returns true: table found, false: table not found.
	function db_table_exists( $p_table_name ) {
		global $g_db, $g_db_schema;

		if ( is_blank( $p_table_name ) ) {
			return false; // no tables found
		}

		if ( db_is_db2() ) {
			// must pass schema
			$t_tables = $g_db->MetaTables( 'TABLE', false, '', $g_db_schema );
		} else {
			$t_tables = $g_db->MetaTables( 'TABLE' );
		}

		# Can't use in_array() since it is case sensitive
		$t_table_name = strtolower( $p_table_name );
		foreach ( $t_tables as $t_current_table ) {
			if ( strtolower( $t_current_table ) == $t_table_name ) {
				return true;
			}
		}

		return false;
	}

	# --------------------
	function db_field_exists( $p_field_name, $p_table_name ) {
		global $g_db;
		return in_array ( $p_field_name , $g_db->MetaColumnNames( $p_table_name ) ) ;
	}

	# --------------------
	function db_field_names( $p_table_name ) {
		global $g_db;
		return $g_db->MetaColumnNames( $p_table_name );
	}

	# --------------------
	# Check if there is an index defined on the specified table/field and with
	# the specified type.
	#
	# @@@ thraxisp - this only works with MySQL
	#
	# $p_table: Name of table to check
	# $p_field: Name of field to check
	# $p_key: key type to check for (eg: PRI, MUL, ...etc)
	function db_key_exists_on_field( $p_table, $p_field, $p_key ) {
		$c_table = db_prepare_string( $p_table );
		$c_field = db_prepare_string( $p_field );
		$c_key   = db_prepare_string( $p_key );

		$query = "DESCRIBE $c_table";
		$result = db_query_bound( $query );
		$count = db_num_rows( $result );
		for ( $i=0 ; $i < $count ; $i++ ) {
			$row = db_fetch_array( $result );

			if ( $row['Field'] == $c_field ) {
				return ( $row['Key'] == $c_key );
			}
		}
		return false;
	}

	# --------------------
	function db_error_num() {
		global $g_db;

		return $g_db->ErrorNo();
	}

	# --------------------
	function db_error_msg() {
		global $g_db;

		return $g_db->ErrorMsg();
	}

	# --------------------
	# display both the error num and error msg
	function db_error( $p_query=null ) {
		if ( null !== $p_query ) {
			error_parameters( db_error_num(), db_error_msg(), $p_query );
		} else {
			error_parameters( db_error_num(), db_error_msg() );
		}
	}

	# --------------------
	# close the connection.
	# Not really necessary most of the time since a connection is
	# automatically closed when a page finishes loading.
	function db_close() {
		global $g_db;

		$t_result = $g_db->Close();
	}

	# --------------------
	# prepare a string before DB insertion
	# @@@ should default be return addslashes( $p_string ); or generate an error
	# @@@ Consider using ADODB escaping for all databases.
	function db_prepare_string( $p_string ) {
		global $g_db;
		$t_db_type = config_get_global( 'db_type' );

		switch( $t_db_type ) {
			case 'mssql':
			case 'odbc_mssql':
			case 'ado_mssql':
				if( ini_get( 'magic_quotes_sybase' ) ) {
					return addslashes( $p_string );
				} else {
					ini_set( 'magic_quotes_sybase', true );
					$t_string = addslashes( $p_string );
					ini_set( 'magic_quotes_sybase', false );
					return $t_string;
				}
				# just making a point with the superfluous break;s  I know it does not execute after a return  ;-)
				break;
			case 'db2':
				$t_escaped = $g_db->qstr( $p_string, false );
				return substr( $t_escaped, 1, strlen( $t_escaped ) - 2 );
				break;
			case 'mssql':
				break;
			case 'odbc_mssql':
				break;
			case 'mysql':
				return mysql_real_escape_string( $p_string );

			# For some reason mysqli_escape_string( $p_string ) always returns an empty
			# string.  This is happening with PHP v5.0.2.
			case 'mysqli':
				$t_escaped = $g_db->qstr( $p_string, false );
				return substr( $t_escaped, 1, strlen( $t_escaped ) - 2 );

			case 'postgres':
			case 'postgres64':
			case 'postgres7':
			case 'pgsql':
				return pg_escape_string( $p_string );

			default:
				error_parameters( 'db_type', $t_db_type );
				trigger_error( ERROR_CONFIG_OPT_INVALID, ERROR );
		}
	}

	# --------------------
	# prepare a binary string before DB insertion
	function db_prepare_binary_string( $p_string ) {
		$t_db_type = config_get_global( 'db_type' );

		switch( $t_db_type ) {
			case 'mssql':
			case 'odbc_mssql':
			case 'ado_mssql':
				$content = unpack("H*hex", $p_string);
				return '0x' . $content['hex'];
				break;
			default:
				return '\'' . db_prepare_string( $p_string ) . '\'';
				break;
		}
	}

	# prepare a date for binding in the format database accepts.
	# @param p_date can be a Unix integer timestamp or an ISO format Y-m-d. If null or false or '' is passed in, it will be converted to an SQL null.
	function db_bind_date( $p_date ) {
		global $g_db;
		return $g_db->BindDate( $p_date );		
	}

	function db_bind_timestamp( $p_date ) {
		global $g_db;
		return $g_db->BindTimeStamp( $p_date );		
	}	

	# --------------------
	# prepare an integer before DB insertion
	function db_prepare_int( $p_int ) {
		return (int)$p_int;
	}

	# --------------------
	# prepare a double before DB insertion
	function db_prepare_double( $p_double ) {
		return (double)$p_double;
	}

	# --------------------
	# prepare a boolean before DB insertion
	function db_prepare_bool( $p_bool ) {
		return (int)(bool)$p_bool;
	}

	# --------------------
	# return current timestamp for DB
	function db_now() {
		global $g_db;

		return $g_db->BindTimeStamp(time());
	}

	# --------------------
	# generate a unixtimestamp of a date
	# > SELECT UNIX_TIMESTAMP();
	#	-> 882226357
	# > SELECT UNIX_TIMESTAMP('1997-10-04 22:23:00');
	#	-> 875996580
	function db_timestamp( $p_date=null ) {
		global $g_db;

		if ( null !== $p_date ) {
			$p_timestamp = $g_db->UnixTimeStamp($p_date);
		} else {
			$p_timestamp = time();
		}
		return $g_db->BindTimeStamp($p_timestamp) ;
	}

	function db_unixtimestamp( $p_date=null ) {
		global $g_db;

		if ( null !== $p_date ) {
			$p_timestamp = $g_db->UnixTimeStamp($p_date);
		} else {
			$p_timestamp = time();
		}
		return $p_timestamp ;
	}

    # convert unix timestamp to db compatible date
	function db_date( $p_timestamp=null ) {
		global $g_db;

		if ( null !== $p_timestamp ) {
			$p_date = $g_db->UserTimeStamp($p_timestamp);
		} else {
			$p_date = $g_db->UserTimeStamp(time());
		}
		return $p_date;
	}


	# --------------------
	# convert minutes to a time format [h]h:mm
	function db_minutes_to_hhmm( $p_min = 0 ) {
		return sprintf( '%02d:%02d', $p_min / 60, $p_min % 60 );
	}

	# --------------------
	# A helper function that generates a case-sensitive or case-insensitive like phrase based on the current db type.
	# $p_field_name - The name of the field to filter on.
	# $p_value - The value that includes the pattern (can include % for wild cards) - not including the quotations.
	# $p_case_sensitive - true: case sensitive, false: case insensitive
	# returns (field LIKE 'value') OR (field ILIKE 'value')
	# The field name and value are assumed to be safe to insert in a query (i.e. already cleaned).
	function db_helper_like( $p_field_name, $p_param_id, $p_case_sensitive = false ) {
		$t_like_keyword = 'LIKE';

		if ( $p_case_sensitive === false ) {
			if ( db_is_pgsql() ) {
				$t_like_keyword = 'ILIKE';
			}
		}

		return "($p_field_name $t_like_keyword " . db_param( $p_param_id ) . ')';
	}

	# --------------------
	# helper function to compare two dates against a certain number of days
	# limitstring can be '> 1' '<= 2 ' etc
	# @@@ Check if there is a way to do that using ADODB rather than implementing it here.
	function db_helper_compare_days($p_date1_id_or_column, $p_date2_id_or_column, $p_limitstring) {
		$t_db_type = config_get_global( 'db_type' );

		$p_date1 = $p_date1_id_or_column;
		$p_date2 = $p_date2_id_or_column;
		if( is_int( $p_date1_id_or_column ) ) {
			$p_date1 = db_param( $p_date1_id_or_column );
		}
		if( is_int( $p_date2_id_or_column ) ) { 
			$p_date2 = db_param( $p_date2_id_or_column );
		}
		switch( $t_db_type ) {
			case 'mssql':
			case 'odbc_mssql':
			case 'ado_mssql':
				return "(DATEDIFF(day, $p_date2, $p_date1) ". $p_limitstring . ")";

			case 'mysql':
			case 'mysqli':
				return "(TO_DAYS($p_date1) - TO_DAYS($p_date2) ". $p_limitstring . ")";

			case 'postgres':
			case 'postgres64':
			case 'postgres7':
			case 'pgsql':
				return "(date_mi($p_date1::date, $p_date2::date) ". $p_limitstring . ")";

			case 'oci8':
				return "(($p_date1 - $p_date2)" . $p_limitstring . ")";

			case 'db2':
				// all DB2 UDB use days function
				return "(days($p_date1) - days($p_date2) " . $p_limitstring . ")";

			default:
				error_parameters( 'db_type', $t_db_type );
				trigger_error( ERROR_CONFIG_OPT_INVALID, ERROR );
		}
	}

	# --------------------
	# count queries
	function db_count_queries () {
		global $g_queries_array;

		return count( $g_queries_array );
	}

	# --------------------
	# count unique queries
	function db_count_unique_queries () {
		global $g_queries_array;

		$t_unique_queries = 0;
		$t_shown_queries = array();
		foreach ($g_queries_array as $t_val_array) {
			if ( ! in_array( $t_val_array[0], $t_shown_queries ) ) {
				$t_unique_queries++;
				array_push( $t_shown_queries, $t_val_array[0] );
			}
		}
		return $t_unique_queries;
	}

	# --------------------
	# get total time for queries
	function db_time_queries () {
		global $g_queries_array;
		$t_count = count( $g_queries_array );
		$t_total = 0;
		for ( $i = 0; $i < $t_count; $i++ ) {
			$t_total += $g_queries_array[$i][1];
		}
		return $t_total;
	}

	function db_get_table( $p_option ) {
		if ( isset( $GLOBALS['g_db_table'][$p_option] ) ) {
			$t_value = config_eval( $GLOBALS['g_db_table'][$p_option] );
			if ( $t_value !== $GLOBALS['g_db_table'][$p_option] ) {
    			$GLOBALS['g_db_table'][$p_option] = $t_value;
    		}
			return $t_value;
		} else {
			trigger_error( ERROR_CONFIG_OPT_NOT_FOUND, WARNING );
		}
	}
	
	function db_get_table_list() {
		$t_tables = Array();
		foreach( $GLOBALS['g_db_table'] as $t_table ) {
			$t_tables[] = config_eval( $t_table );
		}
		return $t_tables;
	}

	# --------------------

	if ( !isset( $g_skip_open_db ) ) {
		if ( OFF == $g_use_persistent_connections ) {
			db_connect( config_get_global( 'dsn', false ), $g_hostname, $g_db_username, $g_db_password, $g_database_name, config_get_global( 'db_schema' ) );
		} else {
			db_pconnect( config_get_global( 'dsn', false ), $g_hostname, $g_db_username, $g_db_password, $g_database_name, config_get_global( 'db_schema' ) );
		}
	} else {
		define( 'PLUGINS_DISABLED', true );	
	}
?>
