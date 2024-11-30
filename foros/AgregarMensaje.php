<?php
    include './server/conexion.php';
    configurarHeaders();
    date_default_timezone_set("America/Bogota");

    
    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;
    
    if ($metodo == 'POST') {
        try {
            if ($idProyecto) {
                $contenido = trim(file_get_contents('php://input'));
                $datos = json_decode($contenido, true);
                
                if ( isset( $datos['idUsuario'], $datos['contenido'] ) ) {
                    $idUsuario = $datos['idUsuario'];
                    $contenido = $datos['contenido'];
                    $fecha_actual = date("Y-m-d H:i:s");

                    $query = "INSERT INTO foros (idUsuario, idProyecto, contenido, fecha)
                            VALUES (:idU, :idP, :con, :fec)";
                    $consulta = $conexion->prepare($query);
                    $consulta->bindParam(':idU', $idUsuario);
                    $consulta->bindParam(':idP', $idProyecto);
                    $consulta->bindParam(':con', $contenido);
                    $consulta->bindParam(':fec', $fecha_actual);
                    
                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Pregunta creado correctamente.');
                    }else{
                        $respuesta = formatearRespuesta(false, 'No se pudo crear la pregunta. Verifica los datos y vuelve a intentarlo.');
                    }
                }else{
                    $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');
                }
            }else{
                $respuesta = formatearRespuesta(false, 'El ID del proyecto es obligatorio.');
            }
        }catch (Exception $e) {
            $respuesta = formatearRespuesta(false, 'Error de base de datos: '. $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }
    
    echo json_encode($respuesta);
?>