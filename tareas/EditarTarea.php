<?php

    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idTarea = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'PUT') {
        try {
            $contenido = trim(file_get_contents('php://input'));
            $datos = json_decode($contenido, true);
            if ($idTarea) {
                if (isset($datos['nombre'], $datos['descripcion'],$datos['fechaLimite'])) {
                    $nombre = $datos['nombre'];
                    $descripcion = $datos['descripcion'];
                    $fechaLimite = $datos['fechaLimite'];
                    $query = "UPDATE tareas SET nombre = :nom, descripcion = :des, fechaLimite = :fecL WHERE id = :id";
                    $consulta = $conexion->prepare($query);
                    $consulta->bindParam(':nom', $nombre);
                    $consulta->bindParam(':des', $descripcion);
                    $consulta->bindParam(':fecL', $fechaLimite);
                    $consulta->bindParam(':id', $idTarea);
                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, "Tarea actualizada exitosamente.");
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar la tarea. Verifica los datos y vuelve a intentarlo.");
                    }
                }else{
                    $respuesta = formatearRespuesta(false, "Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.");
                }
            }
        }catch(Exception $e) {
            $respuesta = formatearRespuesta(false, "Error de base de datos: ". $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba PUT.");
    }
    
    echo json_encode($respuesta);

?>