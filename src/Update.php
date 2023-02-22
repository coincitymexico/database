<?php

namespace Coincitymexico\DB;

use Exception;

class Update
{
    /**
     * Mysql user's password
     * @access private
     * @var string
     */
    private string $id = '0';

    /**
     * @throws Exception
     */
    function construct($sql): array
    {
        if (isset($sql['custom'])) {
            $Statement = $sql['custom'];
            return $this->updateInfo($Statement);
        }
        $Statement = "update ";

        if (isset($sql["table"])) {
            $Statement .= $sql["table"] . " set ";
        }
        if (isset($sql["fields"])) {
            $fields = explode(",", $sql["fields"]);
        } else {
            $result["no"] = "95";
            $result["type"] = "error";
            $result["message"] = "No hay nada que actualizar, revisa el arreglo fields";
            return $result;
        }
        if (isset($sql["values"])) {
            $values = explode(",", $sql["values"]);
            for ($i = 0; $i < count($values); $i++) {
                if ($i < count($values) - 1) {
                    if (isset($sql["nullable"])) {
                        if ($values[$i] == 'null') {
                            $Statement .= $fields[$i] . "=" . $values[$i] . ", ";
                        } else {
                            $Statement .= $fields[$i] . "='" . $values[$i] . "', ";
                        }
                    } else {
                        $Statement .= $fields[$i] . "='" . $values[$i] . "', ";
                    }
                } else {
                    if (isset($sql["nullable"])) {
                        if ($values[$i] == 'null') {
                            $Statement .= $fields[$i] . "=" . $values[$i] . " ";
                        } else {
                            $Statement .= $fields[$i] . "='" . $values[$i] . "' ";
                        }
                    } else {
                        $Statement .= $fields[$i] . "='" . $values[$i] . "' ";
                    }
                }

            }
        }
        if (isset($sql["where"])) {
            $Statement .= " where " . $sql["where"];
            $exp = explode("'", $sql["where"]);
            if (isset($exp[1])) {
                $this->id = $exp[1];
            } else {
                $this->id = 0;
            }
            //var_dump($Statement);die();
            return $this->updateInfo($Statement);

        }
        $result["no"] = "104";
        $result["type"] = "error";
        $result["mysql"] = $Statement;
        $result["message"] = "Error Critico! Estas intentando actualizar sin Where, pendejo!!!!";

        return $result;
    }

    /**
     * @param $db_Update
     * @return array
     * @throws Exception
     */
    function updateInfo($db_Update): array
    {
        if (empty(Config::$db_connection->dbName)) {
            Config::connect();
        }

        $rs_Update = Config::$db_connection->query($db_Update);

//        if ($rs_Update) {
        $File_Id = $rs_Update->insertID();
        if (!$File_Id) {
            $result["no"] = "95";
            $result["type"] = "success";
            $result["message"] = "Registro Actualizado Correctamente";
            //$result["query"]    =$db_Update;
        } else {
            $result["no"] = "106";
            $result["type"] = "error";
            $result["message"] = "No se ha actualizado, vuelve a intentar";
            $result["query"] = $db_Update;
        }

//        } else {
//            $result["no"] = Config::$db_connection->noError();
//            $result["type"] = "error";
//            $result["message"] = Config::$db_connection->isError();
//            $result["mysql"] = $db_Update;
//            //$result["query"]    =$db_Update;
//        }
        $result["id"] = $this->id;
        //Config::$db_connection->close();
        Config::$db_connection = null;
        return $result;
    }
}