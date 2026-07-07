<?php

$current_page = basename($_SERVER['SCRIPT_NAME']);
?>

<!-- BARRA DE NAVEGACIÓN INTERNA PARA EL ADMINISTRADOR -->
<div class="bg-light border-bottom py-2 shadow-sm">
    <div class="container px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            
            <!-- Etiqueta de Rol Seguro -->
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark px-3 py-2 rounded-2 small d-flex align-items-center gap-1">
                    <i class="bi bi-person-workspace text-warning"></i> Módulo Administrativo
                </span>
            </div>

            <!-- Botones de Navegación entre Mantenimientos -->
            <div class="nav nav-pills gap-2 font-monospace" style="font-size: 0.85rem;">
                
                <!-- Pestaña 1: Categorías -->
                <a href="./gestion_categorias.php" class="nav-link border px-3 py-2 rounded-3 d-flex align-items-center gap-1.5 <?= ($current_page == 'gestion_categorias.php') ? 'active fw-bold text-white shadow-sm' : 'bg-white text-secondary' ?>">
                    <i class="bi bi-tags-fill"></i> Categorías
                </a>

                <!-- Pestaña 2: Métodos de Pago -->
                <a href="./gestion_metodos_pago.php" class="nav-link border px-3 py-2 rounded-3 d-flex align-items-center gap-1.5 <?= ($current_page == 'gestion_metodos_pago.php') ? 'active fw-bold text-white shadow-sm' : 'bg-white text-secondary' ?>">
                    <i class="bi bi-credit-card-2-back-fill"></i> Métodos de Pago
                </a>

                <!-- Pestaña 3: Usuarios -->
                <a href="./gestion_usuarios.php" class="nav-link border px-3 py-2 rounded-3 d-flex align-items-center gap-1.5 <?= ($current_page == 'gestion_usuarios.php') ? 'active fw-bold text-white shadow-sm' : 'bg-white text-secondary' ?>">
                    <i class="bi bi-people-fill"></i> Usuarios
                </a>

                <!-- Pestaña 4: Dashboard de Reportes -->
                <a href="./dashboard_reportes.php" class="nav-link border px-3 py-2 rounded-3 d-flex align-items-center gap-1.5 <?= ($current_page == 'dashboard_reportes.php') ? 'active fw-bold text-white shadow-sm' : 'bg-white text-secondary' ?>">
                    <i class="bi bi-bar-chart-line-fill text-success"></i> Panel Analítico (5 Consultas)
                </a>

            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { transition: all 0.2s ease; border-color: #e2e8f0 !important; }
    .nav-pills .nav-link:not(.active):hover { background-color: #f1f5f9 !important; color: #1e3a8a !important; border-color: #cbd5e1 !important; }
</style>