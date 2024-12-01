<?php
include './server/conexion.php';
configurarHeaders();

$metodo = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segmentos_uri = explode('/', $uri);
$idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
$idProyecto = isset($segmentos_uri[4]) && is_numeric($segmentos_uri[4]) ? $segmentos_uri[4] : null;


if ($metodo == 'GET') {
    try {
        if ($idUsuario) {
            if ($idProyecto) {
                $query = "SELECT * FROM usuariosProyectos WHERE idUsuario = ? AND idProyecto = ?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idUsuario, $idProyecto]);
                $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

                if ($datos) {
                    $respuesta = formatearRespuesta(true, "Usuario participando en el proyecto.", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún usuario participando en el proyecto.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "Faltan datos requeridos: idProyecto.");
            }
        } else {
            $respuesta = formatearRespuesta(false, "Faltan datos requeridos: idUsuario.");
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, "Error de base de datos: " . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba GET.");
}

echo json_encode($respuesta);
