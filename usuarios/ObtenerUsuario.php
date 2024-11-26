<?php
    include './server/conexion.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo == 'GET') {
        try {
            if ($idUsuario) {
                $query = "SELECT * FROM usuarios WHERE idUsuario = ?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idUsuario]);
                $datos = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($datos) {
                    formatearRespuesta(true, "usuario obtenido exitosamente", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún usuario con el ID especificado.");
                }
            } else {
                $query = "SELECT * FROM usuarios";
                $consulta = $conexion->prepare($query);
                $consulta->execute();
                $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

                if ($datos) {
                    formatearRespuesta(true, "usuarios obtenidos exitosamente", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontraron usuarios.");
                }
            }
        } catch (PDOException $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta a la base de datos: " . $e->getMessage());
        }
    } else {
        $respuesta = formatearRespuesta(false, "El método de la petición no es válido. Se esperaba GET");
    }
    echo json_encode($respuesta);
?>
