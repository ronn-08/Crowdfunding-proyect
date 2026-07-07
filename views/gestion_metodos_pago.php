<?php
// 1. Integración con el Controlador
require_once __DIR__ . '/../controllers/metodo_pago_crud.php';

// 2. Cargar datos para la vista
$metodos = obtenerMetodosPago($db);

// 3. Configuración de Alertas Dinámicas
$alertConfig = [
    'success_create'   => ['class' => 'success', 'icon' => 'bi-check-circle-fill', 'msg' => '¡Método de pago registrado exitosamente!'],
    'success_update'   => ['class' => 'info',    'icon' => 'bi-info-circle-fill',  'msg' => '¡Configuración de pago actualizada correctamente!'],
    'success_delete'   => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'msg' => '¡Método de pago eliminado del sistema!'],
    'error_constraint' => ['class' => 'danger',  'icon' => 'bi-shield-lock-fill',  'msg' => 'No se puede eliminar este método de pago porque tiene transacciones de donación históricas vinculadas.'],
    'error'            => ['class' => 'danger',  'icon' => 'bi-bug-fill',          'msg' => 'Hubo un error al procesar la solicitud. Intente nuevamente.']
];

$currentAlert = (isset($_GET['msg']) && array_key_exists($_GET['msg'], $alertConfig)) ? $alertConfig[$_GET['msg']] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de Pagos | Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; }
        .table thead th { background-color: #f1f5f9; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border: none; }
        .btn-action { border-radius: 0.5rem; transition: all 0.2s; }
        .sticky-form { position: sticky; top: 2rem; }
        .badge-status { padding: 0.5em 0.8em; border-radius: 0.5rem; font-weight: 600; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<?php include_once __DIR__ . '/../components/admin_nav.php'; ?>
<div class="container py-5">
    <div class="row mb-5 text-center text-lg-start">
        <div class="col-12">
            <h2 class="fw-bold"><i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Métodos de Pago</h2>
            <p class="text-muted">Configura las pasarelas y opciones disponibles para las donaciones.</p>
        </div>
    </div>

    <?php if ($currentAlert): ?>
        <div class="alert alert-<?= $currentAlert['class'] ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi <?= $currentAlert['icon'] ?> fs-5 me-2"></i>
                <div><?= $currentAlert['msg'] ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-form">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" id="formTitle">Nuevo Método de Pago</h5>
                    
                    <form id="payForm" action="../controllers/metodo_pago_crud.php" method="POST">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id_metodo_pago" id="metodoId">

                        <div class="mb-3">
                            <label class="form-label">Identificador / Token</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                                <input type="text" class="form-control border-start-0" name="token_pago" id="token_pago" placeholder="Ej: STRIPE_7890X" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha de Expiración</label>
                            <input type="date" class="form-control" name="fecha_expiracion" id="fecha_expiracion">
                            <div class="form-text text-xs italic">Dejar vacío si no aplica.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Pago</label>
                            <select class="form-select" name="tipo_pago" id="tipo_pago" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                                <option value="Transferencia">Transferencia Bancaria</option>
                                <option value="Billetera">Billetera Digital (PayPal/Other)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado_pago" id="estado_pago" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSubmit" class="btn btn-primary fw-bold py-2">
                                <i class="bi bi-save me-2"></i>Guardar Método
                            </button>
                            <button type="button" id="btnCancel" class="btn btn-outline-secondary d-none" onclick="resetForm()">
                                Cancelar Edición
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold">Registros Existentes</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4">ID</th>
                                <th>Tipo</th>
                                <th>Identificador</th>
                                <th>Expiración</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($metodos) > 0): ?>
                                <?php foreach ($metodos as $m): ?>
                                    <tr>
                                        <td class="px-4 text-muted fw-bold">#<?= str_pad($m['Id_Metodo_Pago'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <?php 
                                                $icon = match($m['Tipo_Pago']) {
                                                    'Tarjeta' => 'bi-credit-card',
                                                    'Transferencia' => 'bi-bank',
                                                    'Billetera' => 'bi-wallet2',
                                                    default => 'bi-cash'
                                                };
                                            ?>
                                            <i class="bi <?= $icon ?> me-2 text-primary"></i><?= htmlspecialchars($m['Tipo_Pago']) ?>
                                        </td>
					<td>
    					<code>
        				<?php 
        				$token = htmlspecialchars($m['Token_Pago']);
        				if (strlen($token) > 4) {
            					//Muestra asteriscos seguidos de los últimos 4 dígitos
            					echo '•••• •••• ' . substr($token, -4);
        					} else {
            						echo $token;
        					}
        					?>
    						</code>
					</td>                                        
                                        <td class="small">
                                            <?php 
                                                // Sanitización adicional para evitar renders de años antiguos inconsistentes
                                                if (!empty($m['Fecha_Expiracion']) && $m['Fecha_Expiracion'] !== '0001-02-01' && strtotime($m['Fecha_Expiracion']) > 0) 						{
                                                    echo date('d/m/Y', strtotime($m['Fecha_Expiracion']));
                                                } else {
                                                    echo '<span class="text-muted">N/A</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($m['Estado_Pago'] == 'Activo'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle badge-status">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-light btn-sm text-primary btn-action" 
                                                        onclick="editMethod(<?= htmlspecialchars(json_encode($m)) ?>)"
                                                        title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <a href="../controllers/metodo_pago_crud.php?action=delete&id=<?= $m['Id_Metodo_Pago'] ?>" 
                                                   class="btn btn-light btn-sm text-danger btn-action" 
                                                   onclick="return confirm('¿Está seguro de eliminar este método de pago? Esta acción no se puede deshacer.');"
                                                   title="Eliminar">
                                                     <i class="bi bi-trash3-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No hay métodos de pago configurados.
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function editMethod(data) {
        document.getElementById('formTitle').innerText = 'Actualizar Método de Pago';
        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Actualizar Registro';
        document.getElementById('btnSubmit').classList.replace('btn-primary', 'btn-info');
        document.getElementById('btnSubmit').classList.add('text-white');
        document.getElementById('btnCancel').classList.remove('d-none');

        document.getElementById('formAction').value = 'update';
        document.getElementById('metodoId').value = data.Id_Metodo_Pago;
        document.getElementById('token_pago').value = data.Token_Pago;
        document.getElementById('fecha_expiracion').value = data.Fecha_Expiracion || '';
        document.getElementById('tipo_pago').value = data.Tipo_Pago;
        document.getElementById('estado_pago').value = data.Estado_Pago;

        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('token_pago').focus();
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Nuevo Método de Pago';
        document.getElementById('btnSubmit').innerHTML = '<i class="bi bi-save me-2"></i>Guardar Método';
        document.getElementById('btnSubmit').className = 'btn btn-primary fw-bold py-2';
        document.getElementById('btnCancel').classList.add('d-none');

        document.getElementById('formAction').value = 'create';
        document.getElementById('metodoId').value = '';
        document.getElementById('payForm').reset();
    }
</script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>