<?php

use Coincitymexico\DB\Config;
use Spatie\Ignition\Ignition;

include_once "../vendor/autoload.php";
Ignition::make()->register();
Config::$db_name = 'test';
Config::$db_user = 'root';
Config::$db_pass = '';
Config::connect();

//$insert = new \Coincitymexico\DB\Insert();
//$name = db_scape("'danidoble\"");
//$r = $insert->construct(['custom'=>"insert into users (name) values ('{$name}')"]);
// dd($r);