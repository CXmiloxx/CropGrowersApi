<?php
include './server/conexion.php';
configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

if ($metodo == 'GET') {
    try {
        if ($idProyecto) {
            $query = 'SELECT f.*, r.id AS idRespuesta, r.contenido AS respuestaContenido
                        FROM foros f
                        LEFT JOIN respuestas r ON f.id = r.idForo
                        WHERE f.idProyecto = ?';
            $consulta = $conexion->prepare($query);
            $consulta->execute([$idProyecto]);
            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            $foros = [];
            foreach ($datos as $fila) {
                $idForo = $fila['id'];
                if (!isset($foros[$idForo])) {
                    $foros[$idForo] = [
                        'id' => $fila['id'],
                        'contenido' => $fila['contenido'],
                        'respuestas' => [],
                    ];
                }
                if ($fila['idRespuesta']) {
                    $foros[$idForo]['respuestas'][] = [
                        'id' => $fila['idRespuesta'],
                        'contenido' => $fila['respuestaContenido'],
                    ];
                }
            }
            $respuesta = formatearRespuesta(true, "Foros obtenidos exitosamente", array_values($foros));
        } else {
            $respuesta = formatearRespuesta(false, "Debes de tener un ID especificado.");
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error al conectar con la base de datos: " . $e->getMessage());
    }
    echo json_encode($respuesta);
}
