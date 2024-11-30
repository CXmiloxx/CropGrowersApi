<?php
include './server/conexion.php';
configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idForo = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

if ($metodo == 'GET') {
    try {
        if ($idForo) {
            $query = 'SELECT f.id AS foro_id, f.idProyecto, f.idUsuario, f.contenido AS foro_contenido, f.fecha AS foro_fecha,
                        r.id AS respuesta_id, r.idUsuario AS respuesta_usuario_id, r.contenido AS respuesta_contenido, r.fecha AS respuesta_fecha
                        FROM foros f
                        LEFT JOIN respuestas r ON f.id = r.idForo
                        WHERE f.id = :foro_id';
            $consulta = $conexion->prepare($query);
            $consulta->execute([':foro_id' => $idForo]);
            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if ($datos) {
                $respuesta = formatearRespuesta(true, "Respuestas obtenidas exitosamente", $datos);
            } else {
                $respuesta = formatearRespuesta(false, "No se encontraron respuestas para el foro con el ID especificado.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "No se encontró ningún ID. Debe especificar el ID.");
        }
    } catch (PDOException $e) {
        $respuesta = formatearRespuesta(false, "Error al obtener las respuestas: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba GET.");
}

echo json_encode($respuesta);

?>
