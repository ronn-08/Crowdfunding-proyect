<?php
//Conexión segura a la base de datos desde la raíz
require_once __DIR__ . '/config/database.php';
$database = new Database();
$db = $database->getConnection();

//CONSULTA DINÁMICA: EXPLORADOR DE CAMPAÑAS 

//Trae el título, descripción corta, monto objetivo y calcula el progreso 
//real sumando las donaciones de cada campaña para que las barras se llenen solas.
$query_causas = "
    SELECT 
        c.Id_Causa,
        c.Titulo,
        SUBSTRING(c.Descripcion, 1, 140) AS Descripcion_Corta,
        m.Monto_Objetivo,
        IFNULL(SUM(d.Total_Donado), 0.00) AS Total_Recaudado,
        ROUND((IFNULL(SUM(d.Total_Donado), 0.00) / m.Monto_Objetivo) * 100, 2) AS Porcentaje_Progreso
    FROM CAUSA_SOCIAL c
    INNER JOIN META_PLAZO m ON c.Id_Causa = m.CAUSA_SOCIAL_Id_Causa
    LEFT JOIN DONACION d ON c.Id_Causa = d.CAUSA_SOCIAL_Id_Causa
    GROUP BY c.Id_Causa, c.Titulo, c.Descripcion, m.Monto_Objetivo
    ORDER BY c.Id_Causa DESC";

$stmt_causas = $db->prepare($query_causas);
$stmt_causas->execute();
$lista_causas = $stmt_causas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crowdfunding UG | Financiamiento Colectivo Transparente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .hero-banner { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; border-radius: 0 0 2rem 2rem; }
        .card-project { border: none; border-radius: 1.25rem; background: white; transition: all 0.2s ease; }
        .card-project:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }
        .note-box { border: none; border-radius: 1rem; background: #ffffff; border-left: 4px solid #3b82f6; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .progress { height: 7px; border-radius: 10px; background-color: #f1f5f9; }
    </style>
</head>
<body>

    <?php include_once __DIR__ . '/components/header.php'; ?>

    <section class="hero-banner py-5 mb-5 shadow-sm">
        <div class="container px-md-4 py-4 text-center text-lg-start">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-white bg-opacity-20 text-black px-3 py-2 rounded-pill mb-3 fw-semibold"><i class="text-warning me-1"></i> El poder de la comunidad</span>
                    <h1 class="display-4 fw-bold mb-3 lh-sm">Financia grandes ideas y cambia realidades</h1>
                    <p class="lead opacity-75 mb-4 font-sans">
                        Una plataforma impulsada por la Universidad de Guayaquil para conectar proyectos de innovación estudiantil, emprendimientos y causas sociales con personas dispuestas a marcar la diferencia.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                        <a href="./views/crear_campana.php" class="btn btn-white bg-white text-primary btn-lg px-4 py-3 fw-bold rounded-3 shadow-sm hover-scale" style="transition: all 0.2s;">
                            <i class="bi bi-rocket-takeoff-fill me-2"></i> Comenzar mi Proyecto
                        </a>
                        <a href="#catalogo" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-3">
                            <i class="bi bi-compass me-1"></i> Explorar Causas
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <i class="bi bi-globe-americas display-1 text-white opacity-25" style="font-size: 10rem;"></i>
                </div>
            </div>
        </div>
    </section>

    <main class="container px-md-4 mb-5" id="catalogo">
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
            <div>
                <h3 class="fw-bold m-0"><i class="bi bi-grid-fill text-primary me-2"></i> Proyectos Disponibles</h3>
                <p class="text-muted m-0 small">Explora e invierte en causas validadas por nuestro equipo administrativo.</p>
            </div>
            <a href="./views/dashboard_reportes.php" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold d-none d-sm-inline-block shadow-sm">
                <i class="bi bi-graph-up-arrow me-1"></i> Ver Métricas Globales
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($lista_causas)): ?>
                <div class="col-12 text-center py-5">
                    <div class="card p-5 bg-white border-0 rounded-4 shadow-sm" style="max-width: 500px; margin: 0 auto;">
                        <i class="bi bi-folder-x display-3 text-muted"></i>
                        <h5 class="fw-bold mt-3">No hay campañas activas</h5>
                        <p class="text-muted small">Sé el primero en postular una causa social haciendo clic en "Comenzar mi Proyecto".</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($lista_causas as $causa): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-project h-100 p-4 shadow-sm d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-bold">ID #<?= str_pad($causa['Id_Causa'], 4, '0', STR_PAD_LEFT) ?></span>
                                    <span class="text-muted small"><i class="bi bi-geo-alt"></i> Ecuador</span>
                                </div>
                                
                                <h5 class="fw-bold text-dark mb-2 text-truncate-2" style="height: 3rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= htmlspecialchars($causa['Titulo']) ?>
                                </h5>
                                
                                <p class="text-muted small mb-4 text-truncate-3" style="height: 3.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                    <?= htmlspecialchars($causa['Descripcion_Corta']) ?>...
                                </p>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center small mb-1.5 fw-medium">
                                    <span class="text-secondary">Progreso: <strong class="text-dark"><?= $causa['Porcentaje_Progreso'] ?>%</strong></span>
                                    <span class="text-muted">Meta: $<?= number_format($causa['Monto_Objetivo'], 0) ?></span>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min($causa['Porcentaje_Progreso'], 100) ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light">
                                    <div>
                                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Recaudado</div>
                                        <span class="fw-bold text-success">$<?= number_format($causa['Total_Recaudado'], 2) ?></span>
                                    </div>
                                    <a href="./views/realizar_donacion.php?id_causa=<?= $causa['Id_Causa'] ?>" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-heart-fill me-1 small"></i> Apoyar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <section class="container px-md-4 mb-5">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light-subtle">
                    <div class="text-primary fs-2 mb-1"><i class="bi bi-heart-pulse-fill"></i></div>
                    <h3 class="fw-bold text-dark m-0"><?= count($lista_causas) ?></h3>
                    <p class="text-muted small m-0">Causas Creadas</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light-subtle">
                    <div class="text-success fs-2 mb-1"><i class="bi bi-shield-check"></i></div>
                    <h3 class="fw-bold text-dark m-0">100%</h3>
                    <p class="text-muted small m-0">Flujos Auditados</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light-subtle">
                    <div class="text-warning fs-2 mb-1"><i class="bi bi-people-fill"></i></div>
                    <h3 class="fw-bold text-dark m-0">Comunidad</h3>
                    <p class="text-muted small m-0">Estudiantes UG</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light-subtle">
                    <div class="text-danger fs-2 mb-1"><i class="bi bi-lightning-charge-fill"></i></div>
                    <h3 class="fw-bold text-dark m-0">Inmediato</h3>
                    <p class="text-muted small m-0">Aporte Seguro</p>
                </div>
            </div>
        </div>
    </section>

    <section class="w-100 py-5 text-white shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="container px-md-4 py-3">
            <div class="text-center mb-5">
                <span class="badge bg-info bg-opacity-20 text-info px-3 py-2 rounded-pill small fw-bold text-white mb-2">Paso a Paso</span>
                <h3 class="fw-bold m-0 text-white">¿Cómo puedes empezar a transformar realidades?</h3>
                <p class="text-white small m-0 mt-1">Nuestra plataforma conecta la solidaridad ciudadana con la innovación de la Universidad de Guayaquil.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4 text-center px-4">
                    <div class="bg-white bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 65px; height: 65px;">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">1. Crea tu Cuenta</h5>
                    <p class="text-white small lh-base">Regístrate de forma gratuita seleccionando tu rol de uso público: apoya proyectos como Donante o postula causas como Emprendedor.</p>
                </div>
                <div class="col-md-4 text-center px-4">
                    <div class="bg-white bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 65px; height: 65px;">
                        <i class="bi bi-search fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">2. Explora Proyectos</h5>
                    <p class="text-white small lh-base">Navega por nuestro catálogo dinámico de causas sociales y tecnológicas validadas y auditadas por el equipo administrativo de la UG.</p>
                </div>
                <div class="col-md-4 text-center px-4">
                    <div class="bg-white bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 65px; height: 65px;">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">3. Aporta con Seguridad</h5>
                    <p class="text-white small lh-base">Realiza donaciones con transacciones seguras bajo cumplimiento ACID, descarga tu comprobante digital y deja mensajes en el muro público.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================== -->
    <!-- 🌟 REDISEÑO COMPLETO: CONSEJOS CON RELIEVE Y CONTRASTE REAL -->
    <!-- ==================================================================== -->
    <section class="container px-md-4 py-5 my-4">
        <div class="mb-4 text-center text-md-start">
            <h4 class="fw-bold m-0 text-dark"><i class="bi bi-lightbulb-fill text-warning me-2"></i> Claves para el Éxito de tu Campaña</h4>
            <p class="text-muted m-0 small">Si eres emprendedor, toma en consideración estos pilares técnicos para potenciar tu recaudación colectiva.</p>
        </div>
        <div class="row g-4 mt-2">
            <!-- Consejo 1 -->
            <div class="col-md-4">
                <div class="card border-0 p-4 rounded-4 shadow-sm h-100 position-relative bg-white" style="border-top: 5px solid #2563eb !important; transition: transform 0.2s;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2.5 fs-4 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h6 class="fw-bold text-dark m-0">Metas Financieras Coherentes</h6>
                    </div>
                    <p class="text-muted small m-0 lh-base">
                        El monto objetivo debe cubrir únicamente los insumos reales del proyecto. Plantear metas económicas desproporcionadas reduce la intención de aporte de los microdonantes.
                    </p>
                </div>
            </div>
            <!-- Consejo 2 -->
            <div class="col-md-4">
                <div class="card border-0 p-4 rounded-4 shadow-sm h-100 position-relative bg-white" style="border-top: 5px solid #10b981 !important; transition: transform 0.2s;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5 fs-4 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-image"></i>
                        </div>
                        <h6 class="fw-bold text-dark m-0">Transparencia Informativa</h6>
                    </div>
                    <p class="text-muted small m-0 lh-base">
                        Sube imágenes nítidas de portada y detalla presupuestos en la descripción de la causa. La confianza de la comunidad digital se fundamenta en evidencias verificables.
                    </p>
                </div>
            </div>
            <!-- Consejo 3 -->
            <div class="col-md-4">
                <div class="card border-0 p-4 rounded-4 shadow-sm h-100 position-relative bg-white" style="border-top: 5px solid #f59e0b !important; transition: transform 0.2s;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 fs-4 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-chat-left-heart"></i>
                        </div>
                        <h6 class="fw-bold text-dark m-0">Interacción con la Comunidad</h6>
                    </div>
                    <p class="text-muted small m-0 lh-base">
                        Monitorea y responde con empatía a los mensajes de apoyo que dejen los usuarios en el muro público. Un lazo cercano incrementa significativamente la fidelización masiva.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/components/footer.php'; ?>

</body>
</html>