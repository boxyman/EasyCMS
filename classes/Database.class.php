<?php
/**
 * Easy CMS
 *
 * @package    Database
 * @author     SimpleShop.dk
 * @copyright  (c) 2010 SimpleShop.dk
 * @license    To use, copy, modify, and/or distribute this
 *             software for any purpose with or without fee is is not
 *             allowed without written consent from the author.
 */
class Database
{
    private $config;
	private $connection;
	
    function __construct($config)
    {
        $this->host = $config['dbhost'];
		$this->user = $config['dbuser'];
		$this->name = $config['dbname'];
		$this->password = $config['dbpassword'];
        
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->name);
        if (mysqli_connect_errno())
        {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        
        $this->connection->query("SET NAMES utf8");
        $this->connection->query("SET character_set_results='utf8'");
    }
    
    // Query
    public function query($sql)
    {
		$sql = $this->escape($sql);
        return $this->connection->query($sql);
    }

    // Multi query
    public function multi_query($sql)
    {
		$sql = $this->escape($sql);
        return $this->connection->multi_query($sql);
    }
	
    // Fetch associative array
    public function fetch_assoc()
    {
        return $this->connection->fetch_assoc();
    }

    // Fetch array
    public function fetch_array()
    {
        return $this->connection->fetch_array();
    }

    // Number of rows
    public function num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    // Escape SQL string
    public function escape($string)
    {
        // return $this->connection->real_escape_string($string);
        // return mysqli_real_escape_string($this->connection, $string);
        return $string;
    }
}