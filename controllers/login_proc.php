<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: ../views/login.php?msg=error_vacios");
        exit();
    }

    // 1. FILTRO DE ACCESO: ADMINISTRADOR
    $admin_email = "admin@crowdfunding.ug";
    $admin_pass  = "rapn0821";

    if ($email === $admin_email && $password === $admin_pass) {
        $_SESSION['usuario_id']   = 0;
        $_SESSION['usuario_nom']  = "Administrador";
        $_SESSION['usuario_rol']  = "Admin";
        header("Location: ../views/gestion_categorias.php");
        exit();
    }

    // 2. FILTRO DE ACCESO: CIUDADANOS COMUNES CON VALIDACIÓN DE CLAVE REAL
    try {
        // Buscamos al usuario por correo electrónico e incluimos la Clave en el SELECT
        $query = "SELECT Id_Usuario, Nombre, Tipo_Usuario, Clave FROM USUARIO WHERE Correo = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comparamos que el usuario exista Y que la clave ingresada sea idéntica a la de la BD
        if ($user && $user['Clave'] === $password) {
            $_SESSION['usuario_id']  = $user['Id_Usuario'];
            $_SESSION['usuario_nom'] = $user['Nombre'];
            $_SESSION['usuario_rol'] = $user['Tipo_Usuario'];
            
            header("Location: ../index.php?msg=welcome");
            exit();
        } else {
            // Si el correo no existe o la contraseña no coincide, rebota
            header("Location: ../views/login.php?msg=error_credenciales");
            exit();
        }

    } catch (PDOException $e) {
        header("Location: ../views/login.php?msg=error_bd");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>