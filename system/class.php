<?php

class DB {

    public function __construct(){
        define('MYSQL_CONNECTION',mysql_connect(MYSQL_SERVER,MYSQL_USER,MYSQL_PASSWORD)); // Подключение к БД
        mysql_select_db('tm'); // Выбор БД
    }

    protected function select($table_name, $fields, $where = "", $order = "", $up = true, $limit = ""){
        for ($i = 0; $i < count ($fields); $i++){
            if ((strpos($fields[$i], "(") === false) && ($fields[$i] != "*")) $fields[$i] = "`".$fields[$i]."`";
        }
        $fields = implode(",", $fields);
        if (!$order) $order = "";
        else {
            if ($order != "RAND()"){
                $order = "ORDER BY `$order`";
                if (!$up) $order .= " DESC";
            }
            else $order = "ORDER BY $order";
        }
        if ($limit) $limit = "LIMIT $limit";
        if ($where) $query = "SELECT $fields FROM $table_name WHERE $where $order $limit";
        else $query = "SELECT $fields FROM $table_name $order $limit";
        $res = mysql_query($query) or die(mysql_error());
        return $res;
    }

    protected function update ($table_name, $upd_fields, $where){
        $query = "UPDATE $table_name SET ";
        foreach ($upd_fields as $field => $value) $query .= "`$field` = '".addslashes($value)."',";
        $query = substr($query, 0, -1);
        if ($where){
            $query .= " WHERE $where";
            $res = mysql_query($query) or die(mysql_error());
            return $res;
        }
        else return false;
    }

    protected function insert ($table_name, $new_value){
        $table_name = $this->config->db_prefix.$table_name;
        $query = "INSERT INTO $table_name (";
        foreach ($new_value as $field => $value) $query .= "`".$field."`,";
        $query = substr($query, 0, -1);
        $query .= ") VALUES (";
        foreach ($new_value as $value) $query .= "'".addslashes($value)."',";
        $query = substr($query, 0, -1);
        $query .= ")";
        $res = mysql_query($query) or die(mysql_error());
        return $res;
    }



    protected function delete ($table_name, $where = ""){
        if ($where){
            $query = "DELETE FROM $table_name WHERE $where";
            $res = mysql_query($query) or die(mysql_error());
            return $res;
        }
        else return false;
    }
}

class Model extends DB {
    public function __construct(){
        $this->db = new DB(); // Вызов БД
        //$this->selectVisProjectUser();
        //$this->deleteVisProjectUser();
        //$this->selectVisProjectUser();
    }

    public function pre_arr($array){
        echo "<pre>";
        print_r($array);
        echo "</pre>";

    }

    public function selectVisProjectUser(){
        $table_name = "visprojectuser";
        $res  = $this->select($table_name,array("iduser","idproject"));
        while($row = mysql_fetch_assoc($res)){
            $this->pre_arr($row);
        }
    }

    public function updateVisProjectUser(){
        $table_name = "visprojectuser";
        $where = "idproject = 2";
        $res  = $this->update($table_name,array("iduser" => 12,"idproject"=>2),$where);
    }

    public function insertVisProjectUser(){
        $table_name = "visprojectuser";
        $res  = $this->insert($table_name,array("iduser" => 13,"idproject"=>3));
    }

    public function deleteVisProjectUser(){
        $table_name = "visprojectuser";
        $where = "iduser=12";
        $res  = $this->delete($table_name,$where);
    }
}

function reg_error($errno, $errstr, $errfile, $errline){ // Обработчик ошибок для вывода их в системный лог
    $LOG='[ERROR LOG START HERE]';
    switch ($errno) {
        case E_USER_ERROR:
            $LOG.= "<b>My ERROR</b> [$errno] $errstr<br />\n";
            $LOG.= "  Фатальная ошибка в строке $errline файла $errfile";
            $LOG.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $LOG.= "Завершение работы...<br />\n";
            exit(1);
            break;

        case E_USER_WARNING:
            $LOG.= "<b>My WARNING</b> [$errno] $errstr<br />\n";
            break;

        case E_USER_NOTICE:
            $LOG.= "<b>My NOTICE</b> [$errno] $errstr<br />\n";
            break;

        default:
            $LOG.= "Неизвестная ошибка: [$errno] $errstr<br />\n";
            break;
    }
    $LOG.='[ERROR LOG END HERE]';
    return true;
}