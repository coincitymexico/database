<?php

namespace Coincitymexico\DB;
use Exception;

class Call
{
    /**
     * @param $sql
     * @return array
     * @throws Exception
     */
    public function construct($sql): array
    {
        $Statement = "call ";
        if (isset($sql["sp"])) {
            $Statement .= $sql["sp"] . "(";
        }
        if (isset($sql["values"])) {
            $values = explode(",", $sql["values"]);
            for ($i = 0; $i < count($values); $i++) {
                if ($i < count($values) - 1) {
                    $Statement .= "'" . $values[$i] . "', ";
                } else {
                    $Statement .= "'" . $values[$i] . "'";
                }
            }
        }


        if (isset($sql["output"])) {
            $Statement .= ");";
            return self::getCallNoOut($Statement);
        } else {

            if (isset($sql["values"])) {
                $Statement .= ",@output);";
            } else {
                $Statement .= "@output);";
            }

            return self::getCall($Statement);
        }


    }

    /**
     * @param $db_Call
     * @return array
     * @throws Exception
     */
    public function getCall($db_Call): array
    {
        if (empty(Config::$db_connection)) {
            Config::connect();
        }
        $call_return = "select @output;";
        $rs_Data_Call = Config::$db_connection->query($call_return);

        return $this->getConnection($rs_Data_Call, Config::$db_connection);
    }

    /**
     * @param $db_Call
     * @return array
     * @throws Exception
     */
    public function getCallNoOut($db_Call): array
    {
        if (empty(Config::$db_connection)) {
            Config::connect();
        }
        $rs_Data = Config::$db_connection->query($db_Call);

        return $this->getConnection($rs_Data, Config::$db_connection);
    }

    /**
     * @param $rs_Data
     * @param $db_Connection
     * @return array
     */
    public function getConnection($rs_Data, $db_Connection): array
    {
        if ($rs_Data) {
            $Affected_Rows = $rs_Data->size();

            if ($Affected_Rows > 0) {
                $view = [];
                for ($i = 0; $Table_Row = $rs_Data->fetch(); $i++) {
                    for ($a = 0; $a < $rs_Data->numFields(); $a++) {
                        $Table_Field = $rs_Data->RowName($a);
                        $view[$i][$Table_Field->name] = $Table_Row[$Table_Field->name];
                    }
                }

                $result["no"] = "110";
                $result["type"] = "success";
                $result["message"] = $view;
                //$result["mysql"]     =$db_Call;
                //var_dump($result);
                //$rs_Data->closeConnection();
            } else {
                $result["no"] = "111";
                $result["type"] = "error";
                $result["message"] = "No Existen Datos Disponibles";
                //$result["mysql"]     =$db_Call;

            }
        } else {
            $result["no"] = $db_Connection->noError();
            $result["type"] = "error";
            $result["message"] = $db_Connection->isError();
            //$result["mysql"]     =$db_Call;

        }
        unset($db_Connection);
        return $result;
    }
}