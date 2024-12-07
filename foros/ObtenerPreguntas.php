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
            $query = 'SELECT 
                        f.id AS idForo, 
                        f.contenido AS foroContenido, 
                        f.fecha AS foroFecha, 
                        u.nombre AS foroUsuarioNombre, 
                        u.apellido AS foroUsuarioApellido,
                        u.id AS foroIdUsuario, 
                        r.id AS idRespuesta, 
                        r.contenido AS respuestaContenido, 
                        r.fecha AS respuestaFecha, 
                        ur.nombre AS respuestaUsuarioNombre, 
                        ur.apellido AS respuestaUsuarioApellido,
                        ur.id AS respuestaIdUsuario
                      FROM foros f
                      INNER JOIN usuarios u ON f.idUsuario = u.id
                      LEFT JOIN respuestas r ON f.id = r.idForo
                      LEFT JOIN usuarios ur ON r.idUsuario = ur.id
                      WHERE f.idProyecto = ?';
            $consulta = $conexion->prepare($query);
            $consulta->execute([$idProyecto]);
            $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

            $foros = [];
            foreach ($datos as $fila) {
                $idForo = $fila['idForo'];
                if (!isset($foros[$idForo])) {
                    $foros[$idForo] = [
                        'id' => $idForo,
                        'contenido' => $fila['foroContenido'],
                        'fecha' => $fila['foroFecha'],
                        'usuario' => [
                            'nombre' => $fila['foroUsuarioNombre'],
                            'apellido' => $fila['foroUsuarioApellido'],
                            'id' => $fila['foroIdUsuario'],
                        ],
                        'respuestas' => [],
                    ];
                }
                if ($fila['idRespuesta']) {
                    $foros[$idForo]['respuestas'][] = [
                        'id' => $fila['idRespuesta'],
                        'contenido' => $fila['respuestaContenido'],
                        'fecha' => $fila['respuestaFecha'],
                        'usuario' => [
                            'nombre' => $fila['respuestaUsuarioNombre'],
                            'apellido' => $fila['respuestaUsuarioApellido'],
                            'id' => $fila['respuestaIdUsuario'],
                        ],
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

?>