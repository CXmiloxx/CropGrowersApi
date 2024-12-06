<?php
    include './server/conexion.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        try {
            $contenido = trim(file_get_contents('php://input'));
            $datos = json_decode($contenido, true);

            if (isset($datos['idProyecto'], $datos['nombre'], $datos['descripcion'], $datos['fechaLimite'])) {
                $idProyecto = $datos['idProyecto'];
                $nombre = $datos['nombre'];
                $descripcion = $datos['descripcion'];
                $fechaLimite = $datos['fechaLimite'];

                $query = "INSERT INTO tareas (idProyecto, nombre, descripcion, fechaLimite) VALUES (:idI, :nom, :des, :fecL)";
                $consulta = $conexion->prepare($query);
                $consulta->bindParam(':idI', $idProyecto);
                $consulta->bindParam(':nom', $nombre);
                $consulta->bindParam(':des', $descripcion);
                $consulta->bindParam(':fecL', $fechaLimite);

                if ($consulta->execute()) {
                    $respuesta = formatearRespuesta(true, 'Tarea creada correctamente.');
                } else {
                    $respuesta = formatearRespuesta(false, 'No se pudo crear la tarea. Verifica los datos y vuelve a intentarlo.');
                }
            } else {
                $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, 'Ocurrió un error inesperado. Detalles del error: ' . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }

    echo json_encode($respuesta);
?>
