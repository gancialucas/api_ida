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

    // METODO CORS - ACCESO DATOS
    function cors() {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD, etc.
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }
        
        echo "You have CORS!";
    }