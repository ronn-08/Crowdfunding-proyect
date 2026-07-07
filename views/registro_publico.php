<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta Ciudadana | Crowdfunding UG</title>
    <!-- Bootstrap 5, Iconos y Tipografía Inter -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f1f5f9; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
        }
        .card-register { 
            border: none; 
            border-radius: 1.5rem; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);
        }
        .form-label { 
            font-weight: 600; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            color: #64748b; 
            letter-spacing: 0.5px; 
        }
        .form-control, .form-select { 
            border-radius: 0.75rem; 
            padding: 0.75rem 1rem; 
            border: 1px solid #cbd5e1; 
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .role-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .role-option:hover {
            border-color: #3b82f6 !important;
            background-color: #eff6ff;
        }
    </style>
</head>
<body>

    <!-- 1. BARRA DE NAVEGACIÓN GLOBAL -->
    <?php include_once __DIR__ . '/../components/header.php'; ?>

    <!-- CONTENEDOR PRINCIPAL DEL FORMULARIO -->
    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 650px;">
            
            <!-- BOTÓN VOLVER AL INICIO ESTILIZADO -->
            <div class="mb-3">
                <a href="../index.php" class="btn btn-white bg-white border border-light-subtle rounded-3 px-3 py-2 btn-sm fw-semibold text-secondary shadow-sm">
                    <i class="bi bi-arrow-left me-1 text-primary fw-bold"></i> Volver al Inicio
                </a>
            </div>

		<!-- CONTROL DE ALERTAS DINÁMICAS -->
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'success_registro'): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>¡Registro Completado con Éxito!</strong> Tu cuenta ha sido creada. Ya puedes participar activamente en la plataforma.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'error_campos'): ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Campos Incompletos:</strong> Por favor, llena todos los datos obligatorios del formulario.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
            <!-- TARJETA CONTENEDORA -->
            <div class="card card-register p-4 p-md-5 bg-white">
                
                <!-- Encabezado del Formulario -->
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-dark m-0">Únete a la Comunidad</h3>
                    <p class="text-muted small m-0 mt-1">Regístrate para financiar innovación o lanzar tus propias causas.</p>
                </div>

                <!-- FORMULARIO DE RECOLECCIÓN (Apunta a tu controlador existente) -->
                <!-- Nota: Incluimos un campo oculto 'accion' por si tu CRUD lo usa para identificar la ruta -->
                <form action="../controllers/usuario_crud.php" method="POST">
    <!-- Marcadores de control para el backend -->
    <input type="hidden" name="accion" value="crear">
    <input type="hidden" name="origen" value="publico">

                    <div class="row g-3 mb-4">
                        <!-- Campo: Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombres Completos</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Abril Eliza" required>
                        </div>
                        
                        <!-- Campo: Apellido -->
                        <div class="col-md-6">
                            <label class="form-label">Apellidos Completos</label>
                            <input type="text" class="form-control" name="apellido" placeholder="Ej: Pluas Viteri" required>
                        </div>

                        <!-- Campo: Correo Electrónico -->
                        <div class="col-12">
                            <label class="form-label">Correo Institucional / Ciudadano</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" name="correo" placeholder="ejemplo@ug.edu.ec" required>
                            </div>
                        </div>

                        <!-- Campo: Teléfono -->
                        <div class="col-12">
                            <label class="form-label">Número Celular de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control border-start-0" name="telefono" placeholder="Ej: 0999999999" required>
                            </div>
                        </div>
			<div class="col-12">
    <label class="form-label">Contraseña de Acceso</label>
    <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-key"></i></span>
        <input type="password" class="form-control border-start-0" name="clave" placeholder="Crea una contraseña segura" required>
    </div>
</div>
                        <!-- SELECCIÓN CRÍTICA DE ROL PÚBLICO -->
                        <div class="col-12">
                            <label class="form-label d-block mb-2">¿Cómo deseas participar en la plataforma?</label>
                            <select class="form-select fs-6 fw-medium text-dark py-2.5" name="tipo_usuario" required>
                                <option value="" disabled selected hidden>Selecciona tu perfil de acceso...</option>
                                <option value="Donante">Donante (Deseo apoyar económicamente a las causas)</option>
                                <option value="Emprendedor">Emprendedor (Deseo postular y gestionar mis proyectos)</option>
                            </select>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-fill-info text-primary"></i> Por políticas de auditoría de la UG, el rol de Administrador no está disponible en el registro público.
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 shadow-sm text-uppercase font-monospace tracking-wide" style="font-size: 0.95rem;">
                        <i class="bi bi-check-circle-fill me-2"></i> Finalizar Mi Registro
                    </button>
                </form>

                <!-- Vínculo Alterno -->
                <div class="text-center mt-4 pt-3 border-top border-light">
                    <p class="small text-muted m-0">¿Ya tienes una cuenta? <a href="./login.php" class="fw-semibold text-primary text-decoration-none">Iniciar Sesión</a></p>
                </div>

            </div>
        </div>
    </main>

    <!-- 2. PIE DE PÁGINA GLOBAL -->
    <?php include_once __DIR__ . '/../components/footer.php'; ?>

</body>
</html>