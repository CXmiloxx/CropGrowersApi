<?php
    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'GET') {
        try {
            if ($idProyecto) {
                $query = 'SELECT * FROM foros WHERE idProyecto = ?';
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idProyecto]);
                $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

                if ($datos) {
                    $respuesta = formatearRespuesta(true, "Foros obtenidos exitosamente", $datos);
                } else {
                    $respuesta = formatearRespuesta(false, "No se encontró ningún foro con el proyecto especificado.");
                }
            }else{
                $respuesta = formatearRespuesta(false, "Debes de tener un ID especificado.");
            }
        }catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error al conectar con la base de datos: ". $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba GET.");
    }
    
    echo json_encode($respuesta);
?>