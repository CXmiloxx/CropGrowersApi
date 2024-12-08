<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'b6qkhpc2m5e8ouerlzpv-mysql.services.clever-cloud.com';
$bdname = 'b6qkhpc2m5e8ouerlzpv';
$user = 'ujuih3xrmk2lv7zk';
$password = 'VHB5B9CkSY5PGS2Nb8qc';

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

function configurarHeaders() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json");
}



?>