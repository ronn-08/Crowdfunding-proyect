# Crowdfunding-proyect
🚀 Crowdfunding UG - Plataforma de Financiamiento Colectivo
Ecosistema web integral diseñado para la gestión, publicación y financiamiento colectivo transparente de proyectos de innovación, causas sociales y emprendimientos tecnológicos dentro de la comunidad de la Universidad de Guayaquil. La aplicación cuenta con una arquitectura desacoplada basada en controladores lógicos en PHP, diseño responsivo comercial con Bootstrap 5 y un motor transaccional relacional bajo cumplimiento ACID en MySQL.

🛠️ Requisitos Previos del Sistema
Para ejecutar esta aplicación en un entorno local (localhost), la computadora del usuario debe contar con los siguientes elementos instalados y configurados:

-Intérprete de PHP: Versión 8.1 o superior.

-Motor de Base de Datos: MySQL Server 8.0 o superior (o MariaDB equivalente).

📦 Parte 1: Instalación del Entorno Servidor (Localhost)
El usuario puede elegir cualquiera de los siguientes dos métodos para levantar los motores de ejecución en su sistema operativo Windows:

Método A: Instalación Automatizada (Recomendado para usuarios rápidos)
1. Descargue e instale XAMPP desde su sitio web oficial.

2. Siga el asistente de instalación interactivo dejando todas las opciones por defecto.

3. Abra el XAMPP Control Panel y encienda el módulo de MySQL haciendo clic en el botón "Start".

Método B: Instalación Purista Manual (Sin suites de terceros)
Si prefiere configurar las herramientas de forma independiente, ejecute el siguiente procedimiento:

Descarga de PHP: Descargue el paquete ZIP de PHP (versión VS16 x64 Thread Safe) desde windows.php.net/download.

Extracción: Cree una carpeta raíz en el disco local C llamada php (C:\php) y extraiga todo el contenido del ZIP en dicha ruta.

Configuración de Variables de Entorno (PATH):

En el buscador de Windows escriba "Variables de entorno" y abra la configuración del sistema.

Haga clic en el botón "Variables de entorno...".

En la sección Variables del sistema, localice la variable Path, selecciónela y haga clic en "Editar".

Haga clic en "Nuevo" y añada la ruta exacta: C:\php. Guarde presionando "Aceptar" en todas las ventanas.

Activación de Extensiones de Base de Datos (php.ini):

Vaya a C:\php, localice el archivo php.ini-development y saque una copia. Nombre a la copia únicamente como php.ini.

Abra el archivo php.ini con un editor de texto plano y retire el punto y coma (;) del inicio de las siguientes líneas:

Ini, TOML
extension_dir = "ext"
extension=pdo_mysql
Motor de Base de Datos: Descargue e instale de forma independiente MySQL Community Server desde la web de Oracle y configure una contraseña para el usuario administrador root.

⚙️ Parte 2: Configuración del Proyecto y Base de Datos
Una vez instalado el entorno, siga estos pasos para inicializar la aplicación y la persistencia de datos:

Descarga del Proyecto: Descargue el repositorio en formato ZIP desde GitHub y extráigalo por completo en una carpeta de su elección (ejemplo: C:\Users\Usuario\Documents\crowdfunding_proyecto\).

Importación de la Base de Datos:

Abra su gestor de bases de datos preferido (phpMyAdmin, MySQL Workbench o consola CMD).

Cree una nueva base de datos vacía ejecutando el comando:

SQL
CREATE DATABASE bd_crowdfunding CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
Importe el archivo estructural de tablas e historial de pruebas ubicado en la raíz del proyecto llamado bd_crowdfunding.sql.

Puente de Conexión en PHP:

Abra el archivo config/database.php en su editor de código.

Modifique las credenciales de conexión para que coincidan con los parámetros de su motor MySQL local:

PHP
private $host = "localhost";
private $db_name = "bd_crowdfunding";
private $username = "root";
private $password = "TU_CONTRASEÑA_DE_MYSQL"; // Dejar vacío "" si usa XAMPP por defecto
🏃‍♂️ Parte 3: Despliegue y Ejecución de la Aplicación
Para encender la plataforma web y empezar a interactuar con el sistema de crowdfunding, realice lo siguiente:

Abra una terminal de comandos (CMD o PowerShell) directamente dentro de la carpeta raíz del proyecto descomprimido.

Inicie el servidor web embebido nativo de PHP ejecutando la instrucción estricta:

Bash
php -S localhost:8000
Abra su navegador web e ingrese a la siguiente dirección URL:

Plaintext
http://localhost:8000
🔑 Credenciales Maestras de Auditoría (Acceso Administrativo)
Para ingresar a las pantallas de control maestro (Gestión de Categorías, Métodos de Pago, Lista de Usuarios y Dashboard Analítico de 5 Consultas), haga clic en el botón "Iniciar Sesión" en la barra superior e introduzca los siguientes datos quemados en el backend securizado:

Correo Electrónico: admin@crowdfunding.ug

Contraseña de Acceso: rapn0821

🚨 Parte 4: Resolución de Errores Comunes (Troubleshooting)
Error: "php" no se reconoce como un comando interno o externo

Causa: Las variables de entorno de Windows no se cargaron o la ruta en el PATH está mal escrita.

Solución: Cierre todas las ventanas del CMD, verifique que los archivos estén en C:\php y abra una nueva terminal para forzar la lectura del PATH. Alternativamente, ejecute el comando usando la ruta absoluta de su instalación: C:\xampp\php\php.exe -S localhost:8000.

Error: Fatal error: Uncaught PDOException: Could not find driver

Causa: El módulo encargado de comunicar a PHP con la base de datos MySQL no ha sido habilitado dentro de los archivos del sistema.

Solución: Abra el archivo C:\php\php.ini, asegúrese de haber removido el punto y coma (;) al inicio de extension=pdo_mysql y reinicie el servidor de la terminal.

Error: Conexión rechazada o fallo de login al intentar registrarse

Causa: Las credenciales del archivo config/database.php no coinciden con las del motor local de MySQL Server o el servicio de base de datos se encuentra apagado.

Solución: Asegúrese de que el puerto de MySQL esté activo en su computadora y revise que la contraseña de su usuario root esté bien digitada en el script del puente de conexión.


