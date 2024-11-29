<?php
include './server/conexion.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'POST') {
    $contenido = trim(file_get_contents("php://input"));
    $datos = json_decode($contenido, true);

    try {
        if (isset($datos['idUsuario'], $datos['idProyecto'])) {
            $idUsuario = $datos['idUsuario'];
            $idProyecto = $datos['idProyecto'];

            $queryVerificar = "SELECT COUNT(*) as total FROM usuariosProyectos
                            WHERE idUsuario = :idU AND idProyecto = :idP";
            $consultaVerificar = $conexion->prepare($queryVerificar);
            $consultaVerificar->bindParam(':idU', $idUsuario);
            $consultaVerificar->bindParam(':idP', $idProyecto);
            $consultaVerificar->execute();
            $resultado = $consultaVerificar->fetch(PDO::FETCH_ASSOC);

            if ($resultado['total'] > 0) {
                $respuesta = formatearRespuesta(false, 'El usuario ya está registrado en este proyecto.');
            } else {
                $queryRelacion = "INSERT INTO usuariosProyectos (idUsuario, idProyecto)
                                VALUES (:idU, :idP)";
                $consultaRelacion = $conexion->prepare($queryRelacion);
                $consultaRelacion->bindParam(':idU', $idUsuario);
                $consultaRelacion->bindParam(':idP', $idProyecto);

                if ($consultaRelacion->execute()) {
                    $respuesta = formatearRespuesta(true, 'Usuario registrado en el proyecto correctamente.');
                } else {
                    $respuesta = formatearRespuesta(false, 'Error al registrar al usuario en el proyecto.');
                }
            }
        } else {
            $respuesta = formatearRespuesta(false, 'Faltan datos requeridos: idUsuario o idProyecto.');
        }
    } catch (Exception $e) {
        $respuesta = formatearRespuesta(false, 'Error de base de datos: ' . $e->getMessage());
    }
} else {
    $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
}

echo json_encode($respuesta);
