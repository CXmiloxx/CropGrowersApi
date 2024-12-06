<?php
    include './server/conexion.php';
    configurarHeaders();
    
    $metodo = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segmentos_uri = explode('/', $uri);
    $idProyecto = isset($segmentos_uri[3]) && is_numeric($segmentos_uri[3]) ? $segmentos_uri[3] : null;
    
    if ($metodo == 'GET') {
        try {
            $queryEstudiantes = "SELECT COUNT(*) as cantidadEstudiantes
                                    FROM usuariosProyectos up
                                    JOIN usuarios u ON up.idUsuario = u.id
                                    WHERE up.idProyecto = ? ";
                                
            $queryTareas = "SELECT COUNT(*) as totalTareas
                            FROM tareas
                            WHERE idProyecto = ?";
                            
            $queryCompletadas = "SELECT COUNT(DISTINCT et.idTarea) as tareasCompletadas 
                                    FROM estadoTareas et
                                    JOIN tareas t ON et.idTarea = t.id
                                    WHERE t.idProyecto = ? AND et.estado = 'completada'";
    
            $estudiantesStmt = $conexion->prepare($queryEstudiantes);
            $estudiantesStmt->execute([$idProyecto]);
            $cantidadEstudiantes = $estudiantesStmt->fetch(PDO::FETCH_ASSOC)['cantidadEstudiantes'];
    
            $tareasStmt = $conexion->prepare($queryTareas);
            $tareasStmt->execute([$idProyecto]);
            $totalTareas = $tareasStmt->fetch(PDO::FETCH_ASSOC)['totalTareas'];
    
            $completadasStmt = $conexion->prepare($queryCompletadas);
            $completadasStmt->execute([$idProyecto]);
            $tareasCompletadas = $completadasStmt->fetch(PDO::FETCH_ASSOC)['tareasCompletadas'];
    
            $porcentajeCompletadas = $totalTareas > 0 ? ($tareasCompletadas / $totalTareas) * 100 : 0;
    
            $respuesta = formatearRespuesta(true, "Estadísticas obtenidas exitosamente", [
                'cantidadEstudiantes' => $cantidadEstudiantes,
                'totalTareas' => $totalTareas,
                'tareasCompletadas' => $tareasCompletadas,
                'porcentajeCompletadas' => round($porcentajeCompletadas, 2),
            ]);
        } catch (Exception $e) {
            $respuesta = formatearRespuesta(false, "Error en la consulta: " . $e->getMessage());
        }
    
        echo json_encode($respuesta);
    }
    
?>