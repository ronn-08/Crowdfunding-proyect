<?php
//Conexión segura al backend y motor de base de datos
require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();


//CONCULTA 1: AVANCE DE CAMPAÑAS (Métrica de progreso visual)

//Une CAUSA_SOCIAL con META_PLAZO y calcula el total donado acumulado.
//Usa un LEFT JOIN para que las campañas con $0.00 donados también aparezcan con valor cero limpio.
$query_avance = "
    SELECT 
        c.Id_Causa,
        c.Titulo,
        m.Monto_Objetivo,
        m.Monto_Minimo,
        m.Fecha_Finalizacion,
        IFNULL(SUM(d.Total_Donado), 0.00) AS Total_Recaudado,
        ROUND((IFNULL(SUM(d.Total_Donado), 0.00) / m.Monto_Objetivo) * 100, 2) AS Porcentaje_Progreso, 
        DATEDIFF(m.Fecha_Finalizacion, NOW()) AS Dias_Restantes
    FROM CAUSA_SOCIAL c
    INNER JOIN META_PLAZO m ON c.Id_Causa = m.CAUSA_SOCIAL_Id_Causa
    LEFT JOIN DONACION d ON c.Id_Causa = d.CAUSA_SOCIAL_Id_Causa
    GROUP BY c.Id_Causa, c.Titulo, m.Monto_Objetivo, m.Monto_Minimo, m.Fecha_Finalizacion
    ORDER BY c.Id_Causa DESC";
$stmt_causa = $db->prepare($query_avance);
$stmt_causa->execute();
$campanas_avance = $stmt_causa->fetchAll(PDO::FETCH_ASSOC);


// CONSULTA 2 CONTABILIDAD GLOBAL (KPI Cards del Servidor)

//Extrae las métricas globales macro de recaudación y comisiones netas de la plataforma.
$query_global = "
    SELECT 
        IFNULL(SUM(Total_Donado), 0.00) AS Global_Recaudado,
        IFNULL(SUM(Comision), 0.00) AS Global_Comisiones,
        COUNT(Id_Donacion) AS Global_Transacciones
    FROM DONACION";
$stmt_global = $db->prepare($query_global);
$stmt_global->execute();
$kpi_global = $stmt_global->fetch(PDO::FETCH_ASSOC);


//CONSULTA 3 TOP DONANTES (Ranking de Fidelización)

//Suma todas las aportaciones por usuario y los ordena de mayor a menor.
$query_top = "
    SELECT 
        u.Nombre,
        u.Apellido,
        u.Correo,
        SUM(d.Total_Donado) AS Total_Aportado,
        COUNT(d.Id_Donacion) AS Veces_Donado
    FROM USUARIO u
    INNER JOIN DONACION d ON u.Id_Usuario = d.USUARIO_Id_Usuario
    GROUP BY u.Id_Usuario, u.Nombre, u.Apellido, u.Correo
    ORDER BY Total_Aportado DESC
    LIMIT 5";
$stmt_top = $db->prepare($query_top);
$stmt_top->execute();
$top_donantes = $stmt_top->fetchAll(PDO::FETCH_ASSOC);


// CONSULTA 4 MURO SOCIAL SEGURO (Privacidad dinámica en Frontend)

//Evalúa la columna 'Es_Anonimo'. Si es 'S', la consulta enmascara el nombre
//directamente desde la BD protegiendo la identidad del ciudadano en la capa pública.
$query_muro = "
    SELECT 
        CASE 
            WHEN d.Es_Anonimo = 'S' THEN 'Donante Anónimo'
            ELSE CONCAT(u.Nombre, ' ', u.Apellido)
        END AS Usuario_Muro,
        m.Mensaje,
        m.Fecha_Publicacion,
        c.Titulo AS Proyecto
    FROM MURO_AGRADECIMIENTO m
    INNER JOIN DONACION d ON m.DONACION_Id_Donacion = d.Id_Donacion
    INNER JOIN USUARIO u ON d.USUARIO_Id_Usuario = u.Id_Usuario
    INNER JOIN CAUSA_SOCIAL c ON d.CAUSA_SOCIAL_Id_Causa = c.Id_Causa
    WHERE m.Visibilidad = 'S'
    ORDER BY m.Fecha_Publicacion DESC
    LIMIT 4";
$stmt_muro = $db->prepare($query_muro);
$stmt_muro->execute();
$comentarios_muro = $stmt_muro->fetchAll(PDO::FETCH_ASSOC);


//CONSULTA 5 ALERTAS DE URGENCIA CRÍTICA (Filtro predictivo de riesgos)

//Encuentra causas que están a 7 días o menos de caducar y que no
//han alcanzado el umbral económico del Monto Mínimo de Éxito requerido.
$query_alertas = "
    SELECT 
        c.Titulo,
        m.Monto_Minimo,
        m.Fecha_Finalizacion,
        IFNULL(SUM(d.Total_Donado), 0.00) AS Recaudado_Actual,
        DATEDIFF(m.Fecha_Finalizacion, NOW()) AS Dias_Para_Cerrar
    FROM CAUSA_SOCIAL c
    INNER JOIN META_PLAZO m ON c.Id_Causa = m.CAUSA_SOCIAL_Id_Causa
    LEFT JOIN DONACION d ON c.Id_Causa = d.CAUSA_SOCIAL_Id_Causa
    GROUP BY c.Id_Causa, c.Titulo, m.Monto_Minimo, m.Fecha_Finalizacion
    HAVING Dias_Para_Cerrar <= 7 AND Recaudado_Actual < m.Monto_Minimo
    ORDER BY Dias_Para_Cerrar ASC";
$stmt_alertas = $db->prepare($query_alertas);
$stmt_alertas->execute();
$alertas_criticas = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estadísticas | Crowdfunding UG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; color: #0f172a; }
        .card-kpi { border: none; border-radius: 1rem; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .card-kpi:hover { transform: translateY(-3px); }
        .card-panel { border: none; border-radius: 1.25rem; background: white; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); }
        .table { vertical-align: middle; }
        .progress { height: 8px; border-radius: 10px; background-color: #e2e8f0; }
        .avatar-sub { width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #475569; }
        .header-title { font-weight: 700; color: #1e3a8a; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/../components/header.php'; ?>
<?php include_once __DIR__ . '/../components/admin_nav.php'; ?>
<div class="container-fluid py-4 px-md-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
        <div>
            <h1 class="header-title m-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Estadísticas de Recaudación</h1>
            <p class="text-muted m-0 small">Seguimiento en tiempo real e integridad transaccional de campañas.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="bi bi-clock-history me-1"></i> Zona GMT-5</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-kpi p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase fw-bold text-muted small m-0">Total Recaudado Global</h6>
                    <h2 class="fw-bold mt-2 mb-0 text-dark">$<?= number_format($kpi_global['Global_Recaudado'], 2) ?></h2>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary fs-3"><i class="bi bi-cash-coin"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-kpi p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase fw-bold text-muted small m-0">Comisiones Plataforma (5%)</h6>
                    <h2 class="fw-bold mt-2 mb-0 text-success">$<?= number_format($kpi_global['Global_Comisiones'], 2) ?></h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success fs-3"><i class="bi bi-percent"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-kpi p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase fw-bold text-muted small m-0">Transacciones Exitosas</h6>
                    <h2 class="fw-bold mt-2 mb-0 text-info"><?= $kpi_global['Global_Transacciones'] ?></h2>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info fs-3"><i class="bi bi-shield-check"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-8">
            <div class="card card-panel p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Monitoreo de Metas Financieras</h5>
                <div class="table-responsive">
                    <table class="table table-hover border-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 rounded-start">Campaña Social</th>
                                <th class="border-0">Meta Objetivo</th>
                                <th class="border-0">Recaudado</th>
                                <th class="border-0">Progreso</th>
                                <th class="border-0 rounded-end text-center">Estado / Cierre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($campanas_avance)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No hay campañas registradas en el sistema.</td></tr>
                            <?php else: ?>
                                <?php foreach($campanas_avance as $ca): ?>
                                    <tr>
                                        <td class="fw-bold text-secondary"><?= htmlspecialchars($ca['Titulo']) ?></td>
                                        <td>$<?= number_format($ca['Monto_Objetivo'], 2) ?></td>
                                        <td class="text-success fw-medium">$<?= number_format($ca['Total_Recaudado'], 2) ?></td>
                                        <td style="width: 20%;">
                                            <div class="d-flex align-items-center justify-content-between small mb-1">
                                                <span class="fw-bold"><?= $ca['Porcentaje_Progreso'] ?>%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min($ca['Porcentaje_Progreso'], 100) ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($ca['Dias_Restantes'] < 0): ?>
                                                <span class="badge bg-secondary px-2 py-1 rounded">Finalizada</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border px-2 py-1 rounded"><i class="bi bi-calendar-event me-1"></i> <?= $ca['Dias_Restantes'] ?> días</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-panel p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Alertas de Urgencia Crítica</h5>
                <div class="d-flex flex-column gap-3">
                    <?php if(empty($alertas_criticas)): ?>
                        <div class="text-center py-5 border rounded-3 bg-light">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="text-muted mt-2 mb-0 small px-3">Excelente. Todas las campañas activas se encuentran estables o con plazos saludables.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($alertas_criticas as $ac): ?>
                            <div class="p-3 border-start border-4 border-danger rounded-end bg-danger bg-opacity-10">
                                <h6 class="fw-bold text-danger m-0 mb-1"><?= htmlspecialchars($ac['Titulo']) ?></h6>
                                <p class="text-muted small m-0 mb-2">Recaudado: <strong class="text-dark">$<?= number_format($ac['Recaudado_Actual'], 2) ?></strong> de un mínimo de $<?= number_format($ac['Monto_Minimo'], 2) ?>.</p>
                                <span class="badge bg-danger rounded-pill"><i class="bi bi-alarm-fill me-1"></i> ¡Cierra en <?= $ac['Dias_Para_Cerrar'] ?> días!</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="card card-panel p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-trophy-fill text-warning me-2"></i> Ranking de Filantropía (Top Donantes)</h5>
                <div class="table-responsive">
                    <table class="table table-sm border-0">
                        <thead>
                            <tr class="text-muted small border-bottom">
                                <th>Donante</th>
                                <th>Contacto</th>
                                <th class="text-center">Aportes</th>
                                <th class="text-end">Total Donado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($top_donantes)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Aún no se registran transacciones contables.</td></tr>
                            <?php else: ?>
                                <?php $pos = 1; foreach($top_donantes as $td): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-circle"><?= $pos++ ?></span>
                                                <span class="fw-bold text-secondary"><?= htmlspecialchars($td['Nombre'] . ' ' . $td['Apellido']) ?></span>
                                            </div>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($td['Correo']) ?></td>
                                        <td class="text-center badge bg-light text-dark mt-2 ms-4"><?= $td['Veces_Donado'] ?></td>
                                        <td class="text-end fw-bold text-primary">$<?= number_format($td['Total_Aported'] ?? $td['Total_Aportado'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-panel p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-chat-heart-fill text-danger me-2"></i> Muro Social Seguro (Últimos Comentarios)</h5>
                <div class="d-flex flex-column gap-3">
                    <?php if(empty($comentarios_muro)): ?>
                        <p class="text-center text-muted py-4 m-0">No se han registrado mensajes en el muro público.</p>
                    <?php else: ?>
                        <?php foreach($comentarios_muro as $cm): ?>
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sub small"><?= substr($cm['Usuario_Muro'], 0, 2) ?></div>
                                        <span class="fw-bold text-secondary small"><?= htmlspecialchars($cm['Usuario_Muro']) ?></span>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i', strtotime($cm['Fecha_Publicacion'])) ?></span>
                                </div>
                                <p class="m-0 text-dark font-monospace small bg-white p-2 rounded border-start border-3 border-info">"<?= htmlspecialchars($cm['Mensaje']) ?>"</p>
                                <div class="text-end mt-1" style="font-size: 0.7rem; color: #64748b;">
                                    Apoyó a: <span class="fw-medium text-primary"><?= htmlspecialchars($cm['Proyecto']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>