<?php

namespace Coincitymexico\DB;

use mysqli_result;

class MysqlResult
{
    /**
     * Instance of MySQL providing database connection
     * @access private
     * @var Mysql
     */
    private Mysql $mysql;

    /**
     * Query resource
     * @access private
     * @var mysqli_result
     */
    private mysqli_result $query;

    /**
     * MySQLResult constructor
     * @param Mysql $mysql mysql   (instance of MySQL class)
     * @param mysqli_result $query query (MySQL query resource)
     * @access public
     */
    function __construct(Mysql &$mysql, mysqli_result $query)
    {
        $this->mysql =& $mysql;
        $this->query = $query;
    }

    /**
     * Fetches a row from the result
     * @return array|bool
     * @access public
     */
    function fetch(): bool|array
    {
        if ($row = mysqli_fetch_array($this->query, MYSQLI_ASSOC)) {
            return $row;
        } else if ($this->size() > 0) {
            mysqli_data_seek($this->query, 0);
            return false;
        } else {
            return false;
        }
    }

    /**
     * Returns the number of rows selected
     * @return int
     * @access public
     */
    function size(): int
    {
        return mysqli_num_rows($this->query);
    }

    /**
     * Returns the ID of the last row inserted
     * @return int
     * @access public
     */
    function insertID(): int
    {
        return mysqli_insert_id($this->mysql->dbConn);
    }

    /**
     * Checks for MySQL errors
     * @return boolean
     * @access public
     */
    function isError(): bool
    {
        return $this->mysql->isError();
    }

    /**
     * @return int
     */
    function numFields(): int
    {
        return mysqli_num_fields($this->query);
    }

    /**
     * @param $index
     * @return object|bool
     */
    function RowName($index): object|bool
    {
        if ($row = mysqli_fetch_field_direct($this->query, $index)) {
            return $row;
        } else {
            return false;
        }
    }

    /**
     *Close Database Connection
     * @return boolean
     * @access public
     */
    function closeConnection(): bool
    {
        mysqli_free_result($this->query);
        return true;
    }


}