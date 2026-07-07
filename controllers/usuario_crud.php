<?php
// Incluir la conexión segura a la base de datos
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();


//OPERACIÓN: LEER USUARIOS (READ)
function obtenerUsuarios($db) {
    $query = "SELECT Id_Usuario, Nombre, Apellido, Correo, Telefono, Tipo_Usuario, Clave FROM USUARIO ORDER BY Id_Usuario DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//ELIMINAR USUARIO 

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    try {
        $query_delete = "DELETE FROM USUARIO WHERE Id_Usuario = :id";
        $stmt_del = $db->prepare($query_delete);
        $stmt_del->execute([':id' => $id_usuario]);

        // Si se elimina con éxito, regresa con la alerta configurada en tu vista
        header("Location: ../views/gestion_usuarios.php?msg=success_eliminar");
        exit();

    } catch (PDOException $e) {
        // Código de error 23000 = Violación de restricción de integridad (Llave foránea)
        if ($e->getCode() == '23000') {
            header("Location: ../views/gestion_usuarios.php?msg=error_restriccion");
        } else {
            header("Location: ../views/gestion_usuarios.php?msg=error_bd&info=" . urlencode($e->getMessage()));
        }
        exit();
    }
}


//OPERACIÓN CREAR USUARIO (INSERT - Registro Público / Admin)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre       = trim($_POST['nombre']);
    $apellido     = trim($_POST['apellido']);
    $correo       = trim($_POST['correo']);
    $telefono     = trim($_POST['telefono']);
    $tipo_usuario = trim($_POST['tipo_usuario']); 
    $clave        = trim($_POST['clave']); 
    $origen       = isset($_POST['origen']) ? $_POST['origen'] : 'admin';

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($tipo_usuario) || empty($clave)) {
        header("Location: ../views/" . ($origen === 'publico' ? "registro_publico.php" : "gestion_usuarios.php") . "?msg=error_campos");
        exit();
    }

    try {
        $query_insert = "INSERT INTO USUARIO (Nombre, Apellido, Correo, Telefono, Tipo_Usuario, Clave) 
                        VALUES (:nombre, :apellido, :correo, :telefono, :tipo_usuario, :clave)";
        
        $stmt_ins = $db->prepare($query_insert);
        $stmt_ins->execute([
            ':nombre'       => $nombre,
            ':apellido'     => $apellido,
            ':correo'       => $correo,
            ':telefono'     => $telefono,
            ':tipo_usuario' => $tipo_usuario,
            ':clave'        => $clave
        ]);

        if ($origen === 'publico') {
            header("Location: ../views/registro_publico.php?msg=success_registro");
        } else {
            header("Location: ../views/gestion_usuarios.php?msg=success_create");
        }
        exit();

    } catch (PDOException $e) {
        header("Location: ../views/" . ($origen === 'publico' ? "registro_publico.php" : "gestion_usuarios.php") . "?msg=error_bd");
        exit();
    }
}
?>