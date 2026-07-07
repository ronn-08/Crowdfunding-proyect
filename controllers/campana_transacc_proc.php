<?php
//Incluir la conexión segura a la base de datos
require_once __DIR__ . '/../config/database.php';

//Inicializar el puente de datos
$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura de datos generales de la causa
    $id_usuario         = intval($_POST['id_usuario']); 
    $titulo             = trim($_POST['titulo']);
    $descripcion        = trim($_POST['descripcion']);
    $beneficiario       = trim($_POST['beneficiario']);
    $pais               = trim($_POST['pais']);
    $ciudad             = trim($_POST['ciudad']);
    
    // Captura de datos financieros y plazos
    $monto_objetivo     = floatval($_POST['monto_objetivo']);
    $monto_minimo       = floatval($_POST['monto_minimo']);
    $fecha_inicio       = $_POST['fecha_inicio'];
    $fecha_finalizacion = $_POST['fecha_finalizacion'];
    
    //Captura del arreglo de categorías seleccionadas
    $categorias         = isset($_POST['categorias']) ? $_POST['categorias'] : [];

    //LAYER DE VALIDACIONES DE REGLAS DE NEGOCIO

    if (empty($titulo) || empty($descripcion) || empty($monto_objetivo) || empty($fecha_inicio) || empty($fecha_finalizacion) || empty($categorias)) {
        header("Location: ../views/crear_campana.php?msg=error_campos");
        exit();
    }

    if (strlen($titulo) > 120 || strlen($descripcion) > 2000) { 
        header("Location: ../views/crear_campana.php?msg=error_longitud");
        exit();
    }

    $date1 = new DateTime($fecha_inicio);
    $date2 = new DateTime($fecha_finalizacion);
    $interval = $date1->diff($date2);
    $duracion_dias = $interval->days;

    if ($duracion_dias < 7 || $duracion_dias > 180 || $date2 <= $date1) { 
        header("Location: ../views/crear_campana.php?msg=error_fechas");
        exit();
    }


    //INICIO DEL BLOQUE TRANSACCIONAL OPTIMIZADO (MÉTODO ATÓMICO)

    try {
        $db->beginTransaction(); //Activamos el entorno seguro

        //PASO 1: Inserción en la tabla principal CAUSA_SOCIAL
        $query_causa = "INSERT INTO CAUSA_SOCIAL (Titulo, Descripcion, Beneficiario, Pais, Ciudad, USUARIO_Id_Usuario) 
                        VALUES (:titulo, :descripcion, :beneficiario, :pais, :ciudad, :id_usuario)";
        $stmt_causa = $db->prepare($query_causa);
        
        //Pasamos el mapa exacto de parámetros directo en el execute
        $stmt_causa->execute([
            ':titulo'       => $titulo,
            ':descripcion'  => $descripcion,
            ':beneficiario' => $beneficiario,
            ':pais'         => $pais,
            ':ciudad'       => $ciudad,
            ':id_usuario'   => $id_usuario
        ]);

        //Recuperamos el ID autonumérico asignado por MySQL
        $id_causa_reciente = $db->lastInsertId();

        //PASO 2: Inserción de la configuración económica en META_PLAZO
        $query_meta = "INSERT INTO META_PLAZO (Monto_Objetivo, Monto_Minimo, Fecha_Inicio, Fecha_Finalizacion, CAUSA_SOCIAL_Id_Causa) 
                       VALUES (:monto_obj, :monto_min, :fecha_ini, :fecha_fin, :id_causa)";
        $stmt_meta = $db->prepare($query_meta);
        
        $stmt_meta->execute([
            ':monto_obj'  => $monto_objetivo,
            ':monto_min'  => $monto_minimo,
            ':fecha_ini'  => $fecha_inicio,
            ':fecha_fin'  => $fecha_finalizacion,
            ':id_causa'   => $id_causa_reciente
        ]);

        //PASO 3: Relación Muchos a Muchos en CAUSA_CATEGORIA
        $query_cat = "INSERT INTO CAUSA_CATEGORIA (CAUSA_SOCIAL_Id_Causa, CATEGORIA_Id_Categoria) 
                      VALUES (:id_causa, :id_cat)";
        $stmt_cat = $db->prepare($query_cat);
        
        foreach ($categorias as $id_categoria_seleccionada) {
            $stmt_cat->execute([
                ':id_causa' => $id_causa_reciente,
                ':id_cat'   => intval($id_categoria_seleccionada)
            ]);
        }

        //PASO 4: Procesamiento de la fotografía de portada (EVIDENCIA)
        if (isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error'] == 0) {
            $filename = $_FILES['foto_principal']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png'];

            if (in_array(strtolower($ext), $allowed)) {
                $nuevo_nombre = "portada_" . $id_causa_reciente . "_" . time() . "." . $ext;
                $ruta_destino = "../uploads/evidencias/" . $nuevo_nombre;

                if (move_uploaded_file($_FILES['foto_principal']['tmp_name'], $ruta_destino)) {
                    $query_foto = "INSERT INTO EVIDENCIA (Tipo_Evidencia, Archivo, Descripcion, CAUSA_SOCIAL_Id_Causa) 
                                   VALUES ('Foto', :archivo, 'Fotografía principal de portada de la campaña', :id_causa)";
                    $stmt_foto = $db->prepare($query_foto);
                    
                    $relative_path = "uploads/evidencias/" . $nuevo_nombre;
                    $stmt_foto->execute([
                        ':archivo'  => $relative_path,
                        ':id_causa' => $id_causa_reciente
                    ]);
                }
            }
        }


        //COMMIT DEFINITIVO: TODO SALIÓ BIEN, PERSISTIMOS EN LA BD

        $db->commit();
        header("Location: ../views/crear_campana.php?msg=success_campana");
        exit();

    } catch (Exception $e) {

        //ROLLBACK OPERATIVO: LIMPIAMOS LA BASE DE DATOS ANTE CUALQUIER FALLO

        $db->rollBack();
        header("Location: ../views/crear_campana.php?msg=error_transaccion&debug=" . urlencode($e->getMessage()));
        exit();
    }
}
?>