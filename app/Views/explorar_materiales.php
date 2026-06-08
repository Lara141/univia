<?php

/**
 * Vista: Explorar Materiales
 *
 * Permite a los estudiantes:
 * - Buscar publicaciones mediante palabras clave.
 * - Filtrar por materia.
 * - Filtrar por tipo de recurso.
 * - Filtrar por disponibilidad.
 * - Filtrar por formato.
 * - Visualizar el detalle de una publicación.
 * - Descargar archivos cuando corresponda.
 */

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
    <link rel="stylesheet" href="<?= base_url('Public/css/explorar_materiales.css') ?>">
    
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
                            <div class="theme-row" id="theme-row">
                                <div class="theme-row-left">
                                    <i class="bi bi-moon-stars-fill t-icon" id="t-icon"></i>
                                    <span id="t-label">Modo nocturno</span>
                                </div>
                                <label class="t-switch" title="Cambiar tema" onclick="event.stopPropagation()">
                                    <input type="checkbox" id="t-checkbox">
                                    <span class="t-track"></span>
                                </label>
                            </div>
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
                            <a class="dropdown-item danger" href="<?= site_url('auth/logout') ?>" style="color:var(--danger);">
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
            <h1>Explora los materiales de la comunidad</h1>
                <p class="subtitle mb-0">
                    Todo lo que necesitás para tu carrera, compartido por estudiantes.
                </p>
            </div>

   <a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>" class="btn btn-nueva">
                <i class="bi bi-arrow-left"></i>
                <span class="btn-nueva-lbl">Mis publicaciones</span>
            </a>
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

    <div class="search-box-big mb-4">
    <form id="formFiltros" action="<?= site_url('publicaciones/explorar') ?>"method="GET">

        <div class="input-group search-group">

            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>

            <input 
                type="text"
                name="q"
                class="form-control search-big-input"
                placeholder="¿Qué estás buscando hoy? (ej. Álgebra, Resumen...)"
                value="<?= esc($filtros['palabra_clave'] ?? '') ?>"
            >

            <button class="btn btn-buscar" type="submit">
                Buscar
            </button>

        </div>

    </form>
</div>
    <form class="d-md-none mb-3" action="<?= site_url('publicaciones/explorar') ?>" method="GET">
        <div class="search-wrap" style="max-width:100%;">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control search-input"
                   placeholder="Buscar materiales…" autocomplete="off">
        </div>
    </form>

    <?php
    $currentFilters = $_GET;

    function filtroUrl($campo, $valor, $currentFilters)
    {
        $params = $currentFilters;

        if (empty($valor)) {
            unset($params[$campo]);
        } else {
            $params[$campo] = $valor;
        }

        return site_url('publicaciones/explorar?' . http_build_query($params));
    }
    ?>

    <!-- FILTROS -->
    <div class="row">
        <!--<div class="col-lg-3"-->
            <div class="col-lg-3 align-self-start">

            <div class="pub-card filtros-card p-3">

                <h5 class="mb-4">Filtros</h5>

                <!-- TIPO DE RECURSO -->
                <div class="mb-4">

                    <h6 class="filtro-titulo">
                        Tipo de recurso
                    </h6>

                    <div class="d-flex flex-wrap gap-2">

                        <a href="<?= filtroUrl('tipo', '', $currentFilters) ?>"
                        class="filter-pill w-48 <?= empty($filtros['tipo']) ? 'active' : '' ?>">
                            Todos
                        </a>
                        <a href="<?= filtroUrl('tipo', 'resumen', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['tipo'] ?? '') == 'resumen' ? 'active' : '' ?>">
                            Resúmenes
                        </a>
                        <a href="<?= filtroUrl('tipo', 'examen', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['tipo'] ?? '') == 'examen' ? 'active' : '' ?>">
                            Exámenes
                        </a>
                        <a href="<?= filtroUrl('tipo', 'libro', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['tipo'] ?? '') == 'libro' ? 'active' : '' ?>">
                            Libros
                        </a>
                        <a href="<?= filtroUrl('tipo', 'guia', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['tipo'] ?? '') == 'guia' ? 'active' : '' ?>">
                            Guías
                        </a>
                        <a href="<?= filtroUrl('tipo', 'otro', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['tipo'] ?? '') == 'otro' ? 'active' : '' ?>">
                            Otros
                        </a>

                    </div>

                </div>

                <hr class="filtro-divider">

                <!-- DISPONIBILIDAD -->
                <div class="my-4">

                    <h6 class="filtro-titulo">
                        Disponibilidad
                    </h6>

                    <div class="d-flex flex-wrap gap-2">

                        <a href="<?= filtroUrl('acuerdo', 'gratis', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['acuerdo'] ?? '') == 'gratis' ? 'active' : '' ?>">
                            Gratis
                        </a>
                    <a href="<?= filtroUrl('acuerdo', '', $currentFilters) ?>"
                        class="filter-pill w-48 <?= empty($filtros['acuerdo']) ? 'active' : '' ?>">
                            Todos
                        </a>
                        <a href="<?= filtroUrl('acuerdo', 'pago', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['acuerdo'] ?? '') == 'pago' ? 'active' : '' ?>">
                            De pago
                        </a>

                    </div>

                </div>

                <hr class="filtro-divider">

                <!-- FORMATO -->
                <div class="mt-4">

                    <h6 class="filtro-titulo">
                        Formato
                    </h6>

                    <div class="d-flex flex-wrap gap-2">

                        <a href="<?= filtroUrl('formato', '', $currentFilters) ?>"
                        class="filter-pill w-48 <?= empty($filtros['formato']) ? 'active' : '' ?>">
                            Todos
                        </a>
                        <a href="<?= filtroUrl('formato', 'pdf', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['formato'] ?? '') == 'pdf' ? 'active' : '' ?>">
                            PDF
                        </a>
                        <a href="<?= filtroUrl('formato', 'docx', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['formato'] ?? '') == 'docx' ? 'active' : '' ?>">
                            Word
                        </a>
                    <a href="<?= filtroUrl('formato', 'jpeg', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['formato'] ?? '') == 'jpeg' ? 'active' : '' ?>">
                            PNG / JPG
                        </a>
                        <a href="<?= filtroUrl('formato', 'pptx', $currentFilters) ?>"
                        class="filter-pill w-48 <?= ($filtros['formato'] ?? '') == 'pptx' ? 'active' : '' ?>">
                            pptx
                        </a>

                    </div>

                </div>
           
        </div>

    </div>
  
    <!-- RESULTADOS -->
    <div class="col-lg-9">

        <div class="section-header mb-3">
            <h2>
                <?php if (!empty($busqueda)): ?>
                    Resultados para "<?= esc($busqueda) ?>"
                <?php else: ?>
                    Explorá todos los materiales disponibles
                <?php endif; ?>
            </h2>
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
            $ruta_archivo = !empty($pub['ruta']) ? base_url($pub['ruta']) : '';
            $formato = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
            
            // 3. Variables para datos de la publicación
            $id_publicacion = (string) esc($pub['id_publicacion'] ?? '');
            $titulo = (string) esc($pub['titulo'] ?? '');
            $descripcion = (string) esc($pub['descripcion'] ?? '');
            $precio = (string) esc($pub['precio'] ?? 0);
            $nombre_materia = (string) esc($pub['nombre_materia'] ?? 'Sin materia');
            $fecha_publicacion = (string) esc($pub['fecha_publicacion'] ?? '');
            
            // Esto es para que me salten los datos del autorrr
$autor_nombre = (string) esc($pub['Nombre_usuario'] ?? 'Estudiante');
$autor_apellido = (string) esc($pub['Apellido_usuario'] ?? 'Anónimo');
$autor_completo = $autor_nombre . ' ' . $autor_apellido;

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

        <div class="col-12 col-sm-6 col-lg-4"
        data-tipo="<?= strtolower($tipo_recurso) ?>"
        data-acuerdo="<?= strtolower($tipo_acuerdo) ?>"
        data-formato="<?= strtolower($formato) ?>">

            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="<?= $id_publicacion ?>"
                 data-titulo="<?= $titulo ?>"
                 data-autor="<?= $autor_completo ?>"
                 data-descripcion="<?= $descripcion ?>"
                 data-tipo-recurso="<?= $tipo_recurso ?>"
                 data-tipo-acuerdo="<?= $tipo_acuerdo ?>"
                 data-pagado="<?= ($pub['ya_pagado'] ?? false) ? '1' : '0' ?>"
                data-precio="<?= $precio ?>"
                 data-estado="<?= $estado_texto ?>"
                 data-materia="<?= $nombre_materia ?>"
                 data-fecha="<?= $fecha_publicacion ?>"
                 data-nombre-archivo="<?= $nombre_archivo ?>"
                 data-nombre-imagen=""
                 data-es-libro-fisico="0"
                 data-descargas="0"
                 data-url-imagen=""
                 data-url-archivo="<?= $ruta_archivo ?>">

                <div class="card-preview">
                    <i class="bi <?= $preview_icon ?> card-preview-icon" style="color:<?= $preview_color ?>;"></i>
                    <span class="preview-pill"><i class="bi <?= $preview_icon ?> me-1"></i><?= $preview_text ?></span>
                </div>

                <div class="card-body-inner">
                                   <!-- PAra los datos del autorrrr -->
                    <div class="pub-card-materia" style="margin-top: -2px; margin-bottom: .6rem;">
                        <i class="bi bi-person-circle"></i>
                        <span>Subido por: <?= $autor_completo ?></span>
                    </div>
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
        <h4>No hay resultados para esta búsqueda</h4>
        <p>
            Probá modificando los filtros o usando palabras clave más generales.
        </p>
    </div>
<?php endif; ?>
           </div>
    </div>
</div>

</main>

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
                        <div class="detail-label">Autor</div>
                        <p class="detail-value" id="modal-autor">—</p>
                    </div>
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
            </div>

            <div class="modal-footer gap-2 justify-content-between flex-wrap">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius:9px; padding: 8px 16px;">Cerrar</button>
                </div>
                
                <div class="d-flex gap-2" id="modal-acciones-wrap">
                    <div id="modal-descarga-wrap" style="display:none;">
                        <a href="#" id="modal-btn-descargar" class="btn-descargar" target="_blank">
                            <i class="bi bi-download"></i> Descargar archivo
                        </a>
                    </div>
                    
                    <button type="button" id="modal-btn-pagar" class="btn btn-warning btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalPagoSimulado" style="display:none; border-radius:9px; padding: 8px 16px;">
                        <i class="bi bi-credit-card-2-front-fill me-1"></i> Realizar Pago
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalPagoSimulado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg"
             style="background: var(--bg-card);">

            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-warning fw-bold">
                    <i class="bi bi-credit-card-fill me-2"></i>
                    Confirmar Compra
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form id="form-pago-simulado" method="POST" action="">

                <div class="modal-body">

                    <!-- RESUMEN DE COMPRA -->

                    <div class="card mb-4 border-warning">
                        <div class="card-body">

                            <h6 class="fw-bold text-warning mb-3">
                                Resumen de la Compra
                            </h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Material:</span>
                                <span id="pago-titulo-material">
                                    
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Autor:</span>
                                <span id="pago-autor-material">
                                
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-success"
                                        id="pago-precio-material">
                                    
                                </strong>
                            </div>

                        </div>
                    </div>

                    <!-- MÉTODO DE PAGO -->

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Método de Pago
                        </label>

                        <select name="metodo_pago"
                                class="form-select"
                                required>

                            <option value="">Seleccionar...</option>
                            <option value="Visa">Visa</option>
                            <option value="Mastercard">Mastercard</option>
                            <option value="American Express">American Express</option>

                        </select>
                    </div>

                    <!-- TITULAR -->

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Titular de la Tarjeta
                        </label>

                        <input type="text"
                               name="titular"
                               class="form-control"
                               placeholder="Nombre y Apellido"
                               required>
                    </div>

                    <!-- TARJETA -->

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Número de Tarjeta
                        </label>

                        <input type="text"
                               name="tarjeta"
                               class="form-control"
                               maxlength="19"
                               pattern="[0-9 ]{19}"
                               placeholder="1234 5678 9012 3456"
                               required>
                    </div>

                    <!-- VENCIMIENTO + CVV -->

                    <div class="row">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Vencimiento
                            </label>

                            <input type="text"
                                   name="vencimiento"
                                   class="form-control"
                                   maxlength="5"
                                   pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                                   placeholder="MM/AA"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                CVV
                            </label>

                            <input type="password"
                                   name="cvv"
                                   class="form-control"
                                   maxlength="3"
                                   pattern="[0-9]{3}"
                                   placeholder="123"
                                   required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer border-top border-secondary">

                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail">
                        Volver
                    </button>

                    <button type="submit"
                            class="btn btn-warning fw-bold text-dark">

                         <i class="bi bi-lock-fill me-1"></i>
                        Confirmar Transacción
                    </button>
                </div>
            </form>
        </div> 
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const site_url = "<?= site_url() ?>";
</script>
<script src="<?= base_url('Public/js/explorar_materiales.js') ?>"></script>

</body>
</html>