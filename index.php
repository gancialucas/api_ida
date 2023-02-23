<?php
    require "conexionBD.php";
    include "utilidades.php";

    $db_connection =  connect($db);

    /* LISTAR TODOS LOS usuarios O SOLO UNO */
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['id'])) {
            // Mostrar un post
            $sql = $db_connection->prepare("SELECT * FROM usuarios where id=:id");
            $sql->bindValue(':id', $_GET['id']);
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
        $input = $_POST;
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
        $statement = $db_connection->prepare("DELETE FROM usuarios where id=:id");
        $statement->bindValue(':id', $id);
        $statement->execute();
        header("HTTP/1.1 200 OK");
        exit();
    }

    // ACTUALIZAR
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $input = $_GET;
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