<?php
//Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/database.php';

//Instanciar la base de datos y obtener la conexión
$database = new Database();
$db = $database->getConnection();

//OPERACIÓN: LEER MÉTODOS DE PAGO (READ)

function obtenerMetodosPago($db) {
    //Recupera todas las configuraciones de pago registradas
    $query = "SELECT Id_Metodo_Pago, Token_Pago, Fecha_Expiracion, Tipo_Pago, Estado_Pago FROM METODO_DE_PAGO ORDER BY Id_Metodo_Pago DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

//DETECTAR ACCIONES ENVIADAS POR FORMULARIOS (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //OPERACIÓN CREAR MÉTODO DE PAGO (CREATE)
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $token_pago = trim($_POST['token_pago']);
        $fecha_expiracion = !empty($_POST['fecha_expiracion']) ? $_POST['fecha_expiracion'] : null;
        $tipo_pago = $_POST['tipo_pago'];
        $estado_pago = $_POST['estado_pago'];

        if (!empty($tipo_pago) && !empty($estado_pago) && !empty($token_pago)) {
            
            // OPTIONAL: Validación previa para que el Token sea único y no salte el AUTO_INCREMENT
            $check_query = "SELECT COUNT(*) FROM METODO_DE_PAGO WHERE Token_Pago = :token_pago";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':token_pago', $token_pago);
            $check_stmt->execute();

            if ($check_stmt->fetchColumn() > 0) {
                // Redirige con error si el token ya existe (puedes mapear un mensaje personalizado si quieres)
                header("Location: ../views/gestion_metodos_pago.php?msg=error");
                exit();
            }

            // Inserción normal
            $query = "INSERT INTO METODO_DE_PAGO (Token_Pago, Fecha_Expiracion, Tipo_Pago, Estado_Pago) 
                      VALUES (:token_pago, :fecha_expiracion, :tipo_pago, :estado_pago)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':token_pago', $token_pago);
            $stmt->bindParam(':fecha_expiracion', $fecha_expiracion);
            $stmt->bindParam(':tipo_pago', $tipo_pago);
            $stmt->bindParam(':estado_pago', $estado_pago);

            if ($stmt->execute()) {
                header("Location: ../views/gestion_metodos_pago.php?msg=success_create");
                exit();
            }
        }
        header("Location: ../views/gestion_metodos_pago.php?msg=error");
        exit();
    }

    //OPERACIÓN ACTUALIZAR MÉTODO DE PAGO (UPDATE)
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id_metodo_pago'];
        $token_pago = trim($_POST['token_pago']);
        $fecha_expiracion = !empty($_POST['fecha_expiracion']) ? $_POST['fecha_expiracion'] : null;
        $tipo_pago = $_POST['tipo_pago'];
        $estado_pago = $_POST['estado_pago'];

        if (!empty($id) && !empty($tipo_pago) && !empty($estado_pago)) {
            $query = "UPDATE METODO_DE_PAGO 
                      SET Token_Pago = :token_pago, Fecha_Expiracion = :fecha_expiracion, Tipo_Pago = :tipo_pago, Estado_Pago = :estado_pago 
                      WHERE Id_Metodo_Pago = :id";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':token_pago', $token_pago);
            $stmt->bindParam(':fecha_expiracion', $fecha_expiracion);
            $stmt->bindParam(':tipo_pago', $tipo_pago);
            $stmt->bindParam(':estado_pago', $estado_pago);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: ../views/gestion_metodos_pago.php?msg=success_update");
                exit();
            }
        }
        header("Location: ../views/gestion_metodos_pago.php?msg=error");
        exit();
    }
}

//DETECTAR ACCIONES ENVIADAS POR URL (GET)

//OPERACIÓN ELIMINAR MÉTODO DE PAGO (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    if (!empty($id)) {
        $query = "DELETE FROM METODO_DE_PAGO WHERE Id_Metodo_Pago = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        try {
            if ($stmt->execute()) {
                header("Location: ../views/gestion_metodos_pago.php?msg=success_delete");
                exit();
            }
        } catch (PDOException $e) {
            //Si una donación o suscripción ya usó este método de pago,
            //la base de datos bloquea el borrado físico para salvaguardar la auditoría financiera.
            header("Location: ../views/gestion_metodos_pago.php?msg=error_constraint");
            exit();
        }
    }
}
?>