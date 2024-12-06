<?php

    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idTarea = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'POST') {
        try {
            $contenido = trim(file_get_contents('php://input'));
            $datos = json_decode($contenido, true);
            
            if (isset($datos['idUsuario'], $datos['estado'], $datos['comentarios']) ) {
                $idUsuario = $datos['idUsuario'];
                $estado = $datos['estado'];
                $comentarios = $datos['comentarios'];
            
                // mirar si existe un estado para esta tarea y usuario
                $query = "SELECT id FROM estadoTareas WHERE idTarea = :idTarea AND idUsuario = :idUsuario";
                $consulta = $conexion->prepare($query);
                $consulta->bindParam(':idTarea', $idTarea);
                $consulta->bindParam(':idUsuario', $idUsuario);
                $consulta->execute();
            
                if ($consulta->rowCount() > 0) {
                    // Si existe se actualiza el estado
                    $updateQuery = "UPDATE estadoTareas SET estado = :estado, comentarios = :com WHERE idTarea = :idTarea AND idUsuario = :idUsuario";
                    $updateConsulta = $conexion->prepare($updateQuery);
                    $updateConsulta->bindParam(':com', $datos['comentarios']);
                    $updateConsulta->bindParam(':estado', $estado);
                    $updateConsulta->bindParam(':idTarea', $idTarea);
                    $updateConsulta->bindParam(':idUsuario', $idUsuario);

            
                    if ($updateConsulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Estado actualizado correctamente.');
                    } else {
                        $respuesta = formatearRespuesta(false, 'No se pudo actualizar el estado.');
                    }
                } else {
                    // Si no existe se ineserta un nuevo estado
                    $insertQuery = "INSERT INTO estadoTareas (idTarea, idUsuario, estado, comentarios) VALUES (:idTarea, :idUsuario, :estado, :com)";
                    $insertConsulta = $conexion->prepare($insertQuery);
                    $insertConsulta->bindParam(':idTarea', $idTarea);
                    $insertConsulta->bindParam(':idUsuario', $idUsuario);
                    $insertConsulta->bindParam(':estado', $estado);
                    $insertConsulta->bindParam(':com', $comentarios);
            
                    if ($insertConsulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Estado creado correctamente.');
                    } else {
                        $respuesta = formatearRespuesta(false, 'No se pudo crear el estado.');
                    }
                }
            } else {
                $respuesta = formatearRespuesta(false, 'Faltan datos necesarios.');
            }
            
        }catch(Exception $e){
            $respuesta = formatearRespuesta(false, 'Error de base de datos: '. $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }
    
    echo json_encode($respuesta);
?>