<?php
// 1. Integración con el Controlador
require_once __DIR__ . '/../controllers/categoria_crud.php';

// 2. Obtener los datos para la tabla
$categorias = obtenerCategorias($db);

// 3. Lógica de Alertas Dinámicas
$alertConfig = [
    'success_create'   => ['class' => 'success', 'msg' => '¡Categoría creada exitosamente!'],
    'success_update'   => ['class' => 'info', 'msg' => '¡Categoría actualizada correctamente!'],
    'success_delete'   => ['class' => 'warning', 'msg' => '¡Categoría eliminada del sistema!'],
    'error_constraint' => ['class' => 'danger', 'msg' => 'No se puede eliminar esta categoría porque contiene campañas activas.'],
    'error'            => ['class' => 'danger', 'msg' => 'Ha ocurrido un error inesperado.'],
];

$currentAlert = null;
if (isset($_GET['msg']) && array_key_exists($_GET['msg'], $alertConfig)) {
    $currentAlert = $alertConfig[$_GET['msg']];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías | Panel Administrativo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --bs-primary: #4e73df; }
        body { background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 0.75rem; }
        .shadow-custom { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1) !important; }
        .table thead { background-color: #f1f4f9; }
        .btn-action { transition: transform 0.2s; }
        .btn-action:hover { transform: scale(1.1); }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<?php include_once __DIR__ . '/../components/admin_nav.php'; ?>
<div class="container py-5">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark"><i class="bi bi-tags-fill me-2"></i>Gestión de Categorías</h2>
            <p class="text-muted">Administra las categorías de proyectos para el sistema de crowdfunding.</p>
        </div>
    </div>

    <!-- Alertas -->
    <?php if ($currentAlert): ?>
        <div class="alert alert-<?= $currentAlert['class'] ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> <?= $currentAlert['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna Izquierda: Formulario -->
        <div class="col-lg-4">
            <div class="card shadow-custom sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-primary" id="formTitle">Crear Nueva Categoría</h5>
                </div>
                <div class="card-body">
                    <form id="categoriaForm" action="../controllers/categoria_crud.php" method="POST">
                        <!-- Campos Ocultos -->
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id_categoria" id="categoriaId">

                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-bold text-uppercase">Nombre de la Categoría</label>
                            <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" placeholder="Ej: Tecnología" required>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label small fw-bold text-uppercase">Descripción (Opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Breve descripción del tipo de proyectos..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>Guardar Categoría
                            </button>
                            <button type="button" id="btnCancel" class="btn btn-light border d-none" onclick="resetForm()">
                                Cancelar Edición
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Tabla -->
        <div class="col-lg-8">
            <div class="card shadow-custom">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">Categorías Registradas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 border-0">ID</th>
                                    <th class="py-3 border-0">Nombre</th>
                                    <th class="py-3 border-0">Descripción</th>
                                    <th class="py-3 border-0 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($categorias) > 0): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <tr>
                                            <td class="px-4 text-muted fw-bold">#<?= $cat['Id_Categoria'] ?></td>
                                            <td><span class="fw-bold text-dark"><?= htmlspecialchars($cat['Nombre']) ?></span></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= !empty($cat['Descripcion']) ? htmlspecialchars($cat['Descripcion']) : '<em>Sin descripción</em>' ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group shadow-sm">
                                                    <!-- Botón Editar -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary btn-action" 
                                                            title="Editar"
                                                            onclick="editCategory(<?= $cat['Id_Categoria'] ?>, '<?= addslashes($cat['Nombre']) ?>', '<?= addslashes($cat['Descripcion']) ?>')">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    
                                                    <!-- Botón Eliminar -->
                                                    <a href="../controllers/categoria_crud.php?action=delete&id=<?= $cat['Id_Categoria'] ?>" 
                                                       class="btn btn-sm btn-outline-danger btn-action" 
                                                       title="Eliminar"
                                                       onclick="return confirm('¿Seguro que deseas eliminar la categoría: <?= addslashes($cat['Nombre']) ?>?');">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x display-4 d-block mb-3"></i>
                                            No hay categorías registradas aún.
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
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Prepara el formulario para editar una categoría existente
     */
    function editCategory(id, nombre, descripcion) {
        // 1. Cambiar textos del UI
        document.getElementById('formTitle').innerText = 'Actualizar Categoría';
        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Actualizar Categoría';
        document.getElementById('btnSubmit').className = 'btn btn-info btn-lg fw-bold text-white';
        
        // 2. Mostrar botón de cancelar
        document.getElementById('btnCancel').classList.remove('d-none');

        // 3. Asignar valores al formulario
        document.getElementById('formAction').value = 'update';
        document.getElementById('categoriaId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;

        // 4. Enfocar el campo nombre para mejorar UX
        document.getElementById('nombre').focus();
        
        // Efecto visual de scroll hacia el formulario en móviles
        if(window.innerWidth < 992) {
            document.getElementById('categoriaForm').scrollIntoView({ behavior: 'smooth' });
        }
    }

    /**
     * Restablece el formulario al estado original (Crear)
     */
    function resetForm() {
        // 1. Revertir textos
        document.getElementById('formTitle').innerText = 'Crear Nueva Categoría';
        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Guardar Categoría';
        document.getElementById('btnSubmit').className = 'btn btn-primary btn-lg fw-bold';
        
        // 2. Ocultar botón cancelar
        document.getElementById('btnCancel').classList.add('d-none');

        // 3. Limpiar inputs
        document.getElementById('formAction').value = 'create';
        document.getElementById('categoriaId').value = '';
        document.getElementById('categoriaForm').reset();
    }
</script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>