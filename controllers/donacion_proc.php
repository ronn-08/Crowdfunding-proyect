<?php
//Incluir la conexión segura a la base de datos
require_once __DIR__ . '/../config/database.php';

//Inicializar la conexión
$database = new Database();
$db = $database->getConnection();

// Verificar que los datos provengan del formulario de donación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura y sanitización de variables básicas
    $id_usuario     = $_POST['id_usuario'];
    $id_causa       = $_POST['id_causa'];
    $id_metodo_pago = $_POST['id_metodo_pago'];
    $total_donado   = floatval($_POST['total_donado']);
    $es_anonimo     = isset($_POST['es_anonimo']) ? 'S' : 'N';
    $mensaje_muro   = trim($_POST['mensaje_muro']);

    //Validación de negocio inicial
    if (empty($id_usuario) || empty($id_causa) || empty($id_metodo_pago) || $total_donado <= 0) {
        header("Location: ../views/realizar_donacion.php?id_causa=$id_causa&msg=error_campos");
        exit();
    }

    try {

        //INICIO DE LA TRANSACCIÓN (Propiedades ACID)

        $db->beginTransaction();

        //Paso 1: Calcular la comisión fija de la plataforma (Simulación de un 5%)
        $comision = $total_donado * 0.05;

        //Paso 2: Insertar el registro principal en la tabla DONACION
        $query_donacion = "INSERT INTO DONACION (Tipo_Donacion, Es_Anonimo, Total_Donado, Comision, METODO_DE_PAGO_Id_Metodo_Pago, USUARIO_Id_Usuario, CAUSA_SOCIAL_Id_Causa) 
                           VALUES ('Unica', :es_anonimo, :total_donado, :comision, :id_metodo_pago, :id_usuario, :id_causa)";
        
        $stmt_don = $db->prepare($query_donacion);
        $stmt_don->bindParam(':es_anonimo', $es_anonimo);
        $stmt_don->bindParam(':total_donado', $total_donado);
        $stmt_don->bindParam(':comision', $comision);
        $stmt_don->bindParam(':id_metodo_pago', $id_metodo_pago);
        $stmt_don->bindParam(':id_usuario', $id_usuario);
        $stmt_don->bindParam(':id_causa', $id_causa);
        $stmt_don->execute();

        //Recuperar el ID de la donación que se acaba de autogenerar
        $id_donacion_reciente = $db->lastInsertId();

        // Paso 3: Generar el Comprobante Digital (RF 09)
        $code_transaccion = "TXN-" . strtoupper(bin2hex(random_bytes(6))); //Código alfanumérico único para pasarelas
        $pdf_path         = "uploads/comprobantes/comprobante_" . $code_transaccion . ".pdf";
        $qr_mock          = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $code_transaccion;

        $query_comprobante = "INSERT INTO COMPROBANTE (pdf, Estado_Pago, QR_Verificacion, Code_Transaccion, DONACION_Id_Donacion) 
                              VALUES (:pdf, 'Aprobado', :qr, :code_tx, :id_donacion)";
        
        $stmt_com = $db->prepare($query_comprobante);
        $stmt_com->bindParam(':pdf', $pdf_path);
        $stmt_com->bindParam(':qr', $qr_mock);
        $stmt_com->bindParam(':code_tx', $code_transaccion);
        $stmt_com->bindParam(':id_donacion', $id_donacion_reciente);
        $stmt_com->execute();

        //Paso 4: Insertar en el Muro de Agradecimientos de forma condicional (RF 12)
        //Si el donante escribió un mensaje de apoyo, se guarda indexado a la donación
        if (!empty($mensaje_muro)) {
            $query_muro = "INSERT INTO MURO_AGRADECIMIENTO (Mensaje, Visibilidad, DONACION_Id_Donacion) 
                           VALUES (:mensaje, 'S', :id_donacion)";
            
            $stmt_mur = $db->prepare($query_muro);
            $stmt_mur->bindParam(':mensaje', $mensaje_muro);
            $stmt_mur->bindParam(':id_donacion', $id_donacion_reciente);
            $stmt_mur->execute();
        }

        //COMMIT: SI TODO SALIÓ BIEN, PERSISTIMOS LOS CAMBIOS FÍSICOS

        $db->commit();

        //Redireccionar al usuario a la pantalla de éxito mostrando su recibo generado
        header("Location: ../views/donacion_exitosa.php?code=" . $code_transaccion . "&monto=" . $total_donado);
        exit();

    } catch (Exception $e) {

        //ROLLBACK: SI ALGO FALLÓ, REVERTIMOS TODO COMO SI NADA HUBIERA PASADO

        $db->rollBack();
        
        //Redireccionar con la traza de error controlado
        header("Location: ../views/realizar_donacion.php?id_causa=$id_causa&msg=error_transaccion&debug=" . urlencode($e->getMessage()));
        exit();
    }
}
?>