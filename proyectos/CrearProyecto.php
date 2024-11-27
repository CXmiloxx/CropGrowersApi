<?php
include './server/conexion.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];


if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);
    
        try {
                if (isset($datos['nombre'], $datos['descripcion'], $datos['fechaInicio'], $datos['fechaFin'], $datos['cantPersonas'], $datos['cantTareas'], $datos['instructor_id']) ) {
                    $nombre = $datos['nombre'];
                    $descripcion = $datos['descripcion'];
                    $fechaInicio = $datos['fechaInicio'];
                    $fechaFin = $datos['fechaFin'];
                    $cantPersonas = $datos['cantPersonas'];
                    $cantTareas = $datos['cantTareas'];
                    $idInstructor = $datos['instructor_id'];

                    $query = "INSERT INTO proyectos (instructor_id, nombre, descripcion, fechaInicio, fechaFin, cantPersonas, cantTareas) 
                            VALUES (:idI, :nombre, :descripcion, :fechaInicio, :fechaFin, :cantPersonas, :cantTareas)";
                    $consulta = $conexion->prepare($query);

                    $consulta->bindParam(':idI', $idInstructor);
                    $consulta->bindParam(':nombre', $nombre);
                    $consulta->bindParam(':descripcion', $descripcion);
                    $consulta->bindParam(':fechaInicio', $fechaInicio);
                    $consulta->bindParam(':fechaFin', $fechaFin);
                    $consulta->bindParam(':cantPersonas', $cantPersonas);
                    $consulta->bindParam(':cantTareas', $cantTareas);

                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Proyecto creado correctamente.');
                    } else {
                        $respuesta = formatearRespuesta(false, 'No se pudo crear el proyecto. Verifica los datos y vuelve a intentarlo.');
                    }
                } else {
                    $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario.');
                }
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, 'Error de base de datos: ' . $e->getMessage());
        }
} else {
    $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
}

echo json_encode($respuesta);
?>
