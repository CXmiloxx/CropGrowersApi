<?php
include './server/conexion.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);

$idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

if ($metodo === 'GET') {
    try {
        if ($idProyecto) {
            $query = "SELECT *,
                        (SELECT COUNT(*) FROM tareas WHERE idProyecto = :idProyecto) AS cantidadTareas 
                        FROM tareas
                        WHERE idProyecto = :idProyecto";
                        
            $consulta = $conexion->prepare($query);
            $consulta->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
            $consulta->execute();

            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($datos) {
                $cantidadTareas = $datos[0]['cantidadTareas'];
                $respuesta = formatearRespuesta(true, "Tareas obtenidas exitosamente", [
                    "tareas" => $datos,
                    "cantidadTareas" => $cantidadTareas
                ]);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron tareas para el ID de proyecto especificado.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debe especificar un ID de proyecto válido.");
        }
    } catch (PDOException $e) {
        $respuesta = formatearRespuesta(false, "Error al conectar con la base de datos: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba GET.");
}

echo json_encode($respuesta);
