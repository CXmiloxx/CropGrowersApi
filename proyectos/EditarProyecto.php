<?php
    include './server/conexion.php';
    
    configurarHeaders();
    
    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3])? $segmentos_uri[3] : null;

    if ($metodo == 'PUT') {
        try {
            if ($idProyecto) {
                $contenido = trim(file_get_contents('php://input'));
                $datos = json_decode($contenido, true);

                if (isset($datos['nombre'], $datos['descripcion'], $datos['fechaInicio'] ,$datos['fechaFin'] ) ) {
                    $nombre = $datos['nombre'];
                    $descripcion = $datos['descripcion'];
                    $fechaInicio = $datos['fechaInicio'];
                    $fechaFin = $datos['fechaFin'];

                    $query = "UPDATE proyectos SET nombre = :nom, descripcion = :des,fechaInicio = :fecI, fechaFin = :fecF WHERE id = :id";
                    
                    $consulta = $conexion->prepare($query);
                    $consulta->bindParam(':nom', $nombre);
                    $consulta->bindParam(':des', $descripcion);
                    $consulta->bindParam(':fecI', $fechaInicio);
                    $consulta->bindParam(':fecF', $fechaFin);
                    $consulta->bindParam(':id', $idProyecto);
                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, "Proyecto actualizado exitosamente");
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo actualizar el proyecto.");
                    }
                }else{
                    $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');                }
            }else{
                $respuesta = formatearRespuesta(false, 'Debes especificar un ID valido. Vuelve a intentarlo');
            }
        }catch (PDOException $e){
            $respuesta = formatearRespuesta(false, 'Ocurrió un error inesperado. Detalles del error: ' . $e->getMessage());
        }
    }else{
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba PUT.');
    }
    echo json_encode($respuesta);

?>