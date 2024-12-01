<?php
    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idTarea = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'DELETE') {
        try {
            if ($idTarea) {
                $query = "DELETE FROM tareas WHERE id = ?";
                $consulta = $conexion->prepare($query);
                $consulta->execute([$idTarea]);
                if ($consulta->execute()) {
                    $respuesta = formatearRespuesta(true, "Tarea eliminada exitosamente");
                } else {
                    $respuesta = formatearRespuesta(false, "Error al eliminar la tarea");
                }
            }else{
                $respuesta = formatearRespuesta(false, "Debes de especificar un ID de tarea.");
            }
        }catch(Exception $e){
            $respuesta = formatearRespuesta(false, "Error de base de datos: ". $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, "Método no permitido. Se esperaba DELETE.");
    }
    
    echo json_encode($respuesta);
?>