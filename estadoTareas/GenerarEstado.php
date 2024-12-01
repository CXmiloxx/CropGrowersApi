<?php

    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idTarea = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'POST') {
        try {
            $contenido = trim(file_get_contents('php://input'));
            $datos = json_decode($contenido, true);
            
            if (isset($datos['idUsuario'], $datos['estado'], $datos['comentarios'] ) ) {
                $idUsuario = $datos['idUsuario'];
                $estado = $datos['estado'];
                $comentarios = $datos['comentarios'];

                $query = "INSERT INTO estadoTareas (idTarea, idUsuario, estado, comentarios) VALUES (:idT, :idU, :est, :com)";
                $consulta = $conexion->prepare($query);
                $consulta->bindParam(':idT', $idTarea);
                $consulta->bindParam(':idU', $idUsuario);
                $consulta->bindParam(':est', $estado);
                $consulta->bindParam(':com', $comentarios);
                
                if ($consulta->execute()) {
                    $respuesta = formatearRespuesta(true, 'Tarea creada correctamente.');

                }else{
                    $respuesta = formatearRespuesta(false, 'No se pudo crear la tarea. Verifica los datos y vuelve a intentarlo.');
                }
            }else{
                $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');
            }
        }catch(Exception $e){
            $respuesta = formatearRespuesta(false, 'Error de base de datos: '. $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }
    
    echo json_encode($respuesta);
?>