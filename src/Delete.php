<?php

namespace Coincitymexico\DB;

use Exception;

class Delete
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
            return self::deleteInfo($Statement);
        }else {
            $Statement = "delete from ";
            if (isset($sql["table"])) {
                $Statement .= $sql["table"];
            } else {
                $result["no"] = "95";
                $result["type"] = "error";
                $result["message"] = "No se ha definido una tabla";
                return $result;
            }

            if (isset($sql["where"])) {
                $Statement .= " where " . $sql["where"];
                $exp = explode("'", $sql["where"]);
                $this->id = $exp[1];
                return self::deleteInfo($Statement);

            } else {
                $result["no"] = "108";
                $result["type"] = "error";
                $result["mysql"] = $Statement;
                $result["message"] = "Error Critico! Estas intentando eliminar sin Where, @#%$$%!!!!";

                return $result;
            }
        }
    }


    /**
     * @param $db_Update
     * @return array
     * @throws Exception
     */
    function deleteInfo($db_Update): array
    {
        if (empty(Config::$db_connection)) {
            Config::connect();
        }

        $rs_Update = Config::$db_connection->query($db_Update);

//        if ($rs_Update) {
        $File_Id = $rs_Update->insertID();
        if (!$File_Id) {
            $result["no"] = "95";
            $result["type"] = "success";
            $result["message"] = "Registro Eliminado Correctamente";
            //$result["query"]    =$db_Update;
        } else {
            $result["no"] = "108";
            $result["type"] = "error";
            $result["message"] = "No se ha eliminado, vuelve a intentar";
            //$result["query"]    =$db_Update;
        }

//        }
//        else {
//            $result["no"] = Config::$db_connection->noError();
//            $result["type"] = "error";
//            $result["message"] = Config::$db_connection->isError();
//            //$result["mysql"]     =$db_Update;
//            //$result["query"]    =$db_Update;
//        }
        $result["id"] = $this->id;
        //Config::$db_connection->close();
        Config::$db_connection = null;
        return $result;
    }
}