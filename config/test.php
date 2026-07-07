<?php
require_once "config/database.php";

$db = new Database();
$connection = $db->getConnection();

if ($connection) {
    echo "Conexión exitosa a bd_crowdfunding! El puente de datos funciona perfectamente";
}
?>