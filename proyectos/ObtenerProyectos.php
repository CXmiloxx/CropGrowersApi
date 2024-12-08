<?php
include './server/conexion.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

configurarHeaders();

if ($metodo === 'GET') {
    try {
        if ($idUsuario) {
            $query = 'SELECT p.*, u.nombre, u.apellido
                        FROM proyectos p
                        INNER JOIN usuarios u ON p.idInstructor = u.id 
                        WHERE p.idInstructor = ?';
            $consulta = $conexion->prepare($query);
            $consulta->execute([$idUsuario]);
            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($datos) {
                $respuesta = formatearRespuesta(true, "Proyectos obtenidos correctamente", $datos);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron proyectos para el ID proporcionado.");
            }
        } else {
            $query = 'SELECT p.*, u.nombre, u.apellido 
                        FROM proyectos p
                        INNER JOIN usuarios u ON p.idInstructor = u.id';
            $consulta = $conexion->prepare($query);
            $consulta->execute();
            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($datos) {
                $respuesta = formatearRespuesta(true, "Proyectos obtenidos correctamente", $datos);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron proyectos.");
            }
        }
    } catch (PDOException $e) {
        $respuesta = formatearRespuesta(false, "Error en la consulta a la base de datos: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método HTTP no permitido. Se esperaba GET.");
}

echo json_encode($respuesta);
