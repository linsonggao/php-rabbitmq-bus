<?php
if(!function_exists("config")) {
    function config($value) {
        $config = include "./lib/rabbitmq.php";
        list($filename,$column) = explode('.',$value);
        return $config[$column];
    }
}