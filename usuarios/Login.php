<?php
    include './server/conexion.php';
    include './server/Token.php';


    configurarHeaders();

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
                $consultaUsuario = $conexion->prepare("SELECT id, contra, nombre, rol FROM usuarios WHERE correo = :cor");
                $consultaUsuario->bindParam(':cor', $correo);
                $consultaUsuario->execute();
                $usuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    if (password_verify($contra, $usuario['contra'])) {
                        unset($usuario['contra']);
                        
                        $token = Token::createToken($correo, $usuario['id'],$usuario['rol']);
                        
                        $respuesta = formatearRespuesta(true, 'Login exitoso', null ,$token);
                    } else {
                        $respuesta = formatearRespuesta(false, 'Contraseña incorrecta.');
                    }
                } else {
                    $respuesta = formatearRespuesta(false, 'El correo que ingresaste no esta registrado.');
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
