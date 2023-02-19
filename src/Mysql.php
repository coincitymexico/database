<?php

namespace Coincitymexico\DB;

use Exception;
use mysqli;

class Mysql
{
    /**
     * MySQL server hostname
     * @access private
     * @var string
     */
    private string $host;

    /**
     * MySQL username
     * @access private
     * @var string
     */
    private string $dbUser;

    /**
     * MySQL user's password
     * @access private
     * @var string
     */
    private string $dbPass;

    /**
     * Name of database to use
     * @access private
     * @var string
     */
    public string $dbName;

    /**
     * MySQL Resource link identifier stored here
     * @access public
     * @var mysqli
     */
    public mysqli $dbConn;

    /**
     * Stores error messages for connection errors
     * @access private
     * @var string
     */
    public string $connectError;

    /**
     * MySQL constructor
     * @param string $host host (MySQL server hostname)
     * @param string $dbUser dbUser (MySQL User Name)
     * @param string $dbPass dbPass (MySQL User Password)
     * @param string $dbName dbName (Database to select)
     * @access public
     * @throws Exception
     */
    function __construct(string $host, string $dbUser, string $dbPass, string $dbName)
    {
        $this->host = $host;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;
        $this->connectToDb();
    }

    /**
     * Establishes connection to MySQL and selects a database
     * @return void
     * @access private
     * @throws Exception
     */
    function connectToDb(): void
    {
        // Make connection to MySQL server
        if (!$this->dbConn = @mysqli_connect($this->host, $this->dbUser, $this->dbPass, $this->dbName)) {

            $msj = 'No se puede conectar con el servidor. <br>
                <b style="font-size: 1.2rem;font-weight: normal;">Error: <i class="text-tw-red-500">' . mysqli_connect_error() . '</i></b>';
            $this->connectError = true;
            //trigger_error('Could not connect to server');
            throw new Exception($msj, 500);
        } else if (!@mysqli_select_db($this->dbConn, $this->dbName)) {
            trigger_error('Could not select database');
            $this->connectError = true;
            throw new Exception('Could not select database', 500);
        } else {
            mysqli_set_charset($this->dbConn, 'utf8');
        }


    }

    /**
     * Checks for MySQL errors
     * @return bool
     * @access public
     */

    /*Si existe algun error de conexion devuelve el mensaje*/
    function isError(): bool
    {
        return mysqli_error($this->dbConn);
    }

    function noError(): int
    {
        return mysqli_errno($this->dbConn);
    }

    /**
     * Returns an instance of MySQLResult to fetch rows with
     * @param $sql string the database query to run
     * @return MySQLResult
     * @access public
     * @throws Exception
     */
    function query(string $sql): MySQLResult
    {
        if ($queryResource = mysqli_query($this->dbConn, $sql)) {
            return new MySQLResult($this, $queryResource);
        }
        throw new Exception('Query failed: ' . mysqli_error($this->dbConn) . ' SQL: ' . $sql);
    }

    function close(): bool
    {
        return mysqli_close($this->dbConn);
    }

}
