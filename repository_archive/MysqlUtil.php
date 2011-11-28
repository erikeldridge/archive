<?php /* A little wrapper for php's mysql handlers

Usage:
1) Drop this code on a page
2) Create a table as you like
3) Initialize an object like this: $db = new MysqlUtil( $db_host, $db_name, $db_user, $db_pass );
4) Run queries like this: $db->query( "select * from `my_table` where `field_name`='value';" ); 
5) Escape input using sprintf notation & logic like this:
$db->query( "insert into `my_table` ( `field1` ) values ( '%s' )", "my ' value" );

Example: see this in action http://github.com/erikeldridge/bbauth-to-oauth-example
License: Yahoo! BSD http://gist.github.com/375593
Source: http://gist.github.com/raw/387056/c99beea743b2ac306ea309d80f391e8cdaa9ef28/MysqlUtil.php
*/

class MysqlUtil {
    function __construct( $host, $db_name, $user_name, $password ){
        
        $this->db_name = $db_name;
        
        $this->db = mysql_connect( $host, $user_name, $password );
        if (!$this->db) {
            throw( new Exception( 'db host connection error'. mysql_error() ) );
        }
        if ( !mysql_select_db( $this->db_name ) ) {
            throw( new Exception( 'db selection error'. mysql_error() ) );
        }
        return $this;
    }
    
    function __destruct(){
        mysql_close($this->db);
    }
    
    function query( /* query, val1, val2, ... */ ){
        
        $args = func_get_args();
        $query = array_shift( $args );
        
        // if args left over, escape them and insert them into query using sprintf rules
        if ( $args ) {
            $escaped = array_map ( 'mysql_real_escape_string', $args );
            $query = vsprintf( $query, $escaped );
        }
        
        $result = mysql_query( $query );
        
        if ( mysql_error() ){
            throw( new Exception( 'db query error: '.mysql_error() ) );
        }
        
        // mysql_query returns true for success w/ some queries 
        if ( true === $result ) {
           return true;
        }
        
        // mysql_query returns resource for other types of queries         
        $rows = array();
        while ( $row = mysql_fetch_assoc( $result ) ) {
            $rows[] = $row;
        }
        return $rows;
    }
}
?>