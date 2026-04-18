<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Mi Proyecto</title>
    
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    
    <style>
        body {
            background-color: #f8f9fa; /* Gris muy claro de fondo */
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #e0f2fe 0%, #fce7f3 100%); /* Degradado celeste a rosa pastel */
            border-radius: 15px;
            padding: 4rem 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .btn-custom {
            background-color: #bae6fd; /* Botón celeste pastel */
            color: #0c4a6e;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #7dd3fc;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="hero-section text-center">
                <h1 class="display-4 fw-bold text-dark mb-3">¡Entorno Listo!</h1>
                <p class="lead text-muted mb-4">CodeIgniter 4 y Bootstrap están perfectamente integrados.</p>
                <button class="btn btn-custom btn-lg px-5 rounded-pill">Comenzar a maquetar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>