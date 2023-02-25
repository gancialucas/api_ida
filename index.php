<?php
    require "conexionBD.php";
    include "utilidades.php";

    $db_connection =  connect($db);

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Content-Type: application/json; charset=utf-8");

    /* LISTAR TODOS LOS usuarios O SOLO UNO */
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['email'])) {
            // Mostrar un post
            $sql = $db_connection->prepare("SELECT * FROM usuarios where email=:email");
            $sql->bindValue(':email', $_GET['email']);
            $sql->execute();

            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
            exit();

        } else {
            // Mostrar lista de post
            $sql = $db_connection->prepare("SELECT * FROM usuarios");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);

            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll());
            exit();
        }
    }

    // CREAR UN NUEVO POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $sql = "INSERT INTO usuarios
            (nombre, apellido, email, pass, rol)
            VALUES
            (:nombre, :apellido, :email, :pass, :rol)";
        $statement = $db_connection->prepare($sql);
        bindAllValues($statement, $input);
        $statement->execute();
        
        $postId = $db_connection->lastInsertId();
        if ($postId) {
            $input['id'] = $postId;
            header("HTTP/1.1 201 CREATED");
            echo json_encode($input);
            exit();
        }
    }

    // BORRAR
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $id = $_GET['id'];
        $statement = $db_connection->prepare("DELETE FROM usuarios WHERE id=:id");
        $statement->bindValue(':id', $id);
        $statement->execute();
        header("HTTP/1.1 200 OK");
        exit();
    }

    // ACTUALIZAR
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        $postId = $input['id'];
        $fields = getParams($input);

        $sql = "
            UPDATE usuarios
            SET $fields
            WHERE id='$postId'
            ";

        $statement = $db_connection->prepare($sql);
        bindAllValues($statement, $input);

        $statement->execute();
        header("HTTP/1.1 200 OK");
        exit();
    }


    /* EN CASO DE QUE NINGUNA DE LAS OPCIONES ANTERIORES SE HAYA EJECUTADO */
    header("HTTP/1.1 400 Bad Request");