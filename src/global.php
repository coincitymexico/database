<?php

use Coincitymexico\DB\Config;

if (!function_exists('db_scape')) {
    /**
     * @param string $str
     * @param bool $bypass
     * @return string
     * @throws Exception
     */
    function db_scape(string $str, bool$bypass = false): string
    {
        if ($bypass) {
            return $str;
        }
        if (empty(Config::$db_connection)) {
            Config::connect();
        }
        $str = stripslashes($str);
        return Config::$db_connection->dbConn->real_escape_string($str);
    }
}
if (!function_exists('db_scape_html')) {
    /**
     * @param string $str
     * @param bool $bypass
     * @return string
     * @throws Exception
     */
    function db_scape_html(string $str,bool $bypass = false): string
    {
        if ($bypass) {
            return $str;
        }
        if (empty(Config::$db_connection)) {
            Config::connect();
        }
        $str = stripslashes($str);
        $str = htmlentities($str);
        $str = strip_tags($str);
        return Config::$db_connection->dbConn->real_escape_string($str);
    }
}