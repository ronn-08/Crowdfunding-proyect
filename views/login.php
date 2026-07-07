<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Portal Crowdfunding UG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f1f5f9; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
        }
        .card-login { 
            border: none; 
            border-radius: 1.5rem; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);
        }
        .form-label { 
            font-weight: 600; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            color: #64748b; 
            letter-spacing: 0.5px; 
        }
        .form-control { 
            border-radius: 0.75rem; 
            padding: 0.75rem 1rem; 
            border: 1px solid #cbd5e1; 
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body>

    <?php include_once __DIR__ . '/../components/header.php'; ?>

    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 460px;">
            
            <div class="mb-3">
                <a href="../index.php" class="btn btn-white bg-white border border-light-subtle rounded-3 px-3 py-2 btn-sm fw-semibold text-secondary shadow-sm">
                    <i class="bi bi-arrow-left me-1 text-primary fw-bold"></i> Volver al Inicio
                </a>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error_credenciales'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error de Autenticación:</strong> El correo o la contraseña ingresada son incorrectos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-login p-4 p-md-5 bg-white">
                
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-lock-fill fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-dark m-0">Ingreso al Portal</h3>
                    <p class="text-muted small m-0 mt-1">Digita tus credenciales registradas para acceder a tu perfil universitario.</p>
                </div>

                <form action="../controllers/login_proc.php" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0" name="email" placeholder="ejemplo@ug.edu.ec" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control border-start-0" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 shadow-sm text-uppercase font-monospace tracking-wide" style="font-size: 0.9rem;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Entrar a la Plataforma
                    </button>
                </form>

            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../components/footer.php'; ?>

</body>
</html>