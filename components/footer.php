<?php
//Volvemos a calcular la ruta base de respaldo por seguridad si el footer se incluye de forma aislada
$current_script_footer = $_SERVER['SCRIPT_NAME'];
$is_subfolder_footer = (strpos($current_script_footer, '/views/') !== false);
$base_route_footer = $is_subfolder_footer ? '../' : './';
?>

<footer class="bg-slate-dark text-white-50 py-5 mt-auto border-top border-dark" style="background-color: #1e293b;">
    <div class="container px-md-4">
        <div class="row g-4">
            
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="bg-white bg-opacity-10 text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="bi bi-rocket-takeoff text-info"></i>
                    </div>
                    <span class="fs-5 text-white fw-bold">Crowdfunding <span class="text-info">UG</span></span>
                </div>
                <p class="small lh-lg mb-4" style="color: #94a3b8;">
                    Plataforma académica integral orientada al financiamiento colectivo y transparente de proyectos de innovación, causas sociales y desarrollo tecnológico estudiantil.
                </p>
                <p class="small m-0 text-muted">
                    &copy; <?= date('Y') ?> Universidad de Guayaquil.<br>Facultad de Ciencias Matemáticas y Físicas.
                </p>
            </div>

            <div class="col-lg-4 col-md-6 px-lg-5">
                <h6 class="text-white fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Ecosistema</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li>
                        <a href="<?= $base_route_footer ?>index.php" class="text-decoration-none text-footer-link d-flex align-items-center gap-2">
                            <i class="bi bi-chevron-right text-info" style="font-size: 0.7rem;"></i> Inicio de Plataforma
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_route_footer ?>views/crear_campana.php" class="text-decoration-none text-footer-link d-flex align-items-center gap-2">
                            <i class="bi bi-chevron-right text-info" style="font-size: 0.7rem;"></i> Publicar Proyecto Activo
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_route_footer ?>views/registro_publico.php" class="text-decoration-none text-footer-link d-flex align-items-center gap-2">
                            <i class="bi bi-chevron-right text-info" style="font-size: 0.7rem;"></i> Crear Cuenta Ciudadana
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_route_footer ?>views/dashboard_reportes.php" class="text-decoration-none text-footer-link d-flex align-items-center gap-2 fw-semibold text-white">
                            <i class="bi bi-graph-up-arrow text-success"></i> Ver Estadísticas Globales
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h6 class="text-white fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Soporte y Transparencia</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small mb-4">
                    <li><span class="text-decoration-none text-footer-link d-flex align-items-center gap-2"><i class="bi bi-shield-check text-info"></i> Términos y Condiciones Legales</span></li>
                    <li><span class="text-decoration-none text-footer-link d-flex align-items-center gap-2"><i class="bi bi-lock text-info"></i> Políticas de Privacidad Seguro</span></li>
                    <li><span class="text-decoration-none text-footer-link d-flex align-items-center gap-2"><i class="bi bi-headset text-info"></i> Central de Ayuda FCMF</span></li>
                </ul>
                <div class="p-3 rounded-3 bg-white bg-opacity-5 border border-secondary border-opacity-20 small d-flex align-items-center gap-3">
                    <i class="bi bi-envelope-at text-info fs-4"></i>
                    <div>
                        <div class="text-black fw-medium">¿Tienes dudas técnicas?</div>
                        <span class="text-muted" style="font-size: 0.8rem;">soporte.crowd@gmail.com</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>

<style>
    .text-footer-link { color: #94a3b8; transition: all 0.2s; }
    .text-footer-link:hover { color: #ffffff !important; padding-left: 4px; }
</style>