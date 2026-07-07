<?php
//Lógica de Procesamiento y Conexión
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();


//PROCESAMIENTO POST: MOTOR TRANSACCIONAL (TODO O NADA)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario     = $_POST['id_usuario'];
    $id_causa       = $_POST['id_causa'];
    $id_metodo_pago = $_POST['id_metodo_pago'];
    $total_donado   = floatval($_POST['total_donado']);
    $es_anonimo     = isset($_POST['es_anonimo']) ? 'S' : 'N';
    $mensaje_muro   = trim($_POST['mensaje_muro']);

    // Validación en el servidor de campos obligatorios
    if (!empty($id_usuario) && !empty($id_causa) && !empty($id_metodo_pago) && $total_donado > 0) {
        try {
            //INICIAMOS LA TRANSACCIÓN ACID
            $db->beginTransaction();

            // Calcular comisión de la plataforma (5%)
            $comision = $total_donado * 0.05;

            // Paso 1: Insertar la Donación Única (RF 07)
            $query_donacion = "INSERT INTO DONACION (Tipo_Donacion, Es_Anonimo, Total_Donado, Comision, METODO_DE_PAGO_Id_Metodo_Pago, USUARIO_Id_Usuario, CAUSA_SOCIAL_Id_Causa) 
                               VALUES ('Unica', :es_anonimo, :total_donado, :comision, :id_metodo_pago, :id_usuario, :id_causa)";
            $stmt_don = $db->prepare($query_donacion);
            $stmt_don->bindParam(':es_anonimo', $es_anonimo);
            $stmt_don->bindParam(':total_donado', $total_donado);
            $stmt_don->bindParam(':comision', $comision);
            $stmt_don->bindParam(':id_metodo_pago', $id_metodo_pago);
            $stmt_don->bindParam(':id_usuario', $id_usuario);
            $stmt_don->bindParam(':id_causa', $id_causa);
            $stmt_don->execute();

            // Obtener el ID de la donación recién creada
            $id_donacion_reciente = $db->lastInsertId();

            // Paso 2: Generar Comprobante Digital con código único (RF 09)
            $code_transaccion = "TXN-" . strtoupper(bin2hex(random_bytes(6)));
            $pdf_path         = "uploads/comprobantes/comprobante_" . $code_transaccion . ".pdf";
            $qr_mock          = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $code_transaccion;

            $query_comprobante = "INSERT INTO COMPROBANTE (pdf, Estado_Pago, QR_Verificacion, Code_Transaccion, DONACION_Id_Donacion) 
                                  VALUES (:pdf, 'Aprobado', :qr, :code_tx, :id_donacion)";
            $stmt_com = $db->prepare($query_comprobante);
            $stmt_com->bindParam(':pdf', $pdf_path);
            $stmt_com->bindParam(':qr', $qr_mock);
            $stmt_com->bindParam(':code_tx', $code_transaccion);
            $stmt_com->bindParam(':id_donacion', $id_donacion_reciente);
            $stmt_com->execute();

            // Paso 3: Publicar en el Muro de Agradecimientos si escribió un mensaje (RF 12)
            if (!empty($mensaje_muro)) {
                $query_muro = "INSERT INTO MURO_AGRADECIMIENTO (Mensaje, Visibilidad, DONACION_Id_Donacion) 
                               VALUES (:mensaje, 'S', :id_donacion)";
                $stmt_mur = $db->prepare($query_muro);
                $stmt_mur->bindParam(':mensaje', $mensaje_muro);
                $stmt_mur->bindParam(':id_donacion', $id_donacion_reciente);
                $stmt_mur->execute();
            }

            //SI TODO ESTÁ PERFECTO, CONFIRMAMOS LOS CAMBIOS EN LA BD
            $db->commit();

            // Redirección interna de éxito pasándole los datos al banner
            header("Location: realizar_donacion.php?msg=success_transaccion&code=" . $code_transaccion . "&monto=" . $total_donado);
            exit();

        } catch (Exception $e) {
            //EN CASO DE FALLO REVERTIMOS TODO AL ESTADO INICIAL
            $db->rollBack();
            header("Location: realizar_donacion.php?id_causa=$id_causa&msg=error_transaccion&debug=" . urlencode($e->getMessage()));
            exit();
        }
    }
}


//CARGA DE DATOS PARA LA VISTA 

$id_usuario_actual = 1; // Forzado temporalmente para desarrollo
$id_causa_actual = isset($_GET['id_causa']) ? intval($_GET['id_causa']) : 1;

// Obtener métodos de pago activos para el combobox
$query_metodos = "SELECT Id_Metodo_Pago, Tipo_Pago, Token_Pago FROM METODO_DE_PAGO WHERE Estado_Pago = 'Activo'";
$stmt_metodos = $db->prepare($query_metodos);
$stmt_metodos->execute();
$metodos_pago = $stmt_metodos->fetchAll(PDO::FETCH_ASSOC);

// CORRECCIÓN DE LA COLUMNA: Cambiado 'Nombre' por 'Titulo'
$query_causa = "SELECT Titulo, Descripcion FROM CAUSA_SOCIAL WHERE Id_Causa = :id";
$stmt_causa = $db->prepare($query_causa);
$stmt_causa->bindParam(':id', $id_causa_actual);
$stmt_causa->execute();
$causa = $stmt_causa->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Donación | Crowdfunding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
        .btn-amount { border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem; font-weight: 600; transition: all 0.2s; background: white; }
        .btn-amount:hover, .btn-amount.active { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
        .summary-card { background: #1e293b; color: white; }
        .summary-divider { border-top: 1px dashed rgba(255,255,255,0.2); }
        .form-control, .form-select { border-radius: 0.75rem; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); border-color: #3b82f6; }
        .donation-header { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 1.25rem; padding: 2rem; color: white; margin-bottom: 2rem; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<div class="container py-5">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error_transaccion'): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-shield-exclamation me-2"></i>
            <strong>Transacción Revertida (Rollback):</strong> La base de datos canceló la donación de forma segura.
            <small class="d-block mt-1 text-muted"><?= htmlspecialchars($_GET['debug'] ?? '') ?></small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success_transaccion'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>¡Donación Completada Exitosamente (Commit)!</strong> El dinero fue acreditado y se generó el código: <strong><?= htmlspecialchars($_GET['code'] ?? '') ?></strong> por un valor de $<?= htmlspecialchars($_GET['monto'] ?? '0.00') ?>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="donation-header shadow-lg">
                <h2 class="fw-bold mb-1"><i class="bi bi-heart-fill me-2"></i> Tu apoyo hace la diferencia</h2>
                <p class="mb-0 opacity-75">Estás donando a: <strong><?= htmlspecialchars($causa['Titulo'] ?? 'Causa Social') ?></strong></p>
            </div>

            <div class="card p-4 p-md-5">
                <form id="donationForm" action="realizar_donacion.php" method="POST">
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario_actual ?>">
                    <input type="hidden" name="id_causa" value="<?= $id_causa_actual ?>">

                    <div class="mb-4">
                        <label class="form-label mb-3">¿Cuánto deseas aportar?</label>
                        <div class="row g-2 mb-3">
                            <div class="col-3"><button type="button" class="btn btn-amount w-100" onclick="setAmount(5)">$5</button></div>
                            <div class="col-3"><button type="button" class="btn btn-amount w-100" onclick="setAmount(10)">$10</button></div>
                            <div class="col-3"><button type="button" class="btn btn-amount w-100" onclick="setAmount(25)">$25</button></div>
                            <div class="col-3"><button type="button" class="btn btn-amount w-100" onclick="setAmount(50)">$50</button></div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 fs-5 fw-bold">$</span>
                            <input type="number" step="0.01" class="form-control border-start-0 fs-5 fw-bold" 
                                   name="total_donado" id="total_donado" placeholder="Otro monto" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Método de Pago</label>
                        <select class="form-select" name="id_metodo_pago" required>
                            <option value="" selected disabled>Selecciona tu forma de pago</option>
                            <?php foreach ($metodos_pago as $mp): ?>
                                <option value="<?= $mp['Id_Metodo_Pago'] ?>">
                                    <?= htmlspecialchars($mp['Tipo_Pago']) ?> - (•••• <?= htmlspecialchars(substr($mp['Token_Pago'], -4)) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Mensaje de apoyo (Muro)</label>
                        <textarea class="form-control" name="mensaje_muro" rows="3" 
                                  placeholder="Escribe unas palabras de aliento para la causa..."></textarea>
                    </div>

                    <div class="form-check form-switch p-3 border rounded-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="es_anonimo" id="es_anonimo">
                        <label class="form-check-label fw-bold" for="es_anonimo">Donar de forma anónima</label>
                        <div class="form-text mt-0 ms-5">Tu nombre no será visible públicamente en el muro de agradecimientos.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-5 py-3 fw-bold shadow">
                        <i class="bi bi-shield-check me-2"></i> Confirmar Donación Segura
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
    <div class="card summary-card p-4 sticky-top" style="top: 6rem; z-index: 10;">
                <h5 class="fw-bold mb-4">Resumen del Aporte</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="opacity-75">Monto Donación</span>
                    <span class="fw-bold" id="res_subtotal">$0.00</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="opacity-75">Comisión Plataforma (5%)</span>
                    <span class="fw-bold" id="res_comision">$0.00</span>
                </div>

                <div class="summary-divider mb-3"></div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold">Total a cobrar</span>
                    <span class="fs-3 fw-bold text-info" id="res_total">$0.00</span>
                </div>

                <div class="bg-white bg-opacity-10 p-3 rounded-3 small">
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-shield-lock-fill me-2 text-info"></i>
                        <span>Pago procesado con encriptación SSL de 256 bits.</span>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="bi bi-file-earmark-pdf-fill me-2 text-info"></i>
                        <span>Se registrará un comprobante ACID en el sistema.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const inputMonto = document.getElementById('total_donado');
    const resSubtotal = document.getElementById('res_subtotal');
    const resComision = document.getElementById('res_comision');
    const resTotal = document.getElementById('res_total');

    function setAmount(val) {
        inputMonto.value = val;
        updateSummary();
        
        document.querySelectorAll('.btn-amount').forEach(btn => {
            btn.classList.toggle('active', btn.innerText === '$' + val);
        });
    }

    function updateSummary() {
        const monto = parseFloat(inputMonto.value) || 0;
        const comision = monto * 0.05;

        resSubtotal.innerText = '$' + (monto - comision).toFixed(2);
        resComision.innerText = '$' + comision.toFixed(2);
        resTotal.innerText = '$' + monto.toFixed(2);
    }

    inputMonto.addEventListener('input', updateSummary);
</script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>