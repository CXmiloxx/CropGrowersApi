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

        if (isset($datos['nombre'], $datos['apellido'], $datos['correo'], $datos['contra'], $datos['telefono'])) {
            $nombre = $datos['nombre'];
            $apellido = $datos['apellido'];
            $correo = $datos['correo'];
            $contra = password_hash($datos['contra'], PASSWORD_DEFAULT);
            $telefono = $datos['telefono'];

            try {
                $consultaCorreo = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = :correo");
                $consultaCorreo->bindParam(':correo', $correo);
                $consultaCorreo->execute();

                if ($consultaCorreo->fetchColumn() > 0) {
                    $respuesta = formatearRespuesta(false, 'El correo ya existe. Regístrese con otro correo.');
                } else {
                    $rol = strpos(strtolower($correo), '@sena.edu.co') !== false ? 'instructor' : 'aprendiz';

                    $consulta = $conexion->prepare("
                        INSERT INTO usuarios (nombre, apellido, correo, rol, telefono, contra) 
                        VALUES (:nombre, :apellido, :correo, :rol, :telefono, :contra)
                    ");
                    $consulta->bindParam(':nombre', $nombre);
                    $consulta->bindParam(':apellido', $apellido);
                    $consulta->bindParam(':correo', $correo);
                    $consulta->bindParam(':rol', $rol);
                    $consulta->bindParam(':telefono', $telefono);
                    $consulta->bindParam(':contra', $contra);

                    if ($consulta->execute()) {
                        $respuesta = formatearRespuesta(true, 'Usuario creado correctamente.');
                    } else {
                        $respuesta = formatearRespuesta(false, "No se pudo crear el usuario. Verifica los datos y vuelve a intentarlo.");
                    }
                }
            } catch (Exception $e) {
                $respuesta = formatearRespuesta(false, 'Error de base de datos: ' . $e->getMessage());
            }
        } else {
            $respuesta = formatearRespuesta(false, 'Faltan datos en el formulario. Asegúrate de incluir todos los campos necesarios.');
        }
    } else {
        $respuesta = formatearRespuesta(false, 'Método no permitido. Se esperaba POST.');
    }

    echo json_encode($respuesta);
?>
