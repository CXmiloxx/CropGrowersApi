<?php

    include './server/conexion.php';


    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo == 'GET') {
        try {
            if ($idProyecto) {
                $query = 'SELECT * FROM proyectos WHERE id = ?';
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idProyecto]);
                $datos = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($datos) {
                    $respuesta = formatearRespuesta(true, "Proyecto obtenido exitosamente", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún proyecto con el ID especificado.");
                }
            } else {
                $query = 'SELECT * FROM proyectos';
                $consulta = $conexion->prepare($query);
                $consulta->execute();
                $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

                if ($datos) {
                    $respuesta = formatearRespuesta(true, "Proyectos obtenido exitosamente", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún proyecto.");
                }
            }
        } catch (PDOException $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta a la base de datos: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "El método de la petición no es válido. Se esperaba GET");
    }

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");

    echo json_encode($respuesta);
?>
