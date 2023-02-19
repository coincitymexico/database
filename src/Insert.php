<?php

namespace Coincitymexico\DB;

use Exception;

class Insert
{
    /**
     * @throws Exception
     */
    function construct($sql)
    {
        $Statement = "insert into ";
        $values = [];

        if (isset($sql["table"])) {
            $Statement .= $sql["table"];
        }

        if (isset($sql["fields"])) {
            $Statement .= "(" . $sql["fields"] . ") ";
        }

        if (isset($sql["package"]) && $sql["package"]) {
            if (isset($sql["values"])) {
                $Statement .= " values";
                for ($i = 0; $i < count($sql["values"]); $i++) {
                    if ($i < count($sql["values"]) - 1) {
                        $Statement .= "(" . $sql["values"][$i] . "),";
                    } else {
                        $Statement .= "(" . $sql["values"][$i] . ");";
                    }

                }
            }
        } else {
            if (isset($sql["values"])) {
                $Statement .= " values(";
                $values = explode(",", $sql["values"]);

                for ($i = 0; $i < count($values); $i++) {

                    if (isset($sql["nullable"])) {
                        if ($i < count($values) - 1) {
                            $Statement .= $values[$i] . ",";
                        } else {
                            $Statement .= $values[$i] . ");";
                        }
                    } else {
                        if ($i < count($values) - 1) {
                            $Statement .= "'" . $values[$i] . "',";
                        } else {
                            $Statement .= "'" . $values[$i] . "');";
                        }
                    }
                }
            }
        }
        if (isset($sql['custom'])) {
            $Statement = $sql['custom'];
        }
        $result = $this->insertInfo($Statement);


        if ($result["no"] == "1062" && isset($sql["update"])) {

            if (preg_match("/Duplicate entry '(.*)' for key '(.*)'/Ui", $result["message"], $matches)) {
                $fields = explode(",", $sql["fields"]);
                for ($i = 0; $i < count($values); $i++) {
                    //echo $matches[1];
                    if ($matches[1] == $values[$i]) {
                        $sql["where"] = $fields[$i] . "='" . $values[$i] . "';";
                        break;
                    }
                }

                if (empty($sql['where'])) { // si no esta la llave en lo actualizado
                    $sql['where'] = $matches[2] . "='" . $matches[1] . "'";
                }
            } else {
                echo 'Could not find violated key name';
            }
            $result = (new Update())->construct($sql);

        }

        return $result ?? null;
    }

    /**
     * @param $db_Insert
     * @return array
     * @throws Exception
     */
    function insertInfo($db_Insert): array
    {
        if (empty(Config::$db_connection)) {
            Config::connect();
        }


        $rs_Insert = Config::$db_connection->query($db_Insert);

        //echo Config::$db_connection->insert_id;
//        if ($rs_Insert) {
            $File_Id = $rs_Insert->insertID();


            if ($File_Id > 0) {
                $result["no"] = "95";
                $result["type"] = "success";
                $result["message"] = "Datos Insertados Correctamente";
                //$result["query"]     =$db_Insert;
                $result["id"] = $File_Id;
            } else {
                $result["no"] = "103";
                $result["type"] = "error";
                $result["message"] = "Error al insertar";

                //$result["query"]     =$db_Insert;
            }


//        } else {
//
//            $result["no"] = Config::$db_connection->noError();
//            $result["type"] = "mysql";
//            //$result["query"]     =$db_Insert;
//            $result["message"] = Config::$db_connection->isError();
//
//        }

        Config::$db_connection = null;
        return $result;
    }

}