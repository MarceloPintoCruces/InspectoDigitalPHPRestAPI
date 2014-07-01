<?php


/*
V5.18 3 Sep 2012  (c) 2000-2012 John Lim (jlim#natsoft.com). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com). 
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.
*/

/*
	You may want to rename the 'data' field to 'session_data' as
	'data' appears to be a reserved word for one or more of the following:
		ANSI SQL
		IBM DB2
		MS SQL Server
		Postgres
		SAP

	If you do, then execute:

		ADODB_Session::dataFieldName('session_data');

*/

if (!defined('_ADODB_LAYER')) {
	require realpath(dirname(__FILE__) . '/../adodb.inc.php');
}

if (defined('ADODB_SESSION')) return 1;

define('ADODB_SESSION', dirname(__FILE__));


/* 
	Unserialize session data manually. See http://phplens.com/lens/lensforum/msgs.php?id=9821 
	
	From Kerr Schere, to unserialize session data stored via ADOdb. 
	1. Pull the session data from the db and loop through it. 
	2. Inside the loop, you will need to urldecode the data column. 
	3. After urldecode, run the serialized string through this function:

*/
function adodb_unserialize( $serialized_string ) 
{
	$variables = array( );
	$a = preg_split( "/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	for( $i = 0; $i < count( $a ); $i = $i+2 ) {
		$variables[$a[$i]] = unserialize( $a[$i+1] );
	}
	return( $variables );
}

/*
	Thanks Joe Li. See http://phplens.com/lens/lensforum/msgs.php?id=11487&x=1
	Since adodb 4.61.
*/
function adodb_session_regenerate_id() 
{
	$conn = ADODB_Session::_conn();
	if (!$conn) return false;

	$old_id = session_id();
	if (function_exists('session_regenerate_id')) {
		session_regenerate_id();
	} else {
		session_id(md5(uniqid(rand(), true)));
		$ck = session_get_cookie_params();
		setcookie(session_name(), session_id(), false, $ck['path'], $ck['domain'], $ck['secure']);
		//@session_start();
	}
	$new_id = session_id();
	$ok = $conn->Execute('UPDATE '. ADODB_Session::table(). ' SET sesskey='. $conn->qstr($new_id). ' WHERE sesskey='.$conn->qstr($old_id));
	
	/* it is possible that the update statement fails due to a collision */
	if (!$ok) {
		session_id($old_id);
		if (empty($ck)) $ck = session_get_cookie_params();
		setcookie(session_name(), session_id(), false, $ck['path'], $ck['domain'], $ck['secure']);
		return false;
	}
	
	return true;
}

/*
    Generate database table for session data
    @see http://phplens.com/lens/lensforum/msgs.php?id=12280
    @return 0 if failure, 1 if errors, 2 if successful.
	@author Markus Staab http://www.public-4u.de
*/
function adodb_session_create_table($schemaFile=null,$conn = null)
{
