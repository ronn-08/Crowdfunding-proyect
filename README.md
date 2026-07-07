# 🚀 Crowdfunding UG - Plataforma de Financiamiento Colectivo

Ecosistema web de nivel comercial diseñado para la gestión, publicación y financiamiento colectivo transparente de proyectos de innovación, causas sociales y emprendimientos tecnológicos dentro de la comunidad de la Universidad de Guayaquil.

La aplicación cuenta con una arquitectura desacoplada basada en controladores lógicos en PHP, una interfaz responsiva desarrollada con Bootstrap 5 y un motor transaccional relacional implementado en MySQL bajo cumplimiento de las propiedades ACID.

---


# ✨ Características

- Registro e inicio de sesión de usuarios.
- Publicación de campañas de crowdfunding.
- Sistema de aportes económicos.
- Panel administrativo.
- Estadísticas generales de la plataforma.
- Diseño responsive con Bootstrap 5.
- Persistencia mediante MySQL y PDO.
- Arquitectura basada en MVC ligero utilizando PHP.

---

# 🧰 Tecnologías Utilizadas

- PHP 8
- MySQL 8
- Bootstrap 5
- HTML5
- CSS3
- JavaScript
- PDO
- Font Awesome

---

# 🛠️ Requisitos Previos del Sistema

Para ejecutar esta aplicación en un entorno local (`localhost`), la computadora del usuario debe contar con las siguientes herramientas básicas:

- **PHP:** versión **8.1** o superior.
- **MySQL Server:** versión **8.0** o superior (o MariaDB equivalente).

---

# 📦 1. Instalación del Entorno Servidor (Localhost)

El usuario puede elegir cualquiera de los siguientes métodos para levantar el entorno de ejecución.

## Método A: Instalación Automatizada (Recomendado)

1. Descargue e instale **XAMPP** desde su sitio oficial.
2. Siga el asistente de instalación dejando las opciones por defecto.
3. Abra el **XAMPP Control Panel**.
4. Inicie el servicio **MySQL** presionando **Start**.

---

## Método B: Instalación Manual (Sin XAMPP)

### 1. Descargar PHP

Descargue la versión **VS16 x64 Thread Safe** desde:

```
https://windows.php.net/download/
```

### 2. Extraer PHP

Cree la carpeta:

```
C:\php
```

Extraiga allí todo el contenido del archivo ZIP.

### 3. Configurar el PATH

Abra:

```
Variables de entorno
```

Después:

- Variables del sistema
- Path
- Editar
- Nuevo

Agregue:

```
C:\php
```

Guarde todos los cambios.

### 4. Habilitar PDO MySQL

Dentro de `C:\php`:

- Copie

```
php.ini-development
```

como

```
php.ini
```

Abra el archivo y habilite:

```ini
extension_dir = "ext"
extension=pdo_mysql
```

### 5. Instalar MySQL

Descargue e instale:

- MySQL Community Server

Configure una contraseña para el usuario **root**.

---

## ⚙️ 2. Configuración del Proyecto y Base de Datos

Una vez instalado el entorno, siga estos pasos para inicializar la aplicación y la persistencia de datos:

1. **Descarga del Proyecto:** Descargue este repositorio en formato ZIP o clónelo, y extráigalo por completo en una carpeta local de su elección.
2. **Importación de la Base de Datos:**
   * Abra su gestor de bases de datos preferido (phpMyAdmin, MySQL Workbench o consola CMD).
   * Cree una nueva base de datos vacía ejecutando el comando:
     ```sql
     CREATE DATABASE bd_crowdfunding CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   * Importe el archivo estructural de tablas e historial de pruebas ubicado en la raíz del proyecto llamado **`bd_crowdfunding.sql`**.
3. **Puente de Conexión en PHP:**
   * Abra el archivo **`config/database.php`** en su editor de código.
   * Modifique las credenciales de conexión para que coincidan con los parámetros de su motor MySQL local:
     ```php
     private $host = "localhost";
     private $db_name = "bd_crowdfunding";
     private $username = "root";
     private $password = "TU_CONTRASEÑA_DE_MYSQL"; // Dejar vacío "" si usa XAMPP por defecto
     ```

---

# 🏃‍♂️ 3. Despliegue y Ejecución de la Aplicación

Abra una terminal dentro de la carpeta del proyecto y ejecute:

```bash
php -S localhost:8000
```

Luego abra el navegador e ingrese a:

```
http://localhost:8000
```

---

# 🔑 Credenciales Maestras de Acceso (Administrador Unificado)

El botón **Iniciar Sesión** sirve tanto para usuarios normales como para administradores.

Si se ingresan las siguientes credenciales, automáticamente se abrirá el panel administrativo.

**Correo**

```
admin@crowdfunding.ug
```

**Contraseña**

```
rapn0821
```

---


# 🚨 4. Resolución de Errores Comunes (Troubleshooting)

## ❌ Error: "php" no se reconoce como un comando interno o externo

**Causa:** Las variables de entorno de Windows no se cargaron correctamente o la ruta en el PATH tiene un error tipográfico.

**Solución:** Cierre todas las ventanas del CMD, verifique que los archivos estén en `C:\php` y abra una nueva terminal para forzar la lectura del PATH actualizado. Alternativamente, ejecute el comando usando la ruta absoluta de su instalación:

```bash
C:\xampp\php\php.exe -S localhost:8000
```

---

## ❌ Error: Fatal error: Uncaught PDOException: Could not find driver

**Causa:** El módulo encargado de comunicar a PHP con el servidor MySQL no ha sido habilitado dentro de la configuración del sistema.

**Solución:** Abra el archivo `C:\php\php.ini`, asegúrese de haber removido el punto y coma (`;`) al inicio de la línea:

```ini
extension=pdo_mysql
```

y reinicie el servidor de la terminal.

---

## ❌ Error: Conexión rechazada o fallo de login al intentar registrarse

**Causa:** El servicio local de MySQL Server se encuentra apagado o las credenciales mapeadas en el script `config/database.php` son incorrectas.

# 🏗️ Arquitectura Técnica del Proyecto

La aplicación sigue una arquitectura modular basada en la separación de responsabilidades, facilitando el mantenimiento, la escalabilidad y la organización del código fuente.

```text
crowdfunding_proyecto/
│
├── index.php
│   └── Página de inicio y catálogo público dinámico.
│
├── components/
│   ├── header.php
│   │   └── Barra de navegación superior con inicio de sesión dinámico.
│   │
│   ├── footer.php
│   │   └── Pie de página institucional de la Universidad de Guayaquil.
│   │
│   └── admin_nav.php
│       └── Submenú dinámico para el panel administrativo.
│
├── config/
│   ├── database.php
│   │   └── Clase de conexión segura a MySQL mediante PDO.
│   │
│   └── proyectoBD.sql
│       └── Script de creación de la base de datos, tablas y datos iniciales.
│
├── controllers/
│   ├── login_proc.php
│   │   └── Procesamiento del inicio de sesión y control de roles.
│   │
│   ├── logout.php
│   │   └── Cierre seguro de sesiones.
│   │
│   └── usuario_crud.php
│       └── Lógica de las operaciones CRUD de usuarios.
│
├── views/
│   ├── login.php
│   │   └── Portal de autenticación.
│   │
│   ├── registro_publico.php
│   │   └── Registro de nuevos usuarios.
│   │
│   ├── crear_campana.php
│   │   └── Creación y publicación de campañas de crowdfunding.
│   │
│   ├── realizar_donacion.php
│   │   └── Interfaz para realizar donaciones.
│   │
│   ├── gestion_usuarios.php
│   │   └── Administración del padrón de usuarios.
│   │
│   ├── gestion_categorias.php
│   │   └── Administración de categorías del sistema.
│   │
│   ├── gestion_metodos_pago.php
│   │   └── Administración de métodos de pago.
│   │
│   └── dashboard_reportes.php
│       └── Panel administrativo con reportes y consultas de negocio.
│
└── uploads/
    └── evidencias/
        └── Almacenamiento de imágenes cargadas por los emprendedores.
```

## 📂 Organización de Directorios

| Directorio | Descripción |
|------------|-------------|
| **components/** | Componentes reutilizables de la interfaz gráfica. |
| **config/** | Configuración de la base de datos y scripts SQL. |
| **controllers/** | Lógica de negocio y procesamiento de solicitudes. |
| **views/** | Interfaces de usuario y paneles del sistema. |
| **uploads/** | Almacenamiento de archivos e imágenes cargadas por los usuarios. |
| **index.php** | Punto de entrada principal de la aplicación. |

**Solución:** Asegúrese de que el puerto de MySQL esté activo en su computadora y revise que la contraseña de su usuario `root` coincida exactamente con la declarada en el archivo del puente de conexión.

# 📄 Licencia

Este proyecto fue desarrollado con fines académicos para la **Universidad de Guayaquil**.
