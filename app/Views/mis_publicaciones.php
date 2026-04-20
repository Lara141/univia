<?php
/**
 * UNIVIA — Vista: Dashboard / Panel del Usuario  (v2)
 * Archivo: application/views/dashboard.php
 *
 * Datos que debe inyectar el controlador (Dashboard.php → index()):
 *   $this->load->view('dashboard', [
 *       'usuario'          => $usuario,         // array: id, nombre, apellido, email
 *       'publicaciones'    => $publicaciones,   // array de publicaciones del usuario
 *       'total_pubs'       => $total_pubs,      // int
 *       'total_descargas'  => $total_descargas, // int
 *       'promedio_rating'  => $promedio_rating, // float
 *   ]);
 *
 * Estructura esperada de cada $pub:
 *   $pub['id']              → int
 *   $pub['titulo']          → string
 *   $pub['descripcion']     → string
 *   $pub['tipo_recurso']    → string: resumen | apunte | libro | examen | guia | otro
 *   $pub['tipo_acuerdo']    → string: gratis | pago | intercambio
 *   $pub['estado']          → string: activo | inactivo
 *   $pub['materia']         → string
 *   $pub['fecha']           → string: datetime MySQL
 *   $pub['nombre_archivo']  → string|null  (nombre del archivo subido)
 *   $pub['nombre_imagen']   → string|null  (nombre de la imagen de portada)
 *   $pub['es_libro_fisico'] → bool
 *   $pub['descargas']       → int
 */
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

    <style>
    /* ══════════════════════════════════════════════
       SISTEMA DE TEMAS  dark / light
       Activar: data-theme="dark" | "light" en <html>
    ══════════════════════════════════════════════ */
    :root,
    [data-theme="dark"] {
        --bg-base:       #0c0e1a;
        --bg-surface:    #111422;
        --bg-card:       #181c30;
        --bg-card-alt:   #1c2035;
        --bg-input:      rgba(255,255,255,.04);
        --border:        rgba(255,255,255,.07);
        --border-hover:  rgba(91,127,255,.38);
        --accent:        #5b7fff;
        --accent-2:      #8b5cf6;
        --accent-3:      #38bdf8;
        --success:       #34d399;
        --warn:          #fbbf24;
        --danger:        #f87171;
        --text:          #e2e8f0;
        --text-muted:    #64748b;
        --text-soft:     #94a3b8;
        --gradient:      linear-gradient(135deg,#5b7fff 0%,#8b5cf6 100%);
        --glow:          0 0 40px rgba(91,127,255,.18);
        --shadow-card:   0 4px 24px rgba(0,0,0,.35);
        --close-filter:  invert(1) grayscale(1);
        --badge-gratis-bg:        rgba(52,211,153,.12);
        --badge-gratis-color:     #34d399;
        --badge-gratis-border:    rgba(52,211,153,.22);
        --badge-pago-bg:          rgba(251,191,36,.12);
        --badge-pago-color:       #fbbf24;
        --badge-pago-border:      rgba(251,191,36,.22);
        --badge-intercambio-bg:   rgba(56,189,248,.12);
        --badge-intercambio-color:#38bdf8;
        --badge-intercambio-border:rgba(56,189,248,.22);
        --switch-track:  rgba(255,255,255,.12);
        --preview-ph-bg: rgba(255,255,255,.03);
    }

    [data-theme="light"] {
        --bg-base:       #f0f2f8;
        --bg-surface:    #ffffff;
        --bg-card:       #ffffff;
        --bg-card-alt:   #f7f9fd;
        --bg-input:      rgba(0,0,0,.03);
        --border:        rgba(0,0,0,.09);
        --border-hover:  rgba(74,108,247,.4);
        --accent:        #4a6cf7;
        --accent-2:      #7c3aed;
        --accent-3:      #0ea5e9;
        --success:       #059669;
        --warn:          #d97706;
        --danger:        #dc2626;
        --text:          #1e293b;
        --text-muted:    #94a3b8;
        --text-soft:     #64748b;
        --gradient:      linear-gradient(135deg,#4a6cf7 0%,#7c3aed 100%);
        --glow:          0 0 32px rgba(74,108,247,.14);
        --shadow-card:   0 2px 16px rgba(0,0,0,.08);
        --close-filter:  none;
        --badge-gratis-bg:        rgba(5,150,105,.1);
        --badge-gratis-color:     #059669;
        --badge-gratis-border:    rgba(5,150,105,.2);
        --badge-pago-bg:          rgba(217,119,6,.1);
        --badge-pago-color:       #d97706;
        --badge-pago-border:      rgba(217,119,6,.2);
        --badge-intercambio-bg:   rgba(14,165,233,.1);
        --badge-intercambio-color:#0284c7;
        --badge-intercambio-border:rgba(14,165,233,.2);
        --switch-track:  rgba(0,0,0,.1);
        --preview-ph-bg: rgba(0,0,0,.02);
    }

    /* ── Transición global al cambiar tema ── */
    html { transition: background-color .28s ease; }
    body, .univia-navbar, .page-hero, .stat-chip, .pub-card,
    .card-preview, .modal-content, .dropdown-menu, .detail-desc-box,
    .file-name-chip { transition: background-color .28s ease, border-color .28s ease, color .2s ease; }

    /* ══════════════════════════════════════════════
       BASE
    ══════════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
        background-color: var(--bg-base);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: .95rem;
        min-height: 100vh;
    }
    h1,h2,h3,h4,h5,.brand-name { font-family: 'Syne', sans-serif; }
    a { text-decoration: none; }

    /* ══════════════════════════════════════════════
       NAVBAR
    ══════════════════════════════════════════════ */
    .univia-navbar {
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        padding: .6rem 0;
        position: sticky; top: 0; z-index: 1030;
        backdrop-filter: blur(14px);
    }
    .brand-name {
        font-size: 1.45rem; font-weight: 800; letter-spacing: -.5px;
        background: var(--gradient);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Búsqueda */
    .search-wrap { position: relative; max-width: 380px; width: 100%; }
    .search-wrap .bi-search {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted); font-size: .85rem; pointer-events: none;
    }
    .search-input {
        background: var(--bg-input) !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        border-radius: 10px !important;
        padding-left: 2rem !important;
        font-family: 'DM Sans', sans-serif; font-size: .88rem;
    }
    .search-input::placeholder { color: var(--text-muted); }
    .search-input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px rgba(91,127,255,.15) !important;
        outline: none;
    }

    /* Avatar */
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--gradient);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; color: #fff;
        cursor: pointer; border: 2px solid rgba(91,127,255,.4);
        user-select: none;
    }

    /* Dropdown */
    .dropdown-menu {
        background: var(--bg-card-alt);
        border: 1px solid var(--border);
        border-radius: 14px; padding: 6px;
        min-width: 230px; box-shadow: var(--shadow-card);
    }
    .dropdown-item {
        color: var(--text); border-radius: 9px;
        padding: 9px 14px; font-size: .88rem;
        display: flex; align-items: center; gap: 9px;
        background: transparent;
    }
    .dropdown-item:hover { background: rgba(91,127,255,.1); color: var(--accent); }
    .dropdown-item.danger:hover { background: rgba(220,38,38,.1); color: var(--danger); }
    .dropdown-divider { border-color: var(--border); margin: 4px 0; }
    .dropdown-header { color: var(--text-muted); font-size: .75rem; padding: 6px 14px; font-family: 'Syne', sans-serif; }

    /* ── Toggle de tema ── */
    .theme-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 9px 14px; border-radius: 9px; cursor: pointer; color: var(--text);
        font-size: .88rem; gap: 9px;
    }
    .theme-row:hover { background: rgba(91,127,255,.1); }
    .theme-row-left { display: flex; align-items: center; gap: 8px; }

    .t-switch {
        position: relative; width: 40px; height: 22px; flex-shrink: 0;
    }
    .t-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .t-track {
        position: absolute; inset: 0;
        background: var(--switch-track);
        border: 1px solid var(--border);
        border-radius: 22px; cursor: pointer;
        transition: background .25s;
    }
    .t-track::before {
        content: '';
        position: absolute; left: 3px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px;
        background: #fff; border-radius: 50%;
        transition: transform .25s;
        box-shadow: 0 1px 4px rgba(0,0,0,.25);
    }
    .t-switch input:checked + .t-track { background: var(--accent); border-color: var(--accent); }
    .t-switch input:checked + .t-track::before { transform: translate(18px,-50%); }

    .t-icon { font-size: 1rem; }

    /* ══════════════════════════════════════════════
       HERO
    ══════════════════════════════════════════════ */
    .page-hero {
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        padding: 2rem 0 1.8rem;
    }
    .page-hero h1 { font-size: 1.85rem; font-weight: 700; margin-bottom: .2rem; letter-spacing: -.4px; }
    .page-hero .subtitle { color: var(--text-muted); font-size: .92rem; }

    .btn-nueva {
        background: var(--gradient);
        border: none; color: #fff;
        font-family: 'Syne', sans-serif; font-weight: 700;
        font-size: .9rem; padding: 10px 22px; border-radius: 10px;
        box-shadow: 0 4px 20px rgba(91,127,255,.35);
        display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;
        transition: transform .15s, box-shadow .15s, filter .15s;
    }
    .btn-nueva:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(91,127,255,.5); filter: brightness(1.08); color: #fff; }

    .stat-chip {
        background: var(--bg-card);
        border: 1px solid var(--border); border-radius: 12px;
        padding: 12px 18px; display: flex; align-items: center; gap: 12px;
        box-shadow: var(--shadow-card);
    }
    .stat-chip .icon-wrap { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .stat-chip .stat-value { font-family: 'Syne', sans-serif; font-size: 1.35rem; font-weight: 700; line-height: 1; }
    .stat-chip .stat-label { font-size: .77rem; color: var(--text-muted); margin-top: 2px; }

    /* ══════════════════════════════════════════════
       SECCIÓN HEADER + FILTROS
    ══════════════════════════════════════════════ */
    .section-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 1.4rem; }
    .section-header h2 { font-size: 1.15rem; font-weight: 700; margin: 0; }
    .filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
    .filter-pill {
        font-size: .77rem; padding: 5px 13px; border-radius: 20px;
        border: 1px solid var(--border); background: transparent;
        color: var(--text-muted); cursor: pointer;
        transition: background .15s, border-color .15s, color .15s;
        font-family: 'DM Sans', sans-serif;
    }
    .filter-pill:hover, .filter-pill.active { background: rgba(91,127,255,.1); border-color: var(--accent); color: var(--accent); }

    /* ══════════════════════════════════════════════
       CARDS DE PUBLICACIONES
    ══════════════════════════════════════════════ */
    .pub-card {
        background: var(--bg-card);
        border: 1px solid var(--border); border-radius: 16px;
        overflow: hidden; display: flex; flex-direction: column; height: 100%;
        box-shadow: var(--shadow-card); cursor: pointer;
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .pub-card:hover { border-color: var(--border-hover); box-shadow: var(--glow); transform: translateY(-2px); }

    /* Preview */
    .card-preview {
        width: 100%; height: 148px;
        background: var(--bg-card-alt);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        position: relative; overflow: hidden;
    }
    .card-preview img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s ease; }
    .pub-card:hover .card-preview img { transform: scale(1.04); }
    .card-preview-icon { font-size: 2.8rem; opacity: .22; }

    .preview-pill {
        position: absolute; bottom: 8px; left: 8px;
        font-size: .68rem; font-weight: 700;
        padding: 3px 9px; border-radius: 20px;
        backdrop-filter: blur(8px);
        background: rgba(0,0,0,.5); color: #fff;
        font-family: 'Syne', sans-serif; letter-spacing: .3px;
        border: 1px solid rgba(255,255,255,.1);
    }
    [data-theme="light"] .preview-pill { background: rgba(255,255,255,.78); color: #1e293b; border-color: rgba(0,0,0,.08); }

    /* Badges de tipo y acuerdo */
    .badge-tipo, .badge-acuerdo {
        font-size: .69rem; font-weight: 700;
        padding: 3px 9px; border-radius: 20px;
        font-family: 'Syne', sans-serif; letter-spacing: .3px;
        display: inline-flex; align-items: center; gap: 3px;
    }
    .badge-resumen   { background:rgba(139,92,246,.14); color:#a78bfa; border:1px solid rgba(139,92,246,.22); }
    .badge-apunte    { background:rgba(91,127,255,.14);  color:#7ca0ff; border:1px solid rgba(91,127,255,.22); }
    .badge-libro     { background:rgba(52,211,153,.12);  color:var(--success); border:1px solid rgba(52,211,153,.22); }
    .badge-examen    { background:rgba(251,191,36,.12);  color:var(--warn); border:1px solid rgba(251,191,36,.22); }
    .badge-guia      { background:rgba(56,189,248,.12);  color:var(--accent-3); border:1px solid rgba(56,189,248,.22); }
    .badge-otro      { background:rgba(100,116,139,.12); color:var(--text-muted); border:1px solid rgba(100,116,139,.2); }
    .badge-gratis    { background:var(--badge-gratis-bg); color:var(--badge-gratis-color); border:1px solid var(--badge-gratis-border); }
    .badge-pago      { background:var(--badge-pago-bg);  color:var(--badge-pago-color);  border:1px solid var(--badge-pago-border); }
    .badge-intercambio { background:var(--badge-intercambio-bg); color:var(--badge-intercambio-color); border:1px solid var(--badge-intercambio-border); }

    .card-body-inner { padding: 1.05rem 1.15rem; display: flex; flex-direction: column; flex: 1; }
    .pub-card-title { font-family: 'Syne', sans-serif; font-size: .96rem; font-weight: 700; color: var(--text); margin-bottom: .3rem; line-height: 1.3; }
    .pub-card-materia { font-size: .79rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-bottom: .5rem; }
    .pub-card-desc { color: var(--text-soft); font-size: .82rem; line-height: 1.55; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pub-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: .85rem; padding-top: .75rem; border-top: 1px solid var(--border); font-size: .78rem; color: var(--text-muted); }

    .status-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .status-active   { background: var(--success); box-shadow: 0 0 6px var(--success); }
    .status-inactive { background: var(--text-muted); }

    .btn-ver-card {
        font-size: .75rem; padding: 4px 11px; border-radius: 7px;
        background: transparent; border: 1px solid var(--border); color: var(--text-soft);
        transition: background .15s, border-color .15s, color .15s;
    }
    .btn-ver-card:hover { background: rgba(91,127,255,.1); border-color: var(--accent); color: var(--accent); }

    /* Empty state */
    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
    .empty-state .empty-icon { font-size: 3.2rem; opacity: .25; margin-bottom: 1rem; }

    /* ══════════════════════════════════════════════
       MODAL DE DETALLE
    ══════════════════════════════════════════════ */
    .modal-content { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; color: var(--text); box-shadow: 0 20px 60px rgba(0,0,0,.45); }
    .modal-header  { border-bottom: 1px solid var(--border); padding: 1.3rem 1.6rem .9rem; }
    .modal-body    { padding: 1.4rem 1.6rem; }
    .modal-footer  { border-top: 1px solid var(--border); padding: .85rem 1.6rem; }
    .modal-title   { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; }
    .btn-close     { filter: var(--close-filter); }

    /* Preview en modal */
    .modal-preview-wrap {
        width: 100%; border-radius: 12px; overflow: hidden;
        border: 1px solid var(--border); background: var(--bg-card-alt);
        position: relative;
    }
    .modal-cover-img { width: 100%; max-height: 300px; object-fit: contain; display: block; padding: 8px; }
    .modal-pdf-frame { width: 100%; height: 280px; border: none; display: block; border-radius: 8px; }
    .modal-preview-ph {
        height: 160px; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 10px;
        color: var(--text-muted); font-size: .88rem;
        background: var(--preview-ph-bg);
    }
    .modal-preview-ph i { font-size: 2.4rem; opacity: .3; }
    .preview-tag {
        position: absolute; top: 8px; right: 8px;
        font-size: .68rem; padding: 3px 9px;
        background: rgba(0,0,0,.55); color: #fff;
        border-radius: 20px; backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.1);
        font-family: 'Syne', sans-serif; font-weight: 700;
    }
    [data-theme="light"] .preview-tag { background: rgba(255,255,255,.82); color: #1e293b; border-color: rgba(0,0,0,.08); }

    /* Grilla de detalles */
    .detail-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem 1.5rem; }
    .detail-label { font-size: .7rem; color: var(--text-muted); font-weight: 600; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 3px; font-family: 'Syne', sans-serif; }
    .detail-value { font-size: .9rem; color: var(--text); margin: 0; }
    .detail-desc-box { background: var(--bg-card-alt); border: 1px solid var(--border); border-radius: 10px; padding: .85rem 1rem; font-size: .88rem; color: var(--text-soft); line-height: 1.65; white-space: pre-wrap; max-height: 120px; overflow-y: auto; }
    .file-name-chip { display: inline-flex; align-items: center; gap: 6px; background: var(--bg-card-alt); border: 1px solid var(--border); border-radius: 7px; padding: 4px 11px; font-size: .8rem; color: var(--text-soft); font-family: 'DM Mono', monospace; word-break: break-all; }

    /* Botones modal */
    .btn-editar { background: var(--gradient); border: none; color: #fff; font-family: 'Syne', sans-serif; font-weight: 700; font-size: .9rem; padding: 10px 22px; border-radius: 10px; box-shadow: 0 4px 16px rgba(91,127,255,.3); display: inline-flex; align-items: center; gap: 7px; transition: filter .15s, box-shadow .15s; }
    .btn-editar:hover { filter: brightness(1.08); box-shadow: 0 6px 22px rgba(91,127,255,.45); color: #fff; }
    .btn-eliminar { background: transparent; border: 1px solid rgba(239,68,68,.28); color: var(--danger); font-size: .88rem; padding: 9px 18px; border-radius: 10px; display: inline-flex; align-items: center; gap: 7px; transition: background .15s, border-color .15s; }
    .btn-eliminar:hover { background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.5); color: var(--danger); }
    .btn-cerrar-modal { background: transparent; border: 1px solid var(--border); color: var(--text-muted); padding: 9px 16px; border-radius: 10px; font-size: .88rem; transition: background .15s, color .15s; }
    .btn-cerrar-modal:hover { background: rgba(255,255,255,.05); color: var(--text); }
    .btn-descargar { background: rgba(52,211,153,.08); border: 1px solid rgba(52,211,153,.22); color: var(--success); font-size: .86rem; padding: 8px 16px; border-radius: 9px; display: inline-flex; align-items: center; gap: 7px; transition: background .15s; }
    .btn-descargar:hover { background: rgba(52,211,153,.16); color: var(--success); }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(128,128,128,.2); border-radius: 3px; }

    @media (max-width:576px) {
        .page-hero h1 { font-size: 1.45rem; }
        .btn-nueva-lbl { display: none; }
        .detail-grid { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════
     NAVBAR
═══════════════════════════════════════ -->
<nav class="univia-navbar">
    <div class="container-lg">
        <div class="d-flex align-items-center gap-3">

            <!-- Logo -->
            <a href="<?= base_url('dashboard') ?>" class="brand-name">Univia</a>

            <!-- Búsqueda desktop -->
            <!-- RUTA: base_url('materiales/buscar') -->
            <form class="flex-grow-1 search-wrap d-none d-md-block"
                  action="<?= base_url('materiales/buscar') ?>" method="GET">
                <i class="bi bi-search"></i>
                <input type="search" name="q" class="form-control search-input"
                       placeholder="Buscar materiales, materias, carreras…" autocomplete="off">
            </form>

            <div class="ms-auto d-flex align-items-center gap-2">

                <!-- ── DROPDOWN DE PERFIL (avatar arriba a la derecha) ── -->
                <div class="dropdown">
                    <!--
                        DATO DINÁMICO: Iniciales del usuario
                        PHP: echo strtoupper(
                               substr($usuario['nombre'],0,1).
                               substr($usuario['apellido'],0,1)
                             );
                    -->
                    <div class="user-avatar dropdown-toggle"
                         data-bs-toggle="dropdown" aria-expanded="false">
                        VA
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:240px;">

                        <!-- Nombre del usuario -->
                        <li>
                            <span class="dropdown-header">
                                <!--
                                    PHP: echo htmlspecialchars(
                                           $usuario['nombre'].' '.$usuario['apellido']
                                         );
                                -->
                                Valentina Acosta
                            </span>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- ── TOGGLE MODO DIURNO / NOCTURNO ── -->
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

                        <!-- Navegación -->
                        <li>
                            <a class="dropdown-item" href="<?= base_url('perfil') ?>">
                                <i class="bi bi-person-circle"></i> Mi Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('publicaciones') ?>">
                                <i class="bi bi-folder2-open"></i> Mis Publicaciones
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('configuracion') ?>">
                                <i class="bi bi-gear"></i> Configuración
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <!-- RUTA: base_url('auth/cerrar_sesion') -->
                            <a class="dropdown-item danger" href="<?= base_url('auth/cerrar_sesion') ?>" style="color:var(--danger);">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div><!-- /dropdown -->

            </div>
        </div>
    </div>
</nav>


<!-- ═══════════════════════════════════════
     HERO — saludo + CTA + stats
═══════════════════════════════════════ -->
<section class="page-hero">
    <div class="container-lg">

        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h1>
                    <!--
                        DATO DINÁMICO:
                        PHP: echo 'Hola, '.htmlspecialchars($usuario['nombre']).' 👋';
                    -->
                    Hola, Valentina 👋
                </h1>
                <p class="subtitle mb-0">
                    Gestioná tus materiales y compartí conocimiento con la comunidad universitaria.
                </p>
            </div>

            <!-- BOTÓN PRINCIPAL: Nueva Publicación -->
            <!-- RUTA: base_url('publicaciones/nueva') -->
            <a href="<?= base_url('publicaciones/nueva') ?>" class="btn btn-nueva">
                <i class="bi bi-plus-lg"></i>
                <span class="btn-nueva-lbl">Nueva Publicación</span>
            </a>
        </div>

        <!-- Stats -->
        <div class="row g-3 mt-3">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(91,127,255,.12);">
                        <i class="bi bi-file-earmark-text" style="color:var(--accent);"></i>
                    </div>
                    <div>
                        <!-- PHP: echo $total_pubs ?? 0; -->
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
                        <!-- PHP: echo $total_descargas ?? 0; -->
                        <div class="stat-value">84</div>
                        <div class="stat-label">Descargas totales</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="stat-chip">
                    <div class="icon-wrap" style="background:rgba(251,191,36,.12);">
                        <i class="bi bi-star" style="color:var(--warn);"></i>
                    </div>
                    <div>
                        <!-- PHP: echo number_format($promedio_rating ?? 0, 1); -->
                        <div class="stat-value">4.7</div>
                        <div class="stat-label">Valoración media</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- ═══════════════════════════════════════
     CONTENIDO PRINCIPAL
═══════════════════════════════════════ -->
<main class="container-lg py-4">

    <!-- Buscador móvil -->
    <form class="d-md-none mb-3" action="<?= base_url('materiales/buscar') ?>" method="GET">
        <div class="search-wrap" style="max-width:100%;">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control search-input"
                   placeholder="Buscar materiales…" autocomplete="off">
        </div>
    </form>

    <!-- Header + filtros -->
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

    <!--
    ═══════════════════════════════════════════════════════
    GRILLA DE PUBLICACIONES — PHP DINÁMICO
    ───────────────────────────────────────────────────────
    Reemplazar las cards estáticas con:

    <?php if (!empty($publicaciones)): ?>
    <?php foreach ($publicaciones as $pub): ?>

    <?php
      $ext = strtolower(pathinfo($pub['nombre_archivo'] ?? '', PATHINFO_EXTENSION));
      $url_imagen  = !empty($pub['nombre_imagen'])
        ? base_url('uploads/imagenes/'.$pub['nombre_imagen']) : '';
      $url_archivo = !empty($pub['nombre_archivo'])
        ? base_url('uploads/archivos/'.$pub['nombre_archivo']) : '';
    ?>

    <div class="col-12 col-sm-6 col-lg-4" data-tipo="<?= $pub['tipo_recurso'] ?>">
      <div class="pub-card"
           data-bs-toggle="modal" data-bs-target="#modalDetalle"
           data-id="<?= $pub['id'] ?>"
           data-titulo="<?= htmlspecialchars($pub['titulo']) ?>"
           data-descripcion="<?= htmlspecialchars($pub['descripcion']) ?>"
           data-tipo-recurso="<?= $pub['tipo_recurso'] ?>"
           data-tipo-acuerdo="<?= $pub['tipo_acuerdo'] ?>"
           data-estado="<?= $pub['estado'] ?>"
           data-materia="<?= htmlspecialchars($pub['materia']) ?>"
           data-fecha="<?= date('d/m/Y', strtotime($pub['fecha'])) ?>"
           data-nombre-archivo="<?= htmlspecialchars($pub['nombre_archivo'] ?? '') ?>"
           data-nombre-imagen="<?= htmlspecialchars($pub['nombre_imagen'] ?? '') ?>"
           data-es-libro-fisico="<?= $pub['es_libro_fisico'] ? '1' : '0' ?>"
           data-descargas="<?= $pub['descargas'] ?>"
           data-url-imagen="<?= $url_imagen ?>"
           data-url-archivo="<?= $url_archivo ?>">
        ... (estructura interna como los ejemplos abajo)
      </div>
    </div>

    <?php endforeach; ?>
    <?php else: ?>
      (empty state)
    <?php endif; ?>
    ═══════════════════════════════════════════════════════
    -->

    <div class="row g-3" id="pub-grid">

        <!-- ── CARD 1: Apunte PDF ── -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="apunte">
            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="1"
                 data-titulo="Apuntes Ingeniería de Software I"
                 data-descripcion="Resumen completo de todos los temas vistos durante la cursada: ciclos de vida, metodologías ágiles, casos de uso, diagramas UML y arquitecturas de software. Incluye ejemplos prácticos."
                 data-tipo-recurso="apunte"
                 data-tipo-acuerdo="gratis"
                 data-estado="activo"
                 data-materia="Ingeniería de Software I"
                 data-fecha="15/05/2025"
                 data-nombre-archivo="apuntes_is1_2024.pdf"
                 data-nombre-imagen=""
                 data-es-libro-fisico="0"
                 data-descargas="23"
                 data-url-imagen=""
                 data-url-archivo="<?= base_url('uploads/archivos/apuntes_is1_2024.pdf') ?>">

                <div class="card-preview">
                    <i class="bi bi-file-earmark-pdf card-preview-icon" style="color:#ef4444;"></i>
                    <span class="preview-pill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</span>
                </div>
                <div class="card-body-inner">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge-tipo badge-apunte">Apunte</span>
                        <span class="badge-acuerdo badge-gratis"><i class="bi bi-gift"></i>Gratis</span>
                    </div>
                    <!-- PHP: echo htmlspecialchars($pub['titulo']); -->
                    <h3 class="pub-card-title">Apuntes Ingeniería de Software I</h3>
                    <div class="pub-card-materia">
                        <i class="bi bi-mortarboard"></i>
                        <!-- PHP: echo htmlspecialchars($pub['materia']); -->
                        <span>Ingeniería de Software I</span>
                    </div>
                    <!-- PHP: echo htmlspecialchars($pub['descripcion']); -->
                    <p class="pub-card-desc">Resumen completo de todos los temas vistos: ciclos de vida, metodologías ágiles, casos de uso, diagramas UML y arquitecturas.</p>
                    <div class="pub-card-footer">
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-dot status-active"></span>
                            <!-- PHP: echo ucfirst($pub['estado']); -->
                            <span>Activo</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span><i class="bi bi-download"></i> 23</span>
                            <button class="btn btn-ver-card">Ver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── CARD 2: Libro físico con imagen de portada ── -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="libro">
            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="2"
                 data-titulo="Introducción a la Programación — Deitel"
                 data-descripcion="Libro físico en excelente estado. Edición 2019. Cubre fundamentos de C++ hasta POO. Ideal para primer año de Ingeniería en Sistemas. Puedo prestarlo o intercambiarlo."
                 data-tipo-recurso="libro"
                 data-tipo-acuerdo="intercambio"
                 data-estado="activo"
                 data-materia="Programación I"
                 data-fecha="02/04/2025"
                 data-nombre-archivo=""
                 data-nombre-imagen="deitel_portada.jpg"
                 data-es-libro-fisico="1"
                 data-descargas="0"
                 data-url-imagen="https://placehold.co/400x560/1a1e35/5b7fff?text=Deitel+C%2B%2B&font=syne"
                 data-url-archivo="">

                <!-- Preview: imagen de portada del libro físico -->
                <div class="card-preview">
                    <!--
                        DATO DINÁMICO: mostrar imagen si existe, sino ícono
                        PHP:
                        if (!empty($pub['nombre_imagen'])):
                          echo '<img src="'.base_url('uploads/imagenes/'.$pub['nombre_imagen']).'" alt="Portada" loading="lazy">';
                        else:
                          echo '<i class="bi bi-book-half card-preview-icon" style="color:var(--success);"></i>';
                        endif;
                    -->
                    <img src="https://placehold.co/400x560/1a1e35/5b7fff?text=Deitel+C%2B%2B&font=syne"
                         alt="Portada del libro" loading="lazy">
                    <span class="preview-pill"><i class="bi bi-book-half me-1"></i>Libro físico</span>
                </div>
                <div class="card-body-inner">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge-tipo badge-libro">Libro</span>
                        <span class="badge-acuerdo badge-intercambio"><i class="bi bi-arrow-left-right"></i>Intercambio</span>
                    </div>
                    <h3 class="pub-card-title">Introducción a la Programación — Deitel</h3>
                    <div class="pub-card-materia"><i class="bi bi-mortarboard"></i><span>Programación I</span></div>
                    <p class="pub-card-desc">Libro físico en excelente estado. Edición 2019. Cubro fundamentos de C++ hasta POO.</p>
                    <div class="pub-card-footer">
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-dot status-active"></span><span>Activo</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="color:var(--text-muted);">📚 Físico</span>
                            <button class="btn btn-ver-card">Ver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── CARD 3: Examen PDF pago ── -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="examen">
            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="3"
                 data-titulo="Parcial 1 Análisis Matemático II — 2024"
                 data-descripcion="Primer parcial 2024 con soluciones paso a paso. Integrales dobles, triples y series de Taylor con 6 ejercicios resueltos. Precio simbólico para cubrir fotocopias."
                 data-tipo-recurso="examen"
                 data-tipo-acuerdo="pago"
                 data-estado="activo"
                 data-materia="Análisis Matemático II"
                 data-fecha="10/03/2025"
                 data-nombre-archivo="parcial1_amat2_2024.pdf"
                 data-nombre-imagen=""
                 data-es-libro-fisico="0"
                 data-descargas="41"
                 data-url-imagen=""
                 data-url-archivo="<?= base_url('uploads/archivos/parcial1_amat2_2024.pdf') ?>">

                <div class="card-preview">
                    <i class="bi bi-file-earmark-check card-preview-icon" style="color:var(--warn);"></i>
                    <span class="preview-pill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</span>
                </div>
                <div class="card-body-inner">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge-tipo badge-examen">Examen</span>
                        <span class="badge-acuerdo badge-pago"><i class="bi bi-currency-dollar"></i>Pago</span>
                    </div>
                    <h3 class="pub-card-title">Parcial 1 Análisis Matemático II — 2024</h3>
                    <div class="pub-card-materia"><i class="bi bi-mortarboard"></i><span>Análisis Matemático II</span></div>
                    <p class="pub-card-desc">Primer parcial 2024 con soluciones paso a paso: integrales dobles, triples y series de Taylor.</p>
                    <div class="pub-card-footer">
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-dot status-active"></span><span>Activo</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span><i class="bi bi-download"></i> 41</span>
                            <button class="btn btn-ver-card">Ver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── CARD 4: Resumen con imagen escaneada ── -->
        <div class="col-12 col-sm-6 col-lg-4" data-tipo="resumen">
            <div class="pub-card"
                 data-bs-toggle="modal" data-bs-target="#modalDetalle"
                 data-id="4"
                 data-titulo="Resumen Redes de Computadoras — Tanenbaum"
                 data-descripcion="Resumen visual con esquemas y mapas conceptuales del libro de Tanenbaum. Capítulos 1 al 5. Incluye imágenes escaneadas de mis apuntes de clase con diagramas de capas OSI y TCP/IP."
                 data-tipo-recurso="resumen"
                 data-tipo-acuerdo="gratis"
                 data-estado="inactivo"
                 data-materia="Redes de Computadoras"
                 data-fecha="20/01/2025"
                 data-nombre-archivo=""
                 data-nombre-imagen="resumen_redes_portada.jpg"
                 data-es-libro-fisico="0"
                 data-descargas="8"
                 data-url-imagen="https://placehold.co/600x400/1c2035/8b5cf6?text=Redes+Tanenbaum&font=syne"
                 data-url-archivo="">

                <div class="card-preview">
                    <img src="https://placehold.co/600x400/1c2035/8b5cf6?text=Redes+Tanenbaum&font=syne"
                         alt="Resumen Redes" loading="lazy">
                    <span class="preview-pill"><i class="bi bi-image me-1"></i>Imagen</span>
                </div>
                <div class="card-body-inner">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge-tipo badge-resumen">Resumen</span>
                        <span class="badge-acuerdo badge-gratis"><i class="bi bi-gift"></i>Gratis</span>
                    </div>
                    <h3 class="pub-card-title">Resumen Redes de Computadoras — Tanenbaum</h3>
                    <div class="pub-card-materia"><i class="bi bi-mortarboard"></i><span>Redes de Computadoras</span></div>
                    <p class="pub-card-desc">Resumen visual con esquemas y mapas conceptuales. Capítulos 1 al 5, diagramas OSI y TCP/IP.</p>
                    <div class="pub-card-footer">
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-dot status-inactive"></span><span>Inactivo</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span><i class="bi bi-download"></i> 8</span>
                            <button class="btn btn-ver-card">Ver</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--
        ── EMPTY STATE ──
        PHP: if (empty($publicaciones)):
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-folder2-open"></i></div>
                <h5 style="font-family:'Syne',sans-serif;font-weight:700;color:var(--text);">Todavía no subiste nada</h5>
                <p style="max-width:300px;margin:0 auto 1.5rem;">Compartí tus apuntes, libros, guías o exámenes con la comunidad.</p>
                <a href="<?= base_url('publicaciones/nueva') ?>" class="btn btn-nueva">
                    <i class="bi bi-plus-lg"></i> Subir primer material
                </a>
            </div>
        </div>
        PHP: endif;
        -->

    </div><!-- /#pub-grid -->
</main>


<!-- ═══════════════════════════════════════
     MODAL — DETALLE COMPLETO
     Datos inyectados por JS desde los data-* de cada card
═══════════════════════════════════════ -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <div class="d-flex flex-column w-100 pe-3">
                    <!-- Badges de tipo recurso y tipo acuerdo -->
                    <div class="d-flex flex-wrap gap-1 mb-2" id="modal-badges"></div>
                    <!-- Título -->
                    <h5 class="modal-title mb-1" id="modal-titulo">—</h5>
                    <!-- Materia -->
                    <div style="font-size:.82rem; color:var(--text-muted);" class="d-flex align-items-center gap-1">
                        <i class="bi bi-mortarboard"></i>
                        <span id="modal-materia">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- ── ZONA DE PREVIEW ── -->
                <!-- Se inyecta dinámicamente con JS según el tipo de material -->
                <div id="modal-preview-wrap" class="modal-preview-wrap mb-4" style="display:none;"></div>

                <!-- ── GRILLA DE DETALLES ── -->
                <div class="detail-grid mb-4">

                    <div>
                        <div class="detail-label">Tipo de recurso</div>
                        <!--
                            PHP: echo ucfirst($pub['tipo_recurso']);
                        -->
                        <p class="detail-value" id="modal-tipo-recurso">—</p>
                    </div>

                    <div>
                        <div class="detail-label">Tipo de acuerdo</div>
                        <!--
                            PHP: echo ucfirst($pub['tipo_acuerdo']);
                        -->
                        <p class="detail-value" id="modal-tipo-acuerdo">—</p>
                    </div>

                    <div>
                        <div class="detail-label">Estado</div>
                        <!--
                            PHP: echo '<span class="status-dot '.($pub['activo'] ? 'status-active' : 'status-inactive').'"></span> '.ucfirst($pub['estado']);
                        -->
                        <p class="detail-value" id="modal-estado">—</p>
                    </div>

                    <div>
                        <div class="detail-label">Fecha de publicación</div>
                        <!--
                            PHP: echo date('d/m/Y', strtotime($pub['fecha']));
                        -->
                        <p class="detail-value" id="modal-fecha">—</p>
                    </div>

                    <div>
                        <div class="detail-label">Nombre del archivo</div>
                        <!--
                            PHP: echo $pub['nombre_archivo'] ?? '—';
                        -->
                        <p class="detail-value" id="modal-nombre-archivo">—</p>
                    </div>

                    <div>
                        <div class="detail-label">Nombre de la imagen</div>
                        <!--
                            PHP: echo $pub['nombre_imagen'] ?? '—';
                        -->
                        <p class="detail-value" id="modal-nombre-imagen">—</p>
                    </div>

                </div><!-- /detail-grid -->

                <!-- ── DESCRIPCIÓN COMPLETA ── -->
                <div class="detail-label mb-2">Descripción completa</div>
                <!--
                    PHP: echo nl2br(htmlspecialchars($pub['descripcion']));
                -->
                <div class="detail-desc-box" id="modal-descripcion">—</div>

                <!-- ── BOTÓN DESCARGA (solo si hay archivo digital) ── -->
                <div class="mt-3" id="modal-descarga-wrap" style="display:none;">
                    <a href="#" id="modal-btn-descargar" class="btn-descargar" target="_blank">
                        <i class="bi bi-download"></i> Descargar archivo
                    </a>
                </div>

            </div><!-- /modal-body -->

            <!-- Footer -->
            <div class="modal-footer gap-2 justify-content-between flex-wrap">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-cerrar-modal" data-bs-dismiss="modal">Cerrar</button>
                    <!-- RUTA: base_url('publicaciones/eliminar/{id}') -->
                    <a href="#" id="modal-btn-eliminar" class="btn btn-eliminar">
                        <i class="bi bi-trash3"></i> Eliminar
                    </a>
                </div>
                <!-- BOTÓN EDITAR — acción principal -->
                <!-- RUTA: base_url('publicaciones/editar/{id}') -->
                <a href="#" id="modal-btn-editar" class="btn btn-editar">
                    <i class="bi bi-pencil-square"></i> Editar Publicación
                </a>
            </div>

        </div>
    </div>
</div><!-- /#modalDetalle -->


<!-- ═══════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════
   1. SISTEMA DE TEMA  dark ↔ light
   Persiste en localStorage con la clave 'univia_theme'
═══════════════════════════════════════════════════ */
(function () {
    const ROOT     = document.documentElement;
    const KEY      = 'univia_theme';
    const checkbox = document.getElementById('t-checkbox');
    const iconEl   = document.getElementById('t-icon');
    const labelEl  = document.getElementById('t-label');

    // dark = checkbox checked | light = unchecked
    const CFG = {
        dark:  { icon: 'bi-moon-stars-fill', label: 'Modo nocturno', checked: true  },
        light: { icon: 'bi-sun-fill',         label: 'Modo diurno',   checked: false },
    };

    function apply(theme, animate) {
        ROOT.dataset.theme  = theme;
        const c             = CFG[theme];
        checkbox.checked    = c.checked;
        labelEl.textContent = c.label;

        if (animate) {
            iconEl.style.transition = 'transform .28s ease, opacity .2s ease';
            iconEl.style.opacity    = '0';
            iconEl.style.transform  = 'rotate(90deg) scale(.7)';
            setTimeout(() => {
                iconEl.className        = 'bi ' + c.icon + ' t-icon';
                iconEl.style.transform  = 'rotate(0deg) scale(1)';
                iconEl.style.opacity    = '1';
            }, 200);
        } else {
            iconEl.className = 'bi ' + c.icon + ' t-icon';
        }

        localStorage.setItem(KEY, theme);
    }

    // Cargar preferencia guardada
    apply(localStorage.getItem(KEY) || 'dark', false);

    // Toggle via checkbox
    checkbox.addEventListener('change', function () {
        apply(this.checked ? 'dark' : 'light', true);
    });

    // Clic en toda la fila también activa
    document.getElementById('theme-row').addEventListener('click', function (e) {
        if (e.target !== checkbox && e.target.tagName !== 'LABEL') {
            checkbox.checked = !checkbox.checked;
            apply(checkbox.checked ? 'dark' : 'light', true);
        }
    });
})();


/* ═══════════════════════════════════════════════════
   2. MODAL DE DETALLE — inyección de datos desde data-*
═══════════════════════════════════════════════════ */
const TIPO_RECURSO_LABEL = {
    resumen: 'Resumen',           apunte:  'Apunte de clase',
    libro:   'Libro',             examen:  'Examen / Parcial',
    guia:    'Guía de ejercicios',otro:    'Otro',
};
const TIPO_ACUERDO_LABEL = {
    gratis:      'Gratuito',
    pago:        'Pago',
    intercambio: 'Intercambio',
};
const TIPO_RECURSO_BADGE_CLASS = {
    resumen:'badge-resumen', apunte:'badge-apunte', libro:'badge-libro',
    examen:'badge-examen',   guia:'badge-guia',     otro:'badge-otro',
};
const TIPO_ACUERDO_BADGE_CLASS = {
    gratis:'badge-gratis', pago:'badge-pago', intercambio:'badge-intercambio',
};
const TIPO_ACUERDO_ICON = {
    gratis:'bi-gift', pago:'bi-currency-dollar', intercambio:'bi-arrow-left-right',
};
const ARCHIVO_ICON = {
    pdf:'bi-file-earmark-pdf',   doc:'bi-file-earmark-word', docx:'bi-file-earmark-word',
    ppt:'bi-file-earmark-slides',pptx:'bi-file-earmark-slides',
    xls:'bi-file-earmark-excel', xlsx:'bi-file-earmark-excel',
    zip:'bi-file-earmark-zip',   rar:'bi-file-earmark-zip',
    jpg:'bi-image', jpeg:'bi-image', png:'bi-image', webp:'bi-image',
};

/*
    BASE_URL — usando base_url() de CodeIgniter para compatibilidad
*/
const BASE_URL = "<?= rtrim(base_url(), '/') . '/' ?>";

const modalEl = document.getElementById('modalDetalle');

modalEl.addEventListener('show.bs.modal', function (e) {
    const card = e.relatedTarget;
    if (!card) return;
    const d = card.dataset;

    /* —— Título y materia —— */
    document.getElementById('modal-titulo').textContent  = d.titulo   || '—';
    document.getElementById('modal-materia').textContent = d.materia  || '—';

    /* —— Badges —— */
    const trBC = TIPO_RECURSO_BADGE_CLASS[d.tipoRecurso] || 'badge-otro';
    const taBC = TIPO_ACUERDO_BADGE_CLASS[d.tipoAcuerdo] || '';
    const taIC = TIPO_ACUERDO_ICON[d.tipoAcuerdo] || 'bi-tag';
    document.getElementById('modal-badges').innerHTML =
        `<span class="badge-tipo ${trBC}">${TIPO_RECURSO_LABEL[d.tipoRecurso] || d.tipoRecurso}</span>` +
        `<span class="badge-acuerdo ${taBC}"><i class="bi ${taIC}"></i>${TIPO_ACUERDO_LABEL[d.tipoAcuerdo] || d.tipoAcuerdo}</span>`;

    /* —— Campos de detalle —— */
    document.getElementById('modal-tipo-recurso').textContent = TIPO_RECURSO_LABEL[d.tipoRecurso] || d.tipoRecurso || '—';
    document.getElementById('modal-tipo-acuerdo').textContent = TIPO_ACUERDO_LABEL[d.tipoAcuerdo] || d.tipoAcuerdo || '—';
    document.getElementById('modal-fecha').textContent        = d.fecha       || '—';
    document.getElementById('modal-descripcion').textContent  = d.descripcion || '—';

    // Estado
    const activo = d.estado === 'activo';
    document.getElementById('modal-estado').innerHTML =
        `<span class="status-dot ${activo ? 'status-active' : 'status-inactive'}" style="display:inline-block;margin-right:5px;"></span>` +
        (activo ? 'Activo' : 'Inactivo');

    // Nombre archivo
    const archEl = document.getElementById('modal-nombre-archivo');
    archEl.innerHTML = d.nombreArchivo
        ? `<span class="file-name-chip"><i class="bi bi-paperclip"></i>${d.nombreArchivo}</span>`
        : '<span style="color:var(--text-muted);">Sin archivo adjunto</span>';

    // Nombre imagen
    const imgEl = document.getElementById('modal-nombre-imagen');
    imgEl.innerHTML = d.nombreImagen
        ? `<span class="file-name-chip"><i class="bi bi-image"></i>${d.nombreImagen}</span>`
        : '<span style="color:var(--text-muted);">Sin imagen de portada</span>';

    /* —— ZONA DE PREVIEW —— */
    const previewWrap   = document.getElementById('modal-preview-wrap');
    previewWrap.style.display = 'none';
    previewWrap.innerHTML     = '';

    const esLibroFisico = d.esLibroFisico === '1';
    const urlImagen     = d.urlImagen  || '';
    const urlArchivo    = d.urlArchivo || '';
    const ext           = (d.nombreArchivo || '').split('.').pop().toLowerCase();

    if (urlImagen) {
        // Imagen de portada (libro físico o resumen escaneado)
        previewWrap.style.display = 'block';
        const label = esLibroFisico ? 'Libro físico' : 'Vista previa';
        const labelIcon = esLibroFisico ? 'bi-book-half' : 'bi-image';
        previewWrap.innerHTML =
            `<img src="${urlImagen}" alt="Vista previa" class="modal-cover-img">
             <span class="preview-tag"><i class="bi ${labelIcon} me-1"></i>${label}</span>`;

    } else if (ext === 'pdf' && urlArchivo) {
        // PDF → primeras páginas via iframe
        previewWrap.style.display = 'block';
        previewWrap.innerHTML =
            `<iframe src="${urlArchivo}#toolbar=0&navpanes=0&scrollbar=0&page=1&view=FitH"
                     class="modal-pdf-frame" title="Primeras páginas" loading="lazy"></iframe>
             <span class="preview-tag"><i class="bi bi-file-earmark-pdf me-1"></i>Primeras páginas</span>`;

    } else if (urlArchivo) {
        // Otro formato → placeholder con ícono
        const ic = ARCHIVO_ICON[ext] || 'bi-file-earmark';
        previewWrap.style.display = 'block';
        previewWrap.innerHTML =
            `<div class="modal-preview-ph">
                <i class="bi ${ic}"></i>
                <span>Vista previa no disponible para <strong>.${ext}</strong></span>
                <span style="font-size:.75rem; color:var(--text-muted);">${d.nombreArchivo}</span>
             </div>`;
    }

    /* —— Botón descarga —— */
    const descWrap = document.getElementById('modal-descarga-wrap');
    const btnDesc  = document.getElementById('modal-btn-descargar');
    if (urlArchivo && !esLibroFisico) {
        descWrap.style.display = '';
        btnDesc.href           = urlArchivo;
    } else {
        descWrap.style.display = 'none';
    }

    /* —— Rutas de acción —— */
    document.getElementById('modal-btn-editar').href   = BASE_URL + 'publicaciones/editar/'   + d.id;
    document.getElementById('modal-btn-eliminar').href = BASE_URL + 'publicaciones/eliminar/' + d.id;
});


/* ═══════════════════════════════════════════════════
   3. FILTROS CLIENT-SIDE
═══════════════════════════════════════════════════ */
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function () {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        const f = this.dataset.filter;
        document.querySelectorAll('#pub-grid > [data-tipo]').forEach(col => {
            col.style.display = (f === 'todos' || col.dataset.tipo === f) ? '' : 'none';
        });
    });
});


/* ═══════════════════════════════════════════════════
   4. CONFIRMAR ELIMINACIÓN
═══════════════════════════════════════════════════ */
document.getElementById('modal-btn-eliminar').addEventListener('click', function (e) {
    const t = document.getElementById('modal-titulo').textContent;
    if (!confirm(`¿Seguro que querés eliminar "${t}"?\nEsta acción no se puede deshacer.`)) {
        e.preventDefault();
    }
});
</script>

</body>
</html>