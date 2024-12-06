<?php

    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'DELETE') {
        try {
            if ($idProyecto) {
                $query = "DELETE FROM proyectos WHERE id = ?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idProyecto]);

                if ($consulta->rowCount() > 0) {
                    $respuesta = formatearRespuesta(true, "Proyecto eliminado exitosamente");
                } else {
                    $respuesta = formatearRespuesta(false, "Proyecto no encontrado");
                }
            }else{
                $respuesta = formatearRespuesta(false, 'El id del Proyecto no fue encontrado. Valida que el id exista');
            }
        }catch(Exception $e){
            $respuesta = formatearRespuesta(false, 'Error de base de datos: '. $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba DELETE.');
    }
    
    echo json_encode($respuesta);

?>