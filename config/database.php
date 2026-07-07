<?php

//Configuración de conexión a la BD

class Database {

    private $host = "localhost";
    private $db_name = "bd_crowdfunding";
    private $username = "root";
    private $password = "rapn0821";
    private $charset = "utf8mb4";
    public $conn;


    public function getConnection() {
        $this->conn = null;

        try {
            //Estructura del DSN (Data Source Name)
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
	    //Configuraciones avanzadas de PDO
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
                PDO::ATTR_EMULATE_PREPARES   => false,                  
            ];
	    
	    //Crear la instancia de conexión
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $exception) {

            die("Error crítico de conexión: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>