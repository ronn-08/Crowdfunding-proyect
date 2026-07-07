<?php

//DETECTOR DINÁMICO DE RUTAS Y SESIÓN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_script = $_SERVER['SCRIPT_NAME'];
$is_subfolder = (strpos($current_script, '/views/') !== false);
$base_route = $is_subfolder ? '../' : './';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 border-bottom border-light shadow-sm">
    <div class="container px-md-4">
        
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="<?= $base_route ?>index.php" style="letter-spacing: -0.5px;">
            <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-rocket-takeoff-fill fs-5"></i>
            </div>
            <span class="fs-5 text-dark fw-bold">Crowdfunding <span class="text-primary">UG</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3 text-center">
                <li class="nav-item">
                    <a class="nav-link fw-medium px-3 text-secondary" href="<?= $base_route ?>index.php">
                        <i class="bi bi-house-door me-1"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium px-3 text-secondary" href="<?= $base_route ?>views/crear_campana.php">
                        <i class="bi bi-plus-circle me-1"></i> Comenzar Proyecto
                    </a>
                </li>
                
                <?php if (!isset($_SESSION['usuario_rol'])): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium px-3 text-secondary" href="<?= $base_route ?>views/registro_publico.php">
                            <i class="bi bi-person-plus me-1"></i> Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex justify-content-center align-items-center gap-2 mt-3 mt-lg-0">
                <?php if (isset($_SESSION['usuario_rol'])): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2.5 rounded-3 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-6"></i> Hola, <?= htmlspecialchars($_SESSION['usuario_nom']) ?> (<?= $_SESSION['usuario_rol'] ?>)
                    </span>
                    <a href="<?= $base_route ?>controllers/logout.php" class="btn btn-outline-danger btn-sm px-3 py-2 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                <?php else: ?>
                    <a href="<?= $base_route ?>views/login.php" class="btn btn-primary px-4 py-2 rounded-3 fw-bold btn-sm shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</nav>

<style>
    .nav-link { transition: color 0.2s ease, background-color 0.2s; border-radius: 0.5rem; }
    .nav-link:hover { color: #2563eb !important; background-color: #f8fafc; }
</style>