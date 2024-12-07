<?php
include './server/conexion.php';

configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idForo = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;


if ($metodo == 'DELETE') {
    
    try {
        if ($idForo ) {
                $query = "DELETE FROM foros WHERE id = ?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idForo]);

                if ($consulta->rowCount() > 0) {
                    $respuesta = formatearRespuesta(true, "Pregunta eliminada exitosamente." );
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró la pregunta especificada. $idForo");
                }
        } else {
            $respuesta = formatearRespuesta(false, "Debes especificar el ID de foro $idForo");
        }
    } catch (PDOException $e) {
        $respuesta = formatearRespuesta(false, "Error de base de datos: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba DELETE.");
}

echo json_encode($respuesta);
