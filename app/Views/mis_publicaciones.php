<?php


// Extraer datos del usuario para evitar errores de analisis estatico
$nombre_usuario = (string) ($usuario['nombre_usuario'] ?? '');
$apellido_usuario = (string) ($usuario['apellido_usuario'] ?? '');
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Univia — Mi Panel</title>

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link href="<?= base_url('Public/css/mis_publicaciones.css') ?>" rel="stylesheet">
</head>
<body> 
  
<nav class="univia-navbar">
    <div class="container-lg">
        <div class="d-flex align-items-center gap-3">
           <a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>" class="brand-name">Univia</a>

            <div class="ms-auto d-flex align-items-center gap-2">

                <div class="dropdown">
               <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
    <?= strtoupper(substr($nombre_usuario, 0, 1) . substr($apellido_usuario, 0, 1)) ?>
</div>

<ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:240px;">
    <li>
        <span class="dropdown-header">
            <?= esc($nombre_usuario) . ' ' . esc($apellido_usuario) ?>
        </span>
    </li>
                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item">
                                <i class="bi bi-person-circle"></i> Mi Perfil
                            </a>
                        </li>
                        <li>
                         <a class="dropdown-item" href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>">
                                <i class="bi bi-folder2-open"></i> Mis Publicaciones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item">
                                <i class="bi bi-gear"></i> Configuración
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>
                         <li>
                            <a class="dropdown-item danger" href="<?= site_url('auth/logout/' . ($usuario['dni_usuario'] ?? '')) ?>" style="color:var(--danger);">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div></div>
        </div>
    </div>
</nav>


<section class="page-hero">
    <div class="container-lg">

       <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h1>Hola, <?= esc($nombre_usuario) ?></h1>
                <p class="subtitle mb-0">
                    Gestioná tus materiales y compartí conocimiento con la comunidad universitaria.
                </p>
            </div>

            <div class="d-flex flex-column gap-2">

                <a href="<?= site_url('publicaciones/crear') ?>" class="btn btn-nueva">
                    <i class="bi bi-plus-lg"></i>
                    <span class="btn-nueva-lbl">Nueva Publicación</span>
                </a>

                <a href="<?= site_url('publicaciones/explorar') ?>" class="btn btn-nueva">
                    <i class="bi bi-search"></i>
                    <span class="btn-nueva-lbl">Buscar Publicaciones</span>
                </a>
            </div>
        </div>

        <div class="row g-3 mt-3">
        <div class="col-6 col-md-4 col-lg-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(91,127,255,.12);">
                        <i class="bi bi-file-earmark-text" style="color:var(--accent);"></i>
                    </div>
                    <div>
                        <div class="stat-value">12</div>
                        <div class="stat-label">Mis publicaciones</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(52,211,153,.12);">
                        <i class="bi bi-download" style="color:var(--success);"></i>
                    </div>
                    <div>
                        <div class="stat-value">84</div>
                        <div class="stat-label">Descargas totales</div>
                    </div>
                </div>
            </div>
         
        </div>

    </div>
</section>
<?php if (session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" 
         style="background-color: rgba(91, 127, 255, 0.1); color: var(--accent); border-radius: 12px; border-left: 5px solid var(--accent) !important;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3" style="font-size: 1.2rem;"></i>
            <div>
                <span class="fw-bold" style="font-family: 'Syne', sans-serif;">¡Excelente!</span><br>
                <?= session()->getFlashdata('mensaje') ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: var(--close-filter);"></button>
    </div>
<?php endif; ?>

<main class="container-lg py-4">

    <form class="d-md-none mb-3" action="<?= site_url('publicaciones/explorar') ?>" method="GET">
        <div class="search-wrap" style="max-width:100%;">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control search-input"
                   placeholder="Buscar materiales…" autocomplete="off">
        </div>
    </form>

    <div class="section-header">
        <h2>Mis Publicaciones</h2>
        <div class="filter-pills">
            <button class="filter-pill active" data-filter="todos">Todos</button>
            <button class="filter-pill" data-filter="resumen">Resúmenes</button>
            <button class="filter-pill" data-filter="apunte">Apuntes</button>
            <button class="filter-pill" data-filter="examen">Exámenes</button>
            <button class="filter-pill" data-filter="libro">Libros</button>
            <button class="filter-pill" data-filter="guia">Guías</button>
        </div>
    </div>

    <div class="row g-3" id="pub-grid">
<?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $pub): ?>
        <?php 
            // 1. Preparar variables seguras
            $tipo_recurso = (string) esc($pub['tipo_recurso'] ?? 'otro');
            $tipo_acuerdo = (string) esc($pub['tipo_acuerdo'] ?? 'gratis');
            $estado_texto = (isset($pub['estado']) && ($pub['estado'] == 1 || $pub['estado'] == 'activo')) ? 'activo' : 'inactivo';
            $estado_clase = $estado_texto === 'activo' ? 'status-active' : 'status-inactive';
            
            // 2. Lógica del archivo y su vista previa
            $nombre_archivo = (string) esc($pub['file_name'] ?? '');
            $ruta_preview = !empty($pub['ruta']) ? site_url('publicaciones/preview/' . $pub['id_publicacion']) : '';
            $formato = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            $is_image = in_array($formato, ['jpg', 'jpeg', 'png', 'webp']);
            
            // 3. Variables para datos de la publicación
            $id_publicacion = (string) esc($pub['id_publicacion'] ?? '');
            $titulo = (string) esc($pub['titulo'] ?? '');
            $descripcion = (string) esc($pub['descripcion'] ?? '');
            $precio = (string) esc($pub['precio'] ?? 0);
            $nombre_materia = (string) esc($pub['nombre_materia'] ?? 'Sin materia');
            $fecha_publicacion = (string) esc($pub['fecha_publicacion'] ?? '');
            
            $preview_icon = 'bi-file-earmark';
            $preview_color = 'var(--text-muted)';
            $preview_text = 'Archivo';

            if ($formato === 'pdf') {
                $preview_icon = 'bi-file-earmark-pdf'; $preview_color = '#ef4444'; $preview_text = 'PDF';
            } elseif (in_array($formato, ['doc', 'docx'])) {
                $preview_icon = 'bi-file-earmark-word'; $preview_color = '#2563eb'; $preview_text = 'Word';
            } elseif (in_array($formato, ['jpg', 'jpeg', 'png', 'webp'])) {
                $preview_icon = 'bi-image'; $preview_color = 'var(--success)'; $preview_text = 'Imagen';
            }
        ?>

        <div class="col-12 col-sm-6 col-lg-4" data-tipo="<?= $tipo_recurso ?>">
            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="<?= $id_publicacion ?>"
                 data-titulo="<?= $titulo ?>"
                 data-descripcion="<?= $descripcion ?>"
                 data-tipo-recurso="<?= $tipo_recurso ?>"
                 data-tipo-acuerdo="<?= $tipo_acuerdo ?>"
                data-precio="<?= $precio ?>"
                 data-estado="<?= $estado_texto ?>"
                 data-materia="<?= $nombre_materia ?>"
                 data-fecha="<?= $fecha_publicacion ?>"
                 data-nombre-archivo="<?= $nombre_archivo ?>"
                 data-nombre-imagen=""
                 data-es-libro-fisico="0"
                 data-descargas="0" 
                 data-url-imagen="<?= $is_image ? $ruta_preview : '' ?>"
                 data-url-archivo="<?= $ruta_preview ?>">

                <div class="card-preview">
                    <i class="bi <?= $preview_icon ?> card-preview-icon" style="color:<?= $preview_color ?>;"></i>
                    <span class="preview-pill"><i class="bi <?= $preview_icon ?> me-1"></i><?= $preview_text ?></span>
                </div>

                <div class="card-body-inner">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge-tipo badge-<?= $tipo_recurso ?>"><?= ucfirst($tipo_recurso) ?></span>
                        <span class="badge-acuerdo badge-<?= $tipo_acuerdo ?>">
                            <i class="bi <?= $tipo_acuerdo == 'gratis' ? 'bi-gift' : 'bi-currency-dollar' ?>"></i><?= ucfirst($tipo_acuerdo) ?>
                        </span>
                    </div>

                    <h3 class="pub-card-title"><?= $titulo ?></h3>
                    
                    <div class="pub-card-materia">
                        <i class="bi bi-mortarboard"></i>
                        <span><?= $nombre_materia ?></span>
                    </div>
                    
                    <p class="pub-card-desc"><?= $descripcion ?></p>
                    
                    <div class="pub-card-footer">
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-dot <?= $estado_clase ?>"></span>
                            <span><?= ucfirst($estado_texto) ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span><i class="bi bi-download"></i> 0</span>
                            <button class="btn btn-ver-card">Ver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12 empty-state">
        <i class="bi bi-folder-x empty-icon"></i>
        <h4>Aún no tenés publicaciones</h4>
        <p>Hacé clic en "Nueva Publicación" para empezar a subir material.</p>
    </div>
<?php endif; ?>
    </div> </main>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header d-flex flex-wrap align-items-start gap-3">
                <div class="flex-grow-1">
                    <div id="modal-badges" class="d-flex gap-1 mb-1"></div>
                    <h5 class="modal-title" id="modal-titulo">—</h5>
                    <div style="font-size: .8rem; color: var(--text-muted); margin-top:4px;">
                        <i class="bi bi-mortarboard me-1"></i><span id="modal-materia">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                
                <div id="modal-preview-wrap" class="modal-preview-wrap mb-4" style="display:none;"></div>

                <div class="detail-grid mb-4">
                    <div>
                        <div class="detail-label">Tipo de recurso</div>
                        <p class="detail-value" id="modal-tipo-recurso">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Tipo de acuerdo</div>
                        <p class="detail-value" id="modal-tipo-acuerdo">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Precio</div>
                        <p class="detail-value" id="modal-precio">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Estado</div>
                        <p class="detail-value" id="modal-estado">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Fecha de publicación</div>
                        <p class="detail-value" id="modal-fecha">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Nombre del archivo</div>
                        <p class="detail-value" id="modal-nombre-archivo">—</p>
                    </div>
                    <div>
                        <div class="detail-label">Nombre de la imagen</div>
                        <p class="detail-value" id="modal-nombre-imagen">—</p>
                    </div>
                </div>
                
                <div class="detail-label mb-2">Descripción completa</div>
                <div class="detail-desc-box" id="modal-descripcion">—</div>

                <div class="mt-3" id="modal-descarga-wrap" style="display:none;">
                    <a href="#" id="modal-btn-descargar" class="btn-descargar" target="_blank">
                        <i class="bi bi-download"></i> Descargar archivo
                    </a>
                </div>
            </div>

            <div class="modal-footer gap-2 justify-content-between flex-wrap">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-cerrar-modal" data-bs-dismiss="modal">Cerrar</button>
                    <a href="#" id="modal-btn-eliminar" class="btn btn-eliminar">
                        <i class="bi bi-trash3"></i> Eliminar
                    </a>
                </div>
                <a href="#" id="modal-btn-editar" class="btn btn-editar">
                    <i class="bi bi-pencil-square"></i> Editar Publicación
                </a>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const site_url = "<?= site_url() ?>";
</script>
<script src="<?= base_url('Public/js/mis_publicaciones.js') ?>"></script>
</body>
</html>  