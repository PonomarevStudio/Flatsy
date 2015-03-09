<?php

define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/'); // Установка корневой директории
define('AJAX','CORE');
require_once ROOT.'system/core.php'; // Инициация запуска системы
$QUERY=json_decode($_GET['query']); // Запрос
$QUERY=objectToArray($QUERY);
$RESPONSE=array();

if(defined('USER_ID')) { // Статус авторизации
    $RESPONSE['auth']=true;
    if(isset($QUERY['action'])) {
        if ($QUERY['action'] == 'logout') {
            $out = User::logout();
            if ($out == true) {
                $RESPONSE['auth'] = false;
            }
        }
    }
}else {
    $RESPONSE['auth'] = false;
    if ($QUERY['action'] == 'reg') {
        $reg = User::registration($QUERY);
        $RESPONSE['reg'] = $reg;
    }
    if ($QUERY['action'] == 'auth') {
        $auth = User::auth($QUERY['email'], $QUERY['pass']);
        $RESPONSE['auth'] = $auth;
    }

}

echo(json_encode($RESPONSE)); // Ответ