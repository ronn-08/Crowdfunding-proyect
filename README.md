# 🚀 Crowdfunding UG - Plataforma de Financiamiento Colectivo

Ecosistema web de nivel comercial diseñado para la gestión, publicación y financiamiento colectivo transparente de proyectos de innovación, causas sociales y emprendimientos tecnológicos dentro de la comunidad de la Universidad de Guayaquil.

La aplicación cuenta con una arquitectura desacoplada basada en controladores lógicos en PHP, una interfaz responsiva desarrollada con Bootstrap 5 y un motor transaccional relacional implementado en MySQL bajo cumplimiento de las propiedades ACID.

---

# 📋 Tabla de Contenidos

- [Características](#-características)
- [Tecnologías Utilizadas](#-tecnologías-utilizadas)
- [Requisitos Previos](#-requisitos-previos-del-sistema)
- [Instalación del Entorno](#-1-instalación-del-entorno-servidor-localhost)
- [Configuración del Proyecto](#-2-configuración-del-proyecto-y-base-de-datos)
- [Ejecución](#-3-despliegue-y-ejecución-de-la-aplicación)
- [Credenciales de Administrador](#-credenciales-maestras-de-acceso-administrador-unificado)
- [Resolución de Problemas](#-4-resolución-de-errores-comunes-troubleshooting)

---

# ✨ Características

- 👥 Registro e inicio de sesión de usuarios.
- 🚀 Publicación de campañas de crowdfunding.
- 💰 Sistema de aportes económicos.
- 📊 Panel administrativo.
- 📈 Estadísticas generales de la plataforma.
- 📱 Diseño responsive con Bootstrap 5.
- 🔒 Persistencia mediante MySQL y PDO.
- ⚙️ Arquitectura basada en MVC ligero utilizando PHP.

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

# ⚙️ 2. Configuración del Proyecto y Base de Datos

## Descargar el proyecto

Clone el repositorio o descárguelo como ZIP.

---

## Crear la Base de Datos

Ejecute:

```sql
CREATE DATABASE bd_crowdfunding
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Después importe el archivo:

```
bd_crowdfunding.sql
```

---

## Configurar la conexión

Abra:

```
config/database.php
```

Modifique los parámetros según su instalación:

```php
private $host = "localhost";
private $db_name = "bd_crowdfunding";
private $username = "root";
private $password = "TU_CONTRASEÑA_DE_MYSQL";
```

> Si utiliza XAMPP por defecto, deje la contraseña vacía:

```php
private $password = "";
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

## Error

```
"php" no se reconoce como un comando interno o externo
```

### Causa

El PATH de Windows no fue configurado correctamente.

### Solución

Verifique que exista:

```
C:\php
```

en las variables de entorno.

Si utiliza XAMPP también puede iniciar el servidor mediante:

```bash
C:\xampp\php\php.exe -S localhost:8000
```

---

## Error

```
Fatal error:
Uncaught PDOException:
Could not find driver
```

### Causa

La extensión PDO MySQL no está habilitada.

### Solución

Edite:

```
C:\php\php.ini
```

y habilite:

```ini
extension=pdo_mysql
```

Después reinicie el servidor.

---

## Error

```
Conexión rechazada
```

o

```
Fallo de login
```

### Causa

MySQL no está iniciado o las credenciales son incorrectas.

### Solución

- Verifique que MySQL esté ejecutándose.
- Revise el puerto utilizado.
- Compruebe que la contraseña del usuario **root** coincida con la configurada en:

```
config/database.php
```

---

# 📄 Licencia

Este proyecto fue desarrollado con fines académicos para la **Universidad de Guayaquil**.
