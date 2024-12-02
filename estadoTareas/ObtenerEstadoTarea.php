<?php
include './server/conexion.php';
configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idTarea = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;


if ($metodo == 'GET') {
    try {
        if ($idTarea) {
            $query = "SELECT * FROM estadoTareas WHERE idTarea = ?";
            $consulta = $conexion->prepare($query);
            $consulta->execute([$idTarea]);
            $tarea = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($tarea) {
                $respuesta = formatearRespuesta(true, "Tarea obtenida exitosamente.", $tarea);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontró ninguna tarea con el ID especificado.");
            }
        } else {
            $query = "SELECT * FROM estadoTareas";
            $consulta = $conexion->prepare($query);
            $consulta->execute();
            $tareas = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($tareas) {
                $respuesta = formatearRespuesta(true, "Tareas obtenidas exitosamente.", $tareas);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron tareas.");
            }
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta a la base de datos: " . $e->getMessage());
    }
    echo json_encode($respuesta);
}
