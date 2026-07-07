<?php
//Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/database.php';

//Instanciar la base de datos y obtener la conexión
$database = new Database();
$db = $database->getConnection();


//ACCIÓN LEER REGISTROS (READ)

function obtenerCategorias($db) {

    $query = "SELECT Id_Categoria, Nombre, Descripcion FROM CATEGORIA ORDER BY Id_Categoria DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(); 
}


//DETECTAR ACCIONES ENVIADAS POR FORMULARIOS (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //ACCIÓN CREAR REGISTRO (CREATE)
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

	//Validación: que el nombre no vaya vacío
        if (!empty($nombre)) {
            
	    //Usamos marcadores (:nombre, :descripcion) para evitar Inyección SQL
            $query = "INSERT INTO CATEGORIA (Nombre, Descripcion) VALUES (:nombre, :descripcion)";
            $stmt = $db->prepare($query);

	    //Vincular los datos reales a los marcadores de forma segura
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
		//Redireccionar de vuelta a la vista con un mensaje de éxito
                header("Location: ../views/gestion_categorias.php?msg=success_create");
                exit();
            }
        }
        header("Location: ../views/gestion_categorias.php?msg=error");
        exit();
    }

    // ACCIÓN ACTUALIZAR REGISTRO (UPDATE)
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id_categoria'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        if (!empty($id) && !empty($nombre)) {
            $query = "UPDATE CATEGORIA SET Nombre = :nombre, Descripcion = :descripcion WHERE Id_Categoria = :id";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: ../views/gestion_categorias.php?msg=success_update");
                exit();
            }
        }
        header("Location: ../views/gestion_categorias.php?msg=error");
        exit();
    }
}


//DETECTARACCIONES ENVIADAS POR URL (GET)

//ACCIÓN ELIMINAR REGISTRO (DELETE)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];

    if (!empty($id)) {
        $query = "DELETE FROM CATEGORIA WHERE Id_Categoria = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        try {
            if ($stmt->execute()) {
                header("Location: ../views/gestion_categorias.php?msg=success_delete");
                exit();
            }
        } catch (PDOException $e) {
            //Si la categoría está siendo usada por una Causa Social, 
            //saltará la restricción ON DELETE RESTRICT que pusimos en la BD.
            header("Location: ../views/gestion_categorias.php?msg=error_constraint");
            exit();
        }
    }
}
?>