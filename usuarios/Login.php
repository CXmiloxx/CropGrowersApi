<?php
    include './server/conexion.php';

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");

    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo == 'POST') {
        $contenido = trim(file_get_contents("php://input"));
        $datos = json_decode($contenido, true);

        if (isset($datos['correo'], $datos['contra'])) {
            $correo = filter_var($datos['correo'], FILTER_SANITIZE_EMAIL);
            $contra = trim($datos['contra']);

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(formatearRespuesta(false, 'El correo no tiene un formato válido.'));
                exit;
            }

            try {
                $consultaUsuario = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :cor");
                $consultaUsuario->bindParam(':cor', $correo);
                $consultaUsuario->execute();
                $usuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($contra, $usuario['contra'])) {
                    unset($usuario['contra']); //quita la contraseña de la respuesta

                    $respuesta = formatearRespuesta(true, 'Login exitoso', $usuario);
                } else {
                    $respuesta = formatearRespuesta(false, 'Correo o contraseña incorrectos.');
                }
            } catch (PDOException $e) {
                $respuesta = formatearRespuesta(false, 'Error en la base de datos: ' . $e->getMessage());
            }
        } else {
            $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario.');
        }
    } else {
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }

    echo json_encode($respuesta);

?>
