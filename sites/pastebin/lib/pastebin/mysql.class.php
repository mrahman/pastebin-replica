<?php
/**
 * $Project: homework $
 * $Id: mysql.class.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 * 
 */

class MySQL
{
    var $dblink=null;
    var $dbresult;
    
    /**
    * Constructor - establishes DB connection
    */
    function MySQL()
    {
    }
    
    function _connect()
    {
        global $CONF;
        $this->dblink=mysql_pconnect(
            $CONF['dbhost'],
            $CONF['dbuser'],
            $CONF['dbpass'])
            or die('Unable to connect to database');
    
        mysql_select_db($CONF['dbname'], $this->dblink)
            or die('Unable to select database ' . $CONF['dbname']);
    }
    
    /**
    * execute query 
    */
    function query($sql)
    {
        global $CONF;
        
        if (is_null($this->dblink))
            $this->_connect();
        
        if (func_num_args() > 1)
        {
            $q = md5(uniqid(rand(), true));
            $sql = str_replace('?', $q, $sql);
            
            $args = func_get_args();
            for ($i=1; $i <= count($args); $i++)
            {
                $sql = preg_replace("/$q/", "'" . preg_quote(mysql_real_escape_string($args[$i])) . "'", $sql,1);
            }
        
            // For debugging 
            $sql=str_replace($q, '?', $sql);
        }
        
        $this->dbresult=mysql_query($sql, $this->dblink);
        if (!$this->dbresult)
        {
            die('Query failure: ' . mysql_error() . '<br />$sql');
        }
        
        return $this->dbresult;
    }
    
    /**
    * get next record after executing _query
    */
    function next_record()
    {
        $this->row=mysql_fetch_array($this->dbresult);
        return $this->row != FALSE;
    }

    /**
    * number of record in resultset
    */
    function num_rows()
    {
        return mysql_num_rows($this->dbresult);
    }
    
    /**
    * get result column $field
    */
    function f($field)
    {
        return $this->row[$field];
    }

    /**
    * get last insertion id
    */
    function get_insert_id()
    {
        return mysql_insert_id($this->dblink);
    }
    
    /**
    * get last error
    */
    function get_db_error()
    {
        return mysql_last_error();
    }
}
