<?php

namespace Coincitymexico\DB;

use Exception;

class Config
{
    /**
     * @var Mysql|null
     */
    public static ?Mysql $db_connection;

    /**
     * @var string
     */
    public static string $db_name = 'test', $db_host = '127.0.0.1', $db_user = 'root', $db_pass = '';

    /**
     * @throws Exception
     */
    public static function connect(): void
    {
        if (!empty(self::$db_connection)) {
            self::$db_connection->close();
        }
        Config::$db_connection = new Mysql(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);
    }

    /**
     * @throws Exception
     */
    public static function connectCustom(string $db_host, string $db_user, string $db_pass, string $db_name): void
    {
        if (!empty(self::$db_connection)) {
            self::$db_connection->close();
        }
        Config::$db_connection = new Mysql($db_host, $db_user, $db_pass, $db_name);
    }
}