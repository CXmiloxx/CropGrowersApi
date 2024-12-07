<?php
include './server/conexion.php';

configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$idRespuesta = isset($segmentos_uri[4]) && is_numeric($segmentos_uri[4]) ? $segmentos_uri[4] : null;

if ($metodo == 'DELETE') {
    try {
        if ($idUsuario && $idRespuesta) {
            $query = "DELETE FROM respuestas WHERE id = ? AND idUsuario = ?";
            $consulta = $conexion->prepare($query);
            $consulta->execute([$idRespuesta, $idUsuario]);

            if ($consulta->rowCount() > 0) {
                $respuesta = formatearRespuesta(true, "Respuesta eliminada exitosamente.");
            } else {
                $respuesta = formatearRespuesta(false, "No se pudo eliminar la respuesta.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Debes de especificar un ID de respuesta.");
        }
    } catch (PDOException $e) {
        $respuesta = formatearRespuesta(false, "Error de base de datos: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba DELETE.");
}

echo json_encode($respuesta);

?>