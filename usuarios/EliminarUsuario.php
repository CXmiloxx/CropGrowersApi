<?php
    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idUsuario = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;

    if ($metodo == 'DELETE') {
        try {
            if ($idUsuario) {
                $query = "DELETE FROM usuarios WHERE idUsuario =?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idUsuario]);

                if ($consulta->execute()) {
                    $respuesta = formatearRespuesta(true, "Usuario eliminado exitosamente.");
                
                } else {
                    $respuesta = formatearRespuesta(false, "No se pudo eliminar el usuario.");
                }
            } else {
                $respuesta = formatearRespuesta(false, "Faltan datos requeridos: idUsuario.");
            }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error de base de datos: " . $e->getMessage());
        }

    }else{
        $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba DELETE.");
    }

    echo json_encode($respuesta);

?>
