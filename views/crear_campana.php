<?php
// Incluimos la base de datos para leer las categorías vivas
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Consulta para popular los Checkboxes dinámicamente
$query_cat = "SELECT Id_Categoria, Nombre FROM CATEGORIA ORDER BY Nombre ASC";
$stmt_cat = $db->prepare($query_cat);
$stmt_cat->execute();
$lista_categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Nueva Causa | Sistema Crowdfunding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.82rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 0.75rem; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #2563eb; border-bottom: 2px solid #eff6ff; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<div class="container py-5" style="max-width: 900px;">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success_campana'): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>¡Campaña Registrada con Éxito (Commit)!</strong> Los plazos, metas y taxonomías fueron guardados de forma segura en estado 'Borrador'.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error_fechas'): ?>
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-calendar-x me-2"></i>
            <strong>Regla de Negocio Violada:</strong> La duración de la campaña debe estar obligatoriamente en el rango de 7 a 180 días.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error_transaccion'): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Transacción Abortada (Rollback):</strong> Ocurrió un error interno en la inyección de datos masiva:
            <small class="d-block mt-1 text-muted"><?= htmlspecialchars($_GET['debug'] ?? '') ?></small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4 p-md-5">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary text-white p-3 rounded-3 me-3">
                <i class="bi bi-rocket-takeoff-fill fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bold m-0">Lanzar Nueva Causa Social</h2>
                <p class="text-muted m-0 small">Completa los parámetros económicos y referenciales requeridos.</p>
            </div>
        </div>

        <form action="../controllers/campana_transacc_proc.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_usuario" value="1">

            <div class="section-title"><i class="bi bi-info-circle me-2"></i> 1. Datos Generales de la Causa</div>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label">Título de la Causa (Máx. 120 caracteres)</label>
                    <input type="text" class="form-control" name="titulo" maxlength="120" placeholder="Ej: Tratamiento médico para niños de escasos recursos" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción detallada del propósito</label>
                    <textarea class="form-control" name="descripcion" rows="4" maxlength="2000" placeholder="Describe claramente en qué se utilizarán los fondos..." required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Beneficiario / Organización</label>
                    <input type="text" class="form-control" name="beneficiario" placeholder="Ej: Fundación Niños Libres" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">País</label>
                    <input type="text" class="form-control" name="pais" value="Ecuador" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" name="ciudad" placeholder="Ej: Guayaquil" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Fotografía de Portada (JPG / PNG - Máx. 5MB)</label>
                    <input type="file" class="form-control" name="foto_principal" accept="image/*" required>
                </div>
            </div>

            <div class="section-title"><i class="bi bi-cash-coin me-2"></i> 2. Configuración Financiera y Plazos</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Monto Meta Objetivo ($ USD)</label>
                    <input type="number" step="0.01" class="form-control fs-5 fw-bold text-primary" name="monto_objetivo" placeholder="0.00" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Monto Mínimo de Éxito ($ USD)</label>
                    <input type="number" step="0.01" class="form-control fs-5 fw-bold text-success" name="monto_minimo" placeholder="0.00" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Inicio de Recaudación</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha Límite de Cierre</label>
                    <input type="date" class="form-control" name="fecha_finalizacion" required>
                </div>
            </div>

            <div class="section-title"><i class="bi bi-tags me-2"></i> 3. Clasificación Temática</div>
            <div class="mb-5">
                <label class="form-label d-block mb-3">Selecciona una o más categorías aplicables:</label>
                <div class="row g-2">
                    <?php foreach ($lista_categorias as $cat): ?>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light d-flex align-items-center">
                                <input class="form-check-input me-3" type="checkbox" name="categorias[]" value="<?= $cat['Id_Categoria'] ?>" id="cat_<?= $cat['Id_Categoria'] ?>">
                                <label class="form-check-label fw-medium text-dark" for="cat_<?= $cat['Id_Categoria'] ?>">
                                    <?= htmlspecialchars($cat['Nombre']) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Registrar y Publicar Causa en Borrador
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>