<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

$request = explode('?', trim($_SERVER['REQUEST_URI'], '/'))[0];
$request = explode('/', $request);
$resource = isset($request[0]) ? $request[0] : null;
$action = isset($request[1]) ? $request[1] : $_SERVER['REQUEST_METHOD'];

if (empty($resource)) {
    echo json_encode([
        'status' => true,
        'message' => 'Bienvenido a la API',
        'detalles' => [

            'usuario' => [
                'UrlObtener' => 'https://apisubastock.cleverapps.io/usuarios/ObtenerUsuario',
                'UrlRegistroUsuario' => 'https://apisubastock.cleverapps.io/usuarios/RegistroUsuario',
                'UrlEditaraUsuario'=> 'https://apisubastock.cleverapps.io/usuarios/EditaraUsuario',
                'UrlEliminarUsuario' => 'https://apisubastock.cleverapps.io/usuarios/EliminarUsuario',
                'UrlLogin' => 'https://apisubastock.cleverapps.io/usuarios/Login',
            ],
        ]
    ]);
    exit;
}

$filepath = "$resource/$action.php";
if (file_exists($filepath)) {
    require $filepath;
} else {
    echo json_encode([
        'status' => false,
        'message' => 'El archivo correspondiente a la acción y recurso solicitados no existe.',
        'details' => [
            'resource' => $resource,
            'action' => $action,
            'filepath' => $filepath
        ]
    ]);
}
?>
