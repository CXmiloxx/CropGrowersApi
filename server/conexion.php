<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'b0monyi56lgjfo5zanjk-mysql.services.clever-cloud.com';
$bdname = 'b0monyi56lgjfo5zanjk';
$user = 'uaektswceptis4gv';
$password = 'lTV3kI7uS1zsAZddhWmD';

try {

    $conexion = new PDO("mysql:host=$host; dbname=$bdname", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(formatearRespuesta(false, "Error al conectar con la base de datos: " . $e->getMessage()));
    exit;
}


function formatearRespuesta($status, $message, $data = null, $token = null,)
{
    $respuesta = [
        'status' => $status,
        'message' => $message
    ];
    if ($data !== null) {
        $respuesta['data'] = $data;
    }
    if ($token !== null) {
        $respuesta['token'] = $token;
    }
    return $respuesta;
}

?>