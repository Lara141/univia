<?php
/**
 * UNIVIA - Vista: Dashboard / Panel del Usuario
 * Archivo: application/views/dashboard.php
 *
 * Datos que debe inyectar el controlador (ej. Dashboard.php):
 *   $this->load->view('dashboard', [
 *       'usuario'       => $usuario,        // array con datos del usuario logueado
 *       'publicaciones' => $publicaciones,  // array de publicaciones del usuario
 *       'total_pubs'    => $total_pubs,     // int: total de publicaciones
 *   ]);
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Univia — Mi Panel</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Syne (display) + DM Sans (cuerpo) -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           VARIABLES Y TOKENS DE DISEÑO
        ============================================= */
        :root {
            --uni-dark:      #0d0f1a;
            --uni-surface:   #131629;
            --uni-card:      #1a1e35;
            --uni-card-alt:  #1e2340;
            --uni-border:    rgba(255,255,255,0.07);
            --uni-accent:    #5b7fff;
            --uni-accent-2:  #8b5cf6;
            --uni-accent-3:  #38bdf8;
            --uni-success:   #34d399;
            --uni-warn:      #fbbf24;
            --uni-text:      #e2e8f0;
            --uni-muted:     #64748b;
            --uni-gradient:  linear-gradient(135deg, #5b7fff 0%, #8b5cf6 100%);
            --uni-glow:      0 0 40px rgba(91,127,255,0.18);
        }

        /* =============================================
           BASE
        ============================================= */
        * { box-sizing: border-box; }

        body {
            background-color: var(--uni-dark);
            color: var(--uni-text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, .brand-name {
            font-family: 'Syne', sans-serif;
        }

        a { text-decoration: none; }

        /* =============================================
           NAVBAR
        ============================================= */
        .univia-navbar {
            background: var(--uni-surface);
            border-bottom: 1px solid var(--uni-border);
            padding: 0.6rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: blur(12px);
        }

        .brand-name {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--uni-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-dot {
            display: inline-block;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--uni-accent);
            margin-left: 1px;
            vertical-align: middle;
            -webkit-text-fill-color: initial;
            box-shadow: 0 0 8px var(--uni-accent);
        }

        /* Barra de búsqueda */
        .search-wrap { position: relative; max-width: 380px; width: 100%; }

        .search-wrap .bi-search {
            position: absolute;
            left: 12px; top: 50%;
            transform: translateY(-50%);
            color: var(--uni-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .search-input {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid var(--uni-border) !important;
            color: var(--uni-text) !important;
            border-radius: 10px !important;
            padding-left: 2rem !important;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .2s, box-shadow .2s;
            font-size: 0.88rem;
        }
        .search-input::placeholder { color: var(--uni-muted); }
        .search-input:focus {
            border-color: var(--uni-accent) !important;
            box-shadow: 0 0 0 3px rgba(91,127,255,0.15) !important;
            outline: none;
            background: rgba(91,127,255,0.06) !important;
        }

        /* Avatar y dropdown */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--uni-gradient);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            cursor: pointer;
            border: 2px solid rgba(91,127,255,0.4);
        }

        .dropdown-menu {
            background: var(--uni-card-alt);
            border: 1px solid var(--uni-border);
            border-radius: 12px;
            padding: 6px;
            min-width: 200px;
        }
        .dropdown-item {
            color: var(--uni-text);
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 0.88rem;
            display: flex; align-items: center; gap: 9px;
            transition: background .15s;
        }
        .dropdown-item:hover { background: rgba(91,127,255,0.12); color: #fff; }
        .dropdown-item.text-danger:hover { background: rgba(239,68,68,0.12); color: #f87171; }
        .dropdown-divider { border-color: var(--uni-border); margin: 4px 0; }
        .dropdown-header {
            color: var(--uni-muted);
            font-size: 0.78rem;
            padding: 6px 14px;
            font-family: 'Syne', sans-serif;
            letter-spacing: 0.05em;
        }

        /* =============================================
           HERO / ENCABEZADO DE SECCIÓN
        ============================================= */
        .page-hero {
            background: var(--uni-surface);
            border-bottom: 1px solid var(--uni-border);
            padding: 2.2rem 0 2rem;
        }

        .page-hero h1 {
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
            letter-spacing: -0.5px;
        }

        .page-hero .subtitle {
            color: var(--uni-muted);
            font-size: 0.92rem;
        }

        /* Botón principal "Nueva Publicación" */
        .btn-nueva {
            background: var(--uni-gradient);
            border: none;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            padding: 10px 22px;
            border-radius: 10px;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 20px rgba(91,127,255,0.35);
            transition: transform .15s, box-shadow .15s, filter .15s;
            display: inline-flex;
            align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .btn-nueva:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(91,127,255,0.5);
            filter: brightness(1.08);
            color: #fff;
        }
        .btn-nueva:active { transform: translateY(0); }

        /* =============================================
           STATS RÁPIDAS
        ============================================= */
        .stat-chip {
            background: var(--uni-card);
            border: 1px solid var(--uni-border);
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-chip .icon-wrap {
            width: 38px; height: 38px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .stat-chip .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-chip .stat-label {
            font-size: 0.78rem;
            color: var(--uni-muted);
            margin-top: 1px;
        }

        /* =============================================
           CARDS DE PUBLICACIONES
        ============================================= */
        .pub-card {
            background: var(--uni-card);
            border: 1px solid var(--uni-border);
            border-radius: 14px;
            padding: 1.35rem 1.4rem;
            transition: border-color .2s, box-shadow .2s, transform .2s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .pub-card:hover {
            border-color: rgba(91,127,255,0.35);
            box-shadow: var(--uni-glow);
            transform: translateY(-2px);
        }

        /* Badge de categoría/materia */
        .materia-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.4px;
            font-family: 'Syne', sans-serif;
            display: inline-block;
        }
        /* Colores de badge según tipo — puedes ampliar este mapa en PHP */
        .badge-apuntes   { background: rgba(91,127,255,0.15); color: #7ca0ff; border: 1px solid rgba(91,127,255,0.2); }
        .badge-examen    { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .badge-libro     { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.2); }
        .badge-guia      { background: rgba(56,189,248,0.12); color: #38bdf8; border: 1px solid rgba(56,189,248,0.2); }
        .badge-otro      { background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.2); }

        .pub-card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.4rem;
            line-height: 1.3;
        }

                .pub-card-desc {
            color: var(--uni-muted);
            font-size: 0.84rem;
            line-height: 1.55;
            flex: 1;
            /* Limitar a 3 líneas */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pub-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--uni-border);
            font-size: 0.8rem;
            color: var(--uni-muted);
        }

        .pub-card-footer .date { display: flex; align-items: center; gap: 5px; }

        /* Estado (visible/oculto) */
        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
        .status-active   { background: var(--uni-success); box-shadow: 0 0 6px var(--uni-success); }
        .status-inactive { background: var(--uni-muted); }

        .btn-ver-card {
            font-size: 0.78rem;
            padding: 4px 12px;
            border-radius: 7px;
            background: transparent;
            border: 1px solid var(--uni-border);
            color: var(--uni-text);
            transition: background .15s, border-color .15s, color .15s;
        }
        .btn-ver-card:hover {
            background: rgba(91,127,255,0.12);
            border-color: var(--uni-accent);
            color: var(--uni-accent);
        }

        /* =============================================
           EMPTY STATE
        ============================================= */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--uni-muted);
        }
        .empty-state .empty-icon {
            font-size: 3.5rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }
        .empty-state p { max-width: 320px; margin: 0 auto 1.5rem; font-size: 0.9rem; }

        /* =============================================
           SECCIÓN HEADER (título + filtros)
        ============================================= */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .section-header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
        .filter-pill {
            font-size: 0.78rem;
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid var(--uni-border);
            background: transparent;
            color: var(--uni-muted);
            cursor: pointer;
            transition: all .15s;
            font-family: 'DM Sans', sans-serif;
        }
        .filter-pill:hover, .filter-pill.active {
            background: rgba(91,127,255,0.12);
            border-color: var(--uni-accent);
            color: var(--uni-accent);
        }

        /* =============================================
           MODAL DE DETALLE
        ============================================= */
        .modal-content {
            background: var(--uni-card);
            border: 1px solid var(--uni-border);
            border-radius: 18px;
            color: var(--uni-text);
        }
        .modal-header {
            border-bottom: 1px solid var(--uni-border);
            padding: 1.4rem 1.6rem 1rem;
        }
        .modal-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
        }
        .modal-footer {
            border-top: 1px solid var(--uni-border);
            padding: 1rem 1.6rem;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--uni-muted);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-family: 'Syne', sans-serif;
        }
        .detail-value {
            font-size: 0.92rem;
            color: var(--uni-text);
            margin-bottom: 0;
        }
        .detail-desc {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--uni-border);
            border-radius: 10px;
            padding: 1rem;
            font-size: 0.9rem;
            color: var(--uni-text);
            line-height: 1.65;
            white-space: pre-wrap;
        }

        .btn-editar {
            background: var(--uni-gradient);
            border: none;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 10px 22px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(91,127,255,0.3);
            transition: filter .15s, box-shadow .15s;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-editar:hover { filter: brightness(1.08); box-shadow: 0 6px 22px rgba(91,127,255,0.45); color: #fff; }

        .btn-eliminar {
            background: transparent;
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            font-size: 0.88rem;
            padding: 9px 18px;
            border-radius: 10px;
            transition: background .15s, border-color .15s;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-eliminar:hover { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.5); color: #f87171; }

        .btn-modal-cerrar {
            background: transparent;
            border: 1px solid var(--uni-border);
            color: var(--uni-muted);
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 0.88rem;
            transition: background .15s, color .15s;
        }
        .btn-modal-cerrar:hover { background: rgba(255,255,255,0.05); color: var(--uni-text); }

        /* Íconos de tipo en modal */
        .file-type-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            background: var(--uni-gradient);
            flex-shrink: 0;
        }

        /* =============================================
           SCROLLBAR
        ============================================= */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

        /* =============================================
           RESPONSIVE TWEAKS
        ============================================= */
        @media (max-width: 576px) {
            .page-hero h1 { font-size: 1.5rem; }
            .btn-nueva span { display: none; }
        }
    </style>
</head>
<body>

<!-- ================================================
     NAVBAR
================================================ -->
<nav class="univia-navbar">
    <div class="container-lg">
        <div class="d-flex align-items-center gap-3">

            <!-- Logotipo -->
            <!-- RUTA: Ajusta el href a base_url('dashboard') de tu controlador -->
            <a href="<?= base_url('dashboard') ?>" class="brand-name d-flex align-items-center gap-1 me-2">
                Univia<span class="brand-dot"></span>
            </a>

            <!-- Barra de búsqueda -->
            <!-- RUTA: El action apunta al método buscar() de tu controlador Materiales -->
            <form class="flex-grow-1 search-wrap d-none d-md-block"
                  action="<?= base_url('materiales/buscar') ?>" method="GET">
                <i class="bi bi-search"></i>
                <input type="search"
                       name="q"
                       class="form-control search-input"
                       placeholder="Buscar materiales, materias, carreras…"
                       autocomplete="off">
            </form>

            <div class="ms-auto d-flex align-items-center gap-3">

                <!-- Notificaciones (placeholder) -->
                <button class="btn btn-link p-0 text-muted" title="Notificaciones" style="font-size:1.1rem;">
                    <i class="bi bi-bell"></i>
                </button>

                <!-- Perfil / Dropdown -->
                <div class="dropdown">
                    <div class="user-avatar dropdown-toggle"
                         data-bs-toggle="dropdown"
                         aria-expanded="false"
                         style="list-style:none;"
                         title="Mi perfil">
                        <!--
                            DATO DINÁMICO: Iniciales del usuario
                            PHP: echo strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido'], 0, 1));
                        -->
                        VA
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <span class="dropdown-header">
                                <!--
                                    DATO DINÁMICO: Nombre completo del usuario
                                    PHP: echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']);
                                -->
                                Valentina A.
                            </span>
                        </li>
                        <li>
                            <!-- RUTA: base_url('perfil') -->
                            <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                                <i class="bi bi-person-circle"></i> Mi Perfil
                            </a>
                        </li>
                        <li>
                            <!-- RUTA: base_url('publicaciones') o similar -->
                            <a class="dropdown-item" href="<?= base_url('publicaciones') ?>">
                                <i class="bi bi-folder2-open"></i> Mis Publicaciones
                            </a>
                        </li>
                        <li>
                            <!-- RUTA: base_url('configuracion') -->
                            <a class="dropdown-item" href="<?= base_url('configuracion') ?>">
                                <i class="bi bi-gear"></i> Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <!-- RUTA: base_url('auth/cerrar_sesion') — ajusta al método de tu controlador Auth -->
                            <a class="dropdown-item text-danger" href="<?= base_url('auth/cerrar_sesion') ?>">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div><!-- /ms-auto -->
        </div><!-- /flex -->
    </div>
</nav>


<!-- ================================================
     HERO — Bienvenida + botón acción principal
================================================ -->
<section class="page-hero">
    <div class="container-lg">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

            <div>
                <h1>
                    <!--
                        DATO DINÁMICO: Saludo con nombre del usuario
                        PHP: echo 'Hola, ' . htmlspecialchars($usuario['nombre']) . ' 👋';
                    -->
                    Hola, Valentina 👋
                </h1>
                <p class="subtitle mb-0">
                    Gestioná y compartí tus materiales académicos con la comunidad.
                </p>
            </div>

            <!-- BOTÓN PRINCIPAL — ACCIÓN CENTRAL -->
            <!-- RUTA: Ajusta a base_url('publicaciones/nueva') de tu controlador -->
            <a href="<?= base_url('publicaciones/nueva') ?>" class="btn btn-nueva">
                <i class="bi bi-plus-lg"></i>
                <span>Nueva Publicación</span>
            </a>

        </div><!-- /flex -->

        <!-- Stats rápidas -->
        <div class="row g-3 mt-3">

            <div class="col-6 col-sm-4 col-md-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(91,127,255,0.12);">
                        <i class="bi bi-file-earmark-text" style="color:var(--uni-accent);"></i>
                    </div>
                    <div>
                        <div class="stat-value">
                            <!--
                                DATO DINÁMICO: Total de publicaciones del usuario
                                PHP: echo $total_pubs ?? 0;
                            -->
                            12
                        </div>
                        <div class="stat-label">Mis publicaciones</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(52,211,153,0.12);">
                        <i class="bi bi-download" style="color:var(--uni-success);"></i>
                    </div>
                    <div>
                        <div class="stat-value">
                            <!--
                                DATO DINÁMICO: Total de descargas de mis materiales
                                PHP: echo $total_descargas ?? 0;
                            -->
                            84
                        </div>
                        <div class="stat-label">Descargas totales</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(251,191,36,0.12);">
                        <i class="bi bi-star" style="color:var(--uni-warn);"></i>
                    </div>
                    <div>
                        <div class="stat-value">
                            <!--
                                DATO DINÁMICO: Calificación promedio
                                PHP: echo number_format($promedio_rating ?? 0, 1);
                            -->
                            4.7
                        </div>
                        <div class="stat-label">Valoración media</div>
                    </div>
                </div>
            </div>

        </div><!-- /row stats -->
    </div>
</section><!-- /page-hero -->


<!-- ================================================
     CONTENIDO PRINCIPAL
================================================ -->
<main class="container-lg py-4">

    <!-- Buscador móvil -->
    <form class="d-md-none mb-3"
          action="<?= base_url('materiales/buscar') ?>" method="GET">
        <div class="search-wrap" style="max-width:100%;">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control search-input"
                   placeholder="Buscar materiales…" autocomplete="off">
        </div>
    </form>

    <!-- Encabezado de sección + filtros -->
    <div class="section-header">
        <h2>Mis Publicaciones</h2>

        <!-- Filtros rápidos por tipo -->
        <!-- FUNCIONALIDAD: Podrías implementar estos filtros con JS del lado cliente
             o apuntarlos a base_url('publicaciones?tipo=X') para filtrado server-side -->
        <div class="filter-pills">
            <button type="button" class="filter-pill active" data-filter="todos">Todos</button>
            <button type="button" class="filter-pill" data-filter="apuntes">Apuntes</button>
            <button type="button" class="filter-pill" data-filter="examen">Exámenes</button>
            <button type="button" class="filter-pill" data-filter="libro">Libros</button>
            <button type="button" class="filter-pill" data-filter="guia">Guías</button>
        </div>
    </div><!-- /section-header -->


    <!-- ============================================
         GRILLA DE PUBLICACIONES
         DATO DINÁMICO: Reemplaza el bloque estático
         con un foreach de PHP:

         <?php if (!empty($publicaciones)): ?>
           <?php foreach ($publicaciones as $pub): ?>
             ... (ver estructura de la card abajo) ...
           <?php endforeach; ?>
         <?php else: ?>
           ... (empty state) ...
         <?php endif; ?>
    ============================================ -->
    <div class="row g-3" id="pub-grid">

        <!--
        ====================================================
        CARD DE PUBLICACIÓN — Repetir por cada $pub en el array
        ====================================================
        Estructura del array esperado:
          $pub['id']          → ID de la publicación
          $pub['titulo']      → Título
          $pub['materia']     → Nombre de la materia
          $pub['tipo']        → Tipo: apuntes|examen|libro|guia|otro
          $pub['descripcion'] → Texto de descripción
          $pub['fecha']       → Fecha de subida (timestamp o fecha formateada)
          $pub['activo']      → bool: visible/oculto
          $pub['descargas']   → int: cantidad de descargas
        -->

        <!-- CARD 1 (ejemplo estático) -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="apuntes">
            <div class="pub-card"
                 data-bs-toggle="modal"
                 data-bs-target="#modalDetalle"
                 data-id="1"
                 data-titulo="Apuntes Ingeniería de Software I"
                 data-materia="Ingeniería de Software I"
                 data-tipo="apuntes"
                 data-fecha="15/05/2025"
                 data-descargas="23"
                 data-activo="1"
                 data-desc="Resumen completo de todos los temas vistos durante la cursada: ciclos de vida, metodologías ágiles, casos de uso, diagramas UML y arquitecturas de software. Ideal para repasar antes del parcial.">

                <!-- Encabezado de la card -->
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <span class="materia-badge badge-apuntes">Apuntes</span>
                    <!--
                        DATO DINÁMICO: Estado activo/inactivo
                        PHP: echo $pub['activo'] ? 'status-active' : 'status-inactive';
                    -->
                    <span class="status-dot status-active" title="Visible"></span>
                </div>

                <!-- Título -->
                <!--
                    DATO DINÁMICO:
                    PHP: echo htmlspecialchars($pub['titulo']);
                -->
                <h3 class="pub-card-title">Apuntes Ingeniería de Software I</h3>

                <!-- Materia -->
                <div class="d-flex align-items-center gap-1 mb-2" style="font-size:0.8rem; color:var(--uni-muted);">
                    <i class="bi bi-book"></i>
                    <!--
                        DATO DINÁMICO:
                        PHP: echo htmlspecialchars($pub['materia']);
                    -->
                    <span>Ingeniería de Software I</span>
                </div>

                <!-- Extracto de descripción -->
                <!--
                    DATO DINÁMICO:
                    PHP: echo htmlspecialchars(substr($pub['descripcion'], 0, 180));
                -->
                <p class="pub-card-desc">
                    Resumen completo de todos los temas vistos durante la cursada: ciclos de vida, metodologías ágiles, casos de uso, diagramas UML y arquitecturas de software. Ideal para repasar antes del parcial.
                </p>

                <!-- Pie de la card -->
                <div class="pub-card-footer">
                    <div class="date">
                        <i class="bi bi-calendar3"></i>
                        <!--
                            DATO DINÁMICO:
                            PHP: echo date('d/m/Y', strtotime($pub['fecha']));
                        -->
                        <span>15/05/2025</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.78rem; color:var(--uni-muted);">
                            <i class="bi bi-download"></i>
                            <!-- PHP: echo $pub['descargas']; -->
                            23
                        </span>
                        <button class="btn btn-ver-card">Ver</button>
                    </div>
                </div>
            </div>
        </div><!-- /col -->

        <!-- CARD 2 (ejemplo) -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="examen">
            <div class="pub-card"
                 data-bs-toggle="modal"
                 data-bs-target="#modalDetalle"
                 data-id="2"
                 data-titulo="Parcial 1 Análisis Matemático II — 2024"
                 data-materia="Análisis Matemático II"
                 data-tipo="examen"
                 data-fecha="02/04/2025"
                 data-descargas="41"
                 data-activo="1"
                 data-desc="Primer parcial del año 2024, con soluciones detalladas. Incluye integrales dobles, triples y aplicaciones de series de Taylor.">

                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <span class="materia-badge badge-examen">Examen</span>
                    <span class="status-dot status-active" title="Visible"></span>
                </div>
                <h3 class="pub-card-title">Parcial 1 Análisis Matemático II — 2024</h3>
                <div class="d-flex align-items-center gap-1 mb-2" style="font-size:0.8rem; color:var(--uni-muted);">
                    <i class="bi bi-book"></i>
                    <span>Análisis Matemático II</span>
                </div>
                <p class="pub-card-desc">
                    Primer parcial del año 2024, con soluciones detalladas. Incluye integrales dobles, triples y aplicaciones de series de Taylor.
                </p>
                <div class="pub-card-footer">
                    <div class="date"><i class="bi bi-calendar3"></i><span>02/04/2025</span></div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.78rem; color:var(--uni-muted);"><i class="bi bi-download"></i> 41</span>
                        <button class="btn btn-ver-card">Ver</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3 (ejemplo) -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="guia">
            <div class="pub-card"
                 data-bs-toggle="modal"
                 data-bs-target="#modalDetalle"
                 data-id="3"
                 data-titulo="Guía de ejercicios — Programación Orientada a Objetos"
                 data-materia="POO"
                 data-tipo="guia"
                 data-fecha="10/03/2025"
                 data-descargas="20"
                 data-activo="0"
                 data-desc="Colección de ejercicios resueltos sobre POO en Java: herencia, polimorfismo, interfaces y patrones de diseño básicos.">

                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <span class="materia-badge badge-guia">Guía</span>
                    <span class="status-dot status-inactive" title="Oculta"></span>
                </div>
                <h3 class="pub-card-title">Guía de ejercicios — Programación Orientada a Objetos</h3>
                <div class="d-flex align-items-center gap-1 mb-2" style="font-size:0.8rem; color:var(--uni-muted);">
                    <i class="bi bi-book"></i>
                    <span>POO</span>
                </div>
                <p class="pub-card-desc">
                    Colección de ejercicios resueltos sobre POO en Java: herencia, polimorfismo, interfaces y patrones de diseño básicos.
                </p>
                <div class="pub-card-footer">
                    <div class="date"><i class="bi bi-calendar3"></i><span>10/03/2025</span></div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.78rem; color:var(--uni-muted);"><i class="bi bi-download"></i> 20</span>
                        <button class="btn btn-ver-card">Ver</button>
                    </div>
                </div>
            </div>
        </div>

        <!--
        ============================================
        EMPTY STATE — mostrar si no hay publicaciones
        PHP: if (empty($publicaciones)):
        ============================================
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-folder2-open"></i></div>
                <h5 style="font-family:'Syne',sans-serif; font-weight:700;">Todavía no subiste nada</h5>
                <p>Compartí tus apuntes, guías o exámenes con la comunidad universitaria.</p>
                <a href="<?= base_url('publicaciones/nueva') ?>" class="btn btn-nueva">
                    <i class="bi bi-plus-lg"></i> Subir primer material
                </a>
            </div>
        </div>
        PHP: endif;
        -->

    </div><!-- /#pub-grid -->

</main><!-- /main -->


<!-- ================================================
     MODAL DE DETALLE DE PUBLICACIÓN
     Se rellena dinámicamente con JS al hacer clic en una card.
     Para ver cómo se pasan los datos, revisar el script al final.
================================================ -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header del modal -->
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div class="file-type-icon">
                        <i class="bi bi-file-earmark-text" id="modal-tipo-icon"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div id="modal-tipo-badge" class="mb-1"></div>
                        <h5 class="modal-title" id="modalDetalleLabel">
                            <!-- PHP: echo htmlspecialchars($pub['titulo']); -->
                            Título de la publicación
                        </h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body del modal -->
            <div class="modal-body p-4">
                <div class="row g-4">

                    <!-- Columna izquierda: metadatos -->
                    <div class="col-md-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="detail-label">Materia</div>
                                <!-- PHP: echo htmlspecialchars($pub['materia']); -->
                                <p class="detail-value" id="modal-materia">—</p>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Fecha de publicación</div>
                                <!-- PHP: echo date('d/m/Y', strtotime($pub['fecha'])); -->
                                <p class="detail-value" id="modal-fecha">—</p>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Descargas</div>
                                <!-- PHP: echo $pub['descargas']; -->
                                <p class="detail-value" id="modal-descargas">—</p>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Estado</div>
                                <p class="detail-value" id="modal-estado">—</p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: descripción completa -->
                    <div class="col-md-8">
                        <div class="detail-label mb-2">Descripción completa</div>
                        <!-- PHP: echo nl2br(htmlspecialchars($pub['descripcion'])); -->
                        <div class="detail-desc" id="modal-desc">—</div>

                        <!-- Descarga del archivo (si aplica) -->
                        <div class="mt-3">
                            <!--
                                RUTA: base_url('publicaciones/descargar/' . $pub['id'])
                                Mostrar solo si el archivo está disponible para descarga
                            -->
                            <a href="#" id="modal-btn-descargar"
                               class="btn d-inline-flex align-items-center gap-2"
                               style="background:rgba(52,211,153,0.1); border:1px solid rgba(52,211,153,0.25); color:var(--uni-success); border-radius:10px; padding:8px 18px; font-size:0.88rem;">
                                <i class="bi bi-download"></i> Descargar archivo
                            </a>
                        </div>
                    </div>

                </div>
            </div><!-- /modal-body -->

            <!-- Footer del modal -->
            <div class="modal-footer gap-2 flex-wrap">
                <button type="button" class="btn btn-modal-cerrar" data-bs-dismiss="modal">
                    Cerrar
                </button>

                <!-- BOTÓN ELIMINAR -->
                <!--
                    RUTA: Apunta a base_url('publicaciones/eliminar/' . $pub['id'])
                    Se puede confirmar con JS antes de navegar
                -->
                <a href="#" id="modal-btn-eliminar" class="btn btn-eliminar">
                    <i class="bi bi-trash3"></i> Eliminar
                </a>

                <!-- BOTÓN EDITAR — Acción principal y destacada -->
                <!--
                    RUTA: Apunta a base_url('publicaciones/editar/' . $pub['id'])
                    Lleva a la vista editar_publicacion.php (ver segundo archivo entregado)
                -->
                <a href="#" id="modal-btn-editar" class="btn btn-editar">
                    <i class="bi bi-pencil-square"></i> Editar Publicación
                </a>
            </div>

        </div><!-- /modal-content -->
    </div>
</div><!-- /#modalDetalle -->


<!-- ================================================
     SCRIPTS
================================================ -->
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ================================================
   1. INYECCIÓN DE DATOS EN EL MODAL
   Lee los data-attributes de cada card y rellena el modal
================================================ */
const modalDetalle = document.getElementById('modalDetalle');

modalDetalle.addEventListener('show.bs.modal', function (e) {
    // La card que disparó el modal
    const card = e.relatedTarget;
    if (!card) return;

    // Leer datos
    const id        = card.dataset.id;
    const titulo    = card.dataset.titulo   || '—';
    const materia   = card.dataset.materia  || '—';
    const tipo      = card.dataset.tipo     || 'otro';
    const fecha     = card.dataset.fecha    || '—';
    const descargas = card.dataset.descargas || '0';
    const activo    = card.dataset.activo;
    const desc      = card.dataset.desc     || '—';

    /* -----------------------------------------------
       BASE URL de CodeIgniter
       Usamos base_url() desde el servidor para evitar problemas
       con subdirectorios en entornos como XAMPP.
    ----------------------------------------------- */
    const BASE_URL = "<?= rtrim(base_url(), '/') . '/' ?>";

    // Mapeo de tipos → iconos Bootstrap Icons
    const iconMap = {
        apuntes: 'bi-file-earmark-text',
        examen:  'bi-file-earmark-check',
        libro:   'bi-book-half',
        guia:    'bi-journal-text',
        otro:    'bi-file-earmark',
    };

    // Mapeo de tipos → clases de badge
    const badgeMap = {
        apuntes: ['badge-apuntes', 'Apuntes'],
        examen:  ['badge-examen',  'Examen'],
        libro:   ['badge-libro',   'Libro'],
        guia:    ['badge-guia',    'Guía'],
        otro:    ['badge-otro',    'Otro'],
    };

    const [badgeClass, badgeLabel] = badgeMap[tipo] || badgeMap['otro'];
    const iconClass = iconMap[tipo] || iconMap['otro'];

    // Rellenar modal
    modalDetalle.querySelector('#modalDetalleLabel').textContent = titulo;
    modalDetalle.querySelector('#modal-tipo-icon').className     = 'bi ' + iconClass;
    modalDetalle.querySelector('#modal-tipo-badge').innerHTML    =
        `<span class="materia-badge ${badgeClass}">${badgeLabel}</span>`;
    modalDetalle.querySelector('#modal-materia').textContent     = materia;
    modalDetalle.querySelector('#modal-fecha').textContent       = fecha;
    modalDetalle.querySelector('#modal-descargas').textContent   = descargas + ' descargas';
    modalDetalle.querySelector('#modal-estado').innerHTML        =
        activo === '1'
            ? '<span class="status-dot status-active"></span> Visible'
            : '<span class="status-dot status-inactive"></span> Oculta';
    modalDetalle.querySelector('#modal-desc').textContent        = desc;

    // Actualizar rutas de botones con el ID real
    // RUTAS CODEIGNITER: ajusta los segmentos a tu enrutamiento
    modalDetalle.querySelector('#modal-btn-editar').href     = BASE_URL + 'publicaciones/editar/'   + id;
    modalDetalle.querySelector('#modal-btn-eliminar').href   = BASE_URL + 'publicaciones/eliminar/' + id;
    modalDetalle.querySelector('#modal-btn-descargar').href  = BASE_URL + 'publicaciones/descargar/'+ id;
});

/* ================================================
   2. FILTROS DE TIPO (client-side)
   Muestra/oculta cards según el tipo seleccionado
================================================ */
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function () {
        // Quitar active de todos
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('#pub-grid > .col-12, #pub-grid > [class*="col-"]').forEach(col => {
            if (filter === 'todos' || col.dataset.tipo === filter) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    });
});

/* ================================================
   3. CONFIRMACIÓN AL ELIMINAR
   Intercepta el clic en "Eliminar" dentro del modal
================================================ */
document.getElementById('modal-btn-eliminar').addEventListener('click', function (e) {
    const titulo = modalDetalle.querySelector('#modalDetalleLabel').textContent;
    if (!confirm(`¿Seguro que querés eliminar "${titulo}"? Esta acción no se puede deshacer.`)) {
        e.preventDefault();
    }
});
</script>

</body>
</html>