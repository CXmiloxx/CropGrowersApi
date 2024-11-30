<?php
    include './server/conexion.php';
    include './server/Token.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
            $decodedToken = Token::validateToken($token);

            if ($decodedToken['valid']) {
                $respuesta = formatearRespuesta(true, 'Token válido',["data" => ["correo" => $decodedToken['correo'], "id" => $decodedToken['id'], "rol" =>$decodedToken['rol'] ] ] );
            } else {
                $respuesta = formatearRespuesta(false, 'Token inválido');
            }
        } else {
            $respuesta = formatearRespuesta(false, 'Token no proporcionado');
        }
    } else {
        $respuesta = formatearRespuesta(false, 'Método no permitido, se esperaba POST');
    }

    echo json_encode($respuesta);
?>
