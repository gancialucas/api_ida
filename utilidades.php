<?php
    // ABRIR CONEXIÓN A LA BASE DE DATOS
    function connect($db) {
        try {
            
            $connection = new PDO("mysql:host={$db['host']};dbname={$db['db']}", $db['username'], $db['password']);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;

        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }


    // OBTENER PARAMETROS PARA UPDATES (PUT)
    function getParams($input) {
        $filterParams = [];

        foreach ($input as $param => $value) {
            $filterParams[] = "$param=:$param";
        }

        return implode(", ", $filterParams);
    }

    // ASOCIAR TODOS LOS PARAMETROS A UN SQL
    function bindAllValues($statement, $params) {
        foreach ($params as $param => $value) {
            $statement->bindValue(':' . $param, $value);
        }
        
        return $statement;
    }