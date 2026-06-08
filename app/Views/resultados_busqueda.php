<?php
/**
 * Muestra los resultados de búsqueda de publicaciones
 */

// Extraer datos del usuario para evitar errores en el analisis estatico
$nombre_usuario = (string) ($usuario['nombre_usuario'] ?? '');
$apellido_usuario = (string) ($usuario['apellido_usuario'] ?? '');
?> 

<!DOCTYPE html>
 
<html lang="es" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resultados de búsqueda — Univia</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= base_url('Public/css/resultados_busqueda.css') ?>" rel="stylesheet">

</head>

<body>



<nav class="navbar navbar-dark px-4">
    <a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>" class="navbar-brand">Univia</a>

    <form action="<?= site_url('publicaciones/explorar') ?>" method="GET" class="d-flex w-50">
        <input type="text" name="q" class="form-control me-2" placeholder="Buscar materiales...">
        <button class="btn btn-primary">Buscar</button>
    </form>

    <span><?= esc($nombre_usuario) ?></span>

</nav>


<div class="container mt-4">
    <h2 class="mb-3">Resultados de búsqueda</h2>
 
<!-- resultados -->
<div class="row g-3">

<?php if (!empty($resultados)): ?>

    <?php foreach ($resultados as $r): ?>

        <?php
        $id_publicacion = (int) ($r['id_publicacion'] ?? 0);
        $titulo = esc((string) ($r['titulo'] ?? ''));
        $descripcion = esc((string) ($r['descripcion'] ?? ''));
        $materia = esc((string) ($r['nombre_materia'] ?? 'Sin materia'));
        $tipo = esc((string) ($r['tipo_recurso'] ?? 'otro'));
        $archivo = esc((string) ($r['file_name'] ?? ''));
        ?>

        <div class="col-md-4">
            <div class="pub-card">

                <h5><?= $titulo ?></h5>

                <small class="text-muted">
                    <i class="bi bi-mortarboard"></i>
                    <?= $materia ?>
                </small>

                <p class="mt-2"><?= $descripcion ?></p>

                <div class="d-flex justify-content-between align-items-center mt-3">

                    <span class="badge bg-primary">
                        <?= ucfirst($tipo) ?>
                    </span>

                    <?php if (!empty($archivo) && $id_publicacion > 0): ?>
                        <a href="<?= site_url('publicaciones/descargar/' . $id_publicacion) ?>" 
                           class="btn btn-sm btn-success" target="_blank">
                            <i class="bi bi-download"></i>
                        </a>
                    <?php endif; ?>

                </div>

            </div>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <!-- no hay resultados -->
    <div class="col-12 text-center mt-5">
        <i class="bi bi-search" style="font-size:50px; opacity:.3;"></i>
        <h4 class="mt-3">No se encontraron resultados</h4>
        <p>Intentá con otros términos de búsqueda</p>
    </div>

<?php endif; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
