<?php
//1. Integración con el Controlador (Lógica de negocio y BD)
require_once __DIR__ . '/../controllers/usuario_crud.php';

//2. Obtener los datos para la tabla
$usuarios = obtenerUsuarios($db);

//3. Configuración de Alertas Dinámicas
$alertConfig = [
    'success_create'   => ['class' => 'success', 'icon' => 'bi-check-circle-fill', 'msg' => '¡Usuario registrado exitosamente en la plataforma!'],
    'success_update'   => ['class' => 'info',    'icon' => 'bi-info-circle-fill',  'msg' => '¡Datos de usuario actualizados correctamente!'],
    'success_delete'   => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'msg' => '¡Usuario removido del sistema!'],
    'success_eliminar' => ['class' => 'success', 'icon' => 'bi-trash-fill', 'msg' => '¡Usuario removido de la base de datos de forma permanente!'], // ◄ NUEVA
    'error_duplicate'  => ['class' => 'danger',  'icon' => 'bi-x-circle-fill',      'msg' => 'El correo electrónico ingresado ya se encuentra registrado.'],
    'error_constraint' => ['class' => 'danger',  'icon' => 'bi-shield-lock-fill',  'msg' => 'No se puede eliminar este usuario porque posee transacciones o proyectos asociados.'],
    'error_restriccion'=> ['class' => 'danger',  'icon' => 'bi-shield-exclamation-fill', 'msg' => 'No se puede eliminar: Este usuario posee campañas activas o registros de donaciones vinculados.'], // ◄ NUEVA
    'error'            => ['class' => 'danger',  'icon' => 'bi-bug-fill',          'msg' => 'Ocurrió un error inesperado al procesar la solicitud.']
];

$currentAlert = (isset($_GET['msg']) && array_key_exists($_GET['msg'], $alertConfig)) ? $alertConfig[$_GET['msg']] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | Crowdfunding System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 12px; }
        .shadow-sm-custom { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .shadow-lg-custom { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1); }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #495057; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-role { font-weight: 600; padding: 0.5em 0.8em; border-radius: 6px; }
        .btn-edit { color: #0d6efd; border-color: #0d6efd; }
        .btn-edit:hover { background-color: #0d6efd; color: white; }
        .sticky-form { position: sticky; top: 2rem; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<?php include_once __DIR__ . '/../components/admin_nav.php'; ?>

<div class="container py-5">
    <!-- Encabezado de Página -->
    <div class="row mb-5">
        <div class="col-12 text-center text-lg-start">
            <h1 class="fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Gestión de Usuarios</h1>
            <p class="text-muted">Administra los roles, accesos y datos de la comunidad de crowdfunding.</p>
        </div>
    </div>

    <!-- Sección de Alertas -->
    <?php if ($currentAlert): ?>
        <div class="alert alert-<?= $currentAlert['class'] ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi <?= $currentAlert['icon'] ?> me-2"></i> <?= $currentAlert['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna Izquierda: Formulario (Card) -->
        <div class="col-lg-4">
            <div class="card shadow-sm-custom sticky-form">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold text-primary" id="formHeader">Registrar Usuario</h5>
                </div>
                <div class="card-body pt-0">
                    <form id="userForm" action="../controllers/usuario_crud.php" method="POST">
                        <!-- Inputs Ocultos -->
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id_usuario" id="userId">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required placeholder="Ej: Juan">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido" id="apellido" required placeholder="Ej: Pérez">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo" required placeholder="juan.perez@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="Ej: +593 999 999 999">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipo de Usuario (Rol)</label>
                            <select class="form-select" name="tipo_usuario" id="tipo_usuario" required>
                                <option value="" selected disabled>Seleccionar rol...</option>
                                <option value="Donante">Donante</option>
                                <option value="Emprendedor">Emprendedor</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSubmit" class="btn btn-primary fw-bold py-2">
                                <i class="bi bi-person-plus-fill me-2"></i>Registrar Usuario
                            </button>
                            <button type="button" id="btnCancel" class="btn btn-outline-secondary d-none" onclick="resetForm()">
                                Cancelar Edición
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Tabla Maestra -->
        <div class="col-lg-8">
            <div class="card shadow-sm-custom overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">Usuarios Registrados</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Nombre Completo</th>
                                <th>Contacto</th>
                                <th>Rol</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($usuarios) > 0): ?>
                                <?php foreach ($usuarios as $u): 
                                    // Lógica de color de badges
                                    $badgeClass = 'bg-secondary';
                                    if ($u['Tipo_Usuario'] == 'Administrador') $badgeClass = 'bg-primary';
                                    elseif ($u['Tipo_Usuario'] == 'Emprendedor') $badgeClass = 'bg-success';
                                    elseif ($u['Tipo_Usuario'] == 'Donante') $badgeClass = 'bg-info text-dark';
                                ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($u['Nombre'] . ' ' . $u['Apellido']) ?></div>
                                            <small class="text-muted">ID: USR-<?= str_pad($u['Id_Usuario'], 5, '0', STR_PAD_LEFT) ?></small>
                                        </td>
                                        <td>
                                            <div class="small"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($u['Correo']) ?></div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($u['Telefono'] ?? 'N/A') ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-role <?= $badgeClass ?>"><?= $u['Tipo_Usuario'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm border rounded">
                                                <button type="button" 
                                                        class="btn btn-white border-0 btn-sm text-primary" 
                                                        onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)"
                                                        title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <a href="../controllers/usuario_crud.php?action=delete&id=<?= $u['Id_Usuario'] ?>" 
                                                   class="btn btn-white border-0 btn-sm text-danger" 
                                                   onclick="return confirm('¿Está seguro de eliminar permanentemente a <?= addslashes($u['Nombre']) ?>?');"
                                                   title="Eliminar">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-person-x display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No se encontraron usuarios registrados.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts Interactividad JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Llena el formulario con los datos del usuario seleccionado para editar
     */
    function editUser(user) {
        // Cambiar estados del UI
        document.getElementById('formHeader').innerText = 'Actualizar Datos de Usuario';
        document.getElementById('formHeader').classList.replace('text-primary', 'text-info');
        
        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Actualizar Usuario';
        document.getElementById('btnSubmit').classList.replace('btn-primary', 'btn-info');
        document.getElementById('btnSubmit').classList.add('text-white');
        
        document.getElementById('btnCancel').classList.remove('d-none');

        // Llenar campos
        document.getElementById('formAction').value = 'update';
        document.getElementById('userId').value = user.Id_Usuario;
        document.getElementById('nombre').value = user.Nombre;
        document.getElementById('apellido').value = user.Apellido;
        document.getElementById('correo').value = user.Correo;
        document.getElementById('telefono').value = user.Telefono;
        document.getElementById('tipo_usuario').value = user.Tipo_Usuario;

        // Foco y Scroll
        document.getElementById('nombre').focus();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * Reinicia el formulario al modo "Crear"
     */
    function resetForm() {
        document.getElementById('formHeader').innerText = 'Registrar Usuario';
        document.getElementById('formHeader').classList.replace('text-info', 'text-primary');

        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Registrar Usuario';
        document.getElementById('btnSubmit').className = 'btn btn-primary fw-bold py-2';
        
        document.getElementById('btnCancel').classList.add('d-none');
        
        document.getElementById('formAction').value = 'create';
        document.getElementById('userId').value = '';
        document.getElementById('userForm').reset();
    }
</script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>