<?php

namespace Coincitymexico\DB;

use Exception;

class Select
{

    /**
     * @throws Exception
     */
    function construct($sql): array
    {
        if (isset($sql['custom'])) {
            $Statement = $sql['custom'];
        } else {
            $Statement = "select ";

            $Statement .= $sql["expressions"] ?? " * ";
            if (isset($sql["table"])) {
                $Statement .= " from " . $sql["table"];
            } else {
                $error["type"] = "Error";
                $error["query"] = $Statement;
                $error["no"] = "200";
                $error["result"] = "Query sin tabla, incluye TABLE a tu consulta";
                return $error;
            }
            if (isset($sql["join"])) {
                $Statement .= " " . $sql["join"] . " ";
            }

            if (isset($sql["where"])) {
                $Statement .= " where " . $sql["where"];
            }
            if (isset($sql["group"])) {
                $Statement .= " group by " . $sql["group"];
            }
            if (isset($sql["order"])) {
                $Statement .= " order by " . $sql["order"];
            }
            if (isset($sql["union"])) {
                $Statement .= " union " . $sql["union"];
            }
        }

        return self::getInfo($Statement);
    }

    /**
     * @param $db_Query
     * @return array
     * @throws Exception
     */
    function getInfo($db_Query): array
    {
        if (empty(Config::$db_connection->dbName)) {
            Config::connect();
        }
        $rs_Data = Config::$db_connection->query($db_Query);
//        if ($rs_Data) {
            $Affected_Rows = $rs_Data->size();

            if ($Affected_Rows > 0) {
                $view = [];
                for ($i = 0; $Table_Row = $rs_Data->fetch(); $i++) {
                    for ($a = 0; $a < $rs_Data->numFields(); $a++) {
                        $Table_Field = $rs_Data->RowName($a);
                        $view[$i][$Table_Field->name] = $Table_Row[$Table_Field->name];
                    }
                }

                $result["type"] = "success";
                $result["no"] = "100";
                $result["message"] = $view;


            } else {
                $result["no"] = "101";
                $result["type"] = "error";
                $result["message"] = "No Existen Datos Disponibles";

            }
            //$rs_Data->closeConnection();
//        } else {
//            $result["no"] = Config::$db_connection->noError();
//            $result["type"] = "error";
//            $result["message"] = Config::$db_connection->isError();
//        }
        $result["mysql"] = $db_Query;
        Config::$db_connection = null;
        return $result;
    }
}