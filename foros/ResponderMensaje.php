<?php
    include './server/conexion.php';
    configurarHeaders();

    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idForo = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;
    date_default_timezone_set("America/Bogota");

    if ($metodo == 'POST') {
        try {
            if ($idForo) {
                $contenido = trim(file_get_contents('php://input'));
                $datos = json_decode($contenido, true);
                
                if (isset($datos['idUsuario'], $datos['contenido'] ) ) {

                    $idUsuario = $datos['idUsuario'];
                    $contenido = $datos['contenido'];
                    $fecha_actual = date("Y-m-d H:i:s");
                    
                    $query = "INSERT INTO respuestas (idForo, idUsuario, contenido, fecha)
                                VALUES (:idF,:idU, :con, :fec)";
                                
                    $consulta = $conexion->prepare($query);
                    $consulta->bindParam(':idF', $idForo);
                    $consulta->bindParam(':idU', $idUsuario);
                    $consulta->bindParam(':con', $contenido);
                    $consulta->bindParam(':fec', $fecha_actual);

                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Respuesta creada correctamente.');
                    } else {
                        $respuesta = formatearRespuesta(false, 'Error al crear la respuesta.');
                    }
                    
                }else{
                    $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');
                }
            }else{
                $respuesta = formatearRespuesta(false, 'El ID del foro es obligatorio.');
            }
        }catch (Exception $e){
            $respuesta = formatearRespuesta(false, 'Error de base de datos: '. $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }
    
    echo json_encode($respuesta);
?>