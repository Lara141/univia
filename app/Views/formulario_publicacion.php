<?php


// Determina si el formulario está en modo edición o creación
$modoEdicion  = isset($modo) && $modo === 'editar';
// Obtiene los datos de la publicación si existen
$pub          = $publicacion ?? [];

// Función para obtener el valor de un campo del formulario
function fv($pub, $key, $default = '') {
    return htmlspecialchars($pub[$key] ?? $default);
}

function fsel($pub, $key, $value, $default = '') {
    $actual = $pub[$key] ?? $default;
    return $actual === $value ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
   <!-- Configuración básica del documento HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- Título dinámico según modo-->
    <title>Univia — <?= $modoEdicion ? 'Editar' : 'Nueva' ?> Publicación</title>

   <!-- Librerías externas (Bootstrap, iconos y fuentes) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
    /* temas*/
    :root,
    [data-theme="dark"] {
        /*Colores base*/
        --bg-base:      #0c0e1a;
        --bg-surface:   #111422;
        --bg-card:      #181c30;
        --bg-card-alt:  #1c2035;
       
        --bg-input:     rgba(255,255,255,.05);
        --border:       rgba(255,255,255,.07);
        --border-hover: rgba(91,127,255,.5);
        --border-focus: rgba(91,127,255,.8);
        /*Colores principales*/
        --accent:       #5b7fff;
        --accent-2:     #8b5cf6;
        --accent-3:     #38bdf8;
        --success:      #34d399;
        --warn:         #fbbf24;
        --danger:       #f87171;
        --text:         #e2e8f0;
        --text-muted:   #64748b;
        --text-soft:    #94a3b8;
        --gradient:     linear-gradient(135deg,#5b7fff 0%,#8b5cf6 100%);
        --glow:         0 0 40px rgba(91,127,255,.18);
        --shadow-card:  0 4px 24px rgba(0,0,0,.35);
        --close-filter: invert(1) grayscale(1);
        --switch-track: rgba(255,255,255,.12);
        --drop-bg:      rgba(91,127,255,.06);
        --drop-border:  rgba(91,127,255,.25);
    }

    /*Tema claro*/
    [data-theme="light"] {
        --bg-base:      #f0f2f8;
        --bg-surface:   #ffffff;
        --bg-card:      #ffffff;
        --bg-card-alt:  #f7f9fd;
        --bg-input:     rgba(0,0,0,.03);
        --border:       rgba(0,0,0,.09);
        --border-hover: rgba(74,108,247,.4);
        --border-focus: rgba(74,108,247,.7);
        --accent:       #4a6cf7;
        --accent-2:     #7c3aed;
        --accent-3:     #0ea5e9;
        --success:      #059669;
        --warn:         #d97706;
        --danger:       #dc2626;
        --text:         #1e293b;
        --text-muted:   #94a3b8;
        --text-soft:    #64748b;
        --gradient:     linear-gradient(135deg,#4a6cf7 0%,#7c3aed 100%);
        --glow:         0 0 32px rgba(74,108,247,.14);
        --shadow-card:  0 2px 16px rgba(0,0,0,.08);
        --close-filter: none;
        --switch-track: rgba(0,0,0,.1);
        --drop-bg:      rgba(74,108,247,.04);
        --drop-border:  rgba(74,108,247,.22);
    }

    /*Configuración general del documento*/
    html { transition: background-color .28s ease; scroll-behavior: smooth; }
    *, *::before, *::after { box-sizing: border-box; }

    /*Estilos base*/
    body {
        background: var(--bg-base);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: .95rem;
        min-height: 100vh;
    }
   
    h1,h2,h3,h4,h5,.brand-name,.field-label { font-family: 'Syne', sans-serif; }
    
    a { text-decoration: none; }


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
     /* Avatar del usuario */
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--gradient);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; color: #fff;
        cursor: pointer; border: 2px solid rgba(91,127,255,.4);
    }
    
    .dropdown-menu {
        background: var(--bg-card-alt); border: 1px solid var(--border);
        border-radius: 14px; padding: 6px; min-width: 230px;
        box-shadow: var(--shadow-card);
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

    .theme-row { display:flex; align-items:center; justify-content:space-between; padding:9px 14px; border-radius:9px; cursor:pointer; color:var(--text); font-size:.88rem; gap:9px; }
    .theme-row:hover { background: rgba(91,127,255,.1); }
   .theme-row-left { display:flex; align-items:center; gap:8px; }
    .t-switch { position:relative; width:40px; height:22px; flex-shrink:0; }
    .t-switch input { opacity:0; width:0; height:0; position:absolute; }
    .t-track { position:absolute; inset:0; background:var(--switch-track); border:1px solid var(--border); border-radius:22px; cursor:pointer; transition:background .25s; }
    .t-track::before { content:''; position:absolute; left:3px; top:50%; transform:translateY(-50%); width:16px; height:16px; background:#fff; border-radius:50%; transition:transform .25s; box-shadow:0 1px 4px rgba(0,0,0,.25); }
    .t-switch input:checked + .t-track { background:var(--accent); border-color:var(--accent); }
    .t-switch input:checked + .t-track::before { transform:translate(18px,-50%); }
    .t-icon { font-size:1rem; }

    /* Encabezado principal del formulario */
    .form-hero {
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        padding: 2rem 0 1.6rem;
    }
   
    .form-hero .breadcrumb { font-size: .8rem; }
    .form-hero .breadcrumb-item a { color: var(--accent); }
    .form-hero .breadcrumb-item.active { color: var(--text-muted); }
    .form-hero .breadcrumb-item + .breadcrumb-item::before { color: var(--text-muted); }
    .form-hero h1 { font-size: 1.75rem; font-weight: 800; margin: .5rem 0 .3rem; letter-spacing: -.4px; }
    .form-hero .subtitle { color: var(--text-muted); font-size: .9rem; }


    .mode-badge {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .75rem; font-weight: 700; padding: 4px 12px;
        border-radius: 20px; font-family: 'Syne', sans-serif; letter-spacing: .3px;
    }
  
    .mode-badge.nueva      { background: rgba(91,127,255,.12); color: var(--accent); border: 1px solid rgba(91,127,255,.25); }
    .mode-badge.editar-mode { background: rgba(251,191,36,.12); color: var(--warn); border: 1px solid rgba(251,191,36,.25); }

    
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
        padding: 2rem 0 3rem;
    }

   
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 1.6rem;
        box-shadow: var(--shadow-card);
        transition: border-color .2s;
    }
  
    .form-card + .form-card { margin-top: 1.2rem; }

   
    .card-section-title {
        font-size: .8rem; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: var(--text-muted);
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 1.4rem; padding-bottom: .85rem;
        border-bottom: 1px solid var(--border);
    }
   
    .card-section-title i { font-size: .95rem; color: var(--accent); }

  
    .field-label {
        font-size: .72rem; font-weight: 700; letter-spacing: .06em;
        text-transform: uppercase; color: var(--text-muted);
        margin-bottom: 6px; display: flex; align-items: center; gap: 5px;
    }
    
    .field-label .required-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--accent); display: inline-block;
    }

    /* Inputs y selects */
    .form-control, .form-select {
        background: var(--bg-input) !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        border-radius: 10px !important;
        font-family: 'DM Sans', sans-serif;
        font-size: .9rem;
        transition: border-color .2s, box-shadow .2s !important;
    }
    /* Estado focus */
    .form-control:focus, .form-select:focus {
        border-color: var(--border-focus) !important;
        box-shadow: 0 0 0 3px rgba(91,127,255,.15) !important;
        outline: none !important;
    }
    /* Placeholder */
    .form-control::placeholder { color: var(--text-muted) !important; }
    /* Campo inválido */
    .form-control.invalid, .form-select.invalid {
        border-color: var(--danger) !important;
        box-shadow: 0 0 0 3px rgba(248,113,113,.12) !important;
    }
    /* Textarea */
    textarea.form-control { resize: vertical; min-height: 110px; }
    /* Opciones del select */
    .form-select option { background: var(--bg-card); color: var(--text); }

    /* Texto de ayuda */
    .field-hint { font-size: .75rem; color: var(--text-muted); margin-top: 5px; }
    /* Mensaje de error */
    .field-error { font-size: .75rem; color: var(--danger); margin-top: 4px; display: none; }
    /* Mostrar error */
    .field-error.visible { display: block; }

    
    /* Contenedor de los botones tipo “pill” (opciones tipo radio) */
    .radio-pills { display: flex; gap: 8px; flex-wrap: wrap; /* Permite que bajen de línea si no entran */ }
    /* Oculta el input real */
    .radio-pill input[type="radio"] { display: none; }
   /* Estilo visual del botón */
    .radio-pill label {
        display: flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 10px; cursor: pointer;
        border: 1px solid var(--border); background: transparent;
        font-size: .88rem; font-family: 'DM Sans', sans-serif; color: var(--text-soft);
        transition: all .18s;
    }
    /* Icono dentro del pill */
    .radio-pill label i { font-size: 1rem; }
    /* Estado seleccionado general */
    .radio-pill input:checked + label {
        background: rgba(91,127,255,.1); border-color: var(--accent); color: var(--accent); font-weight: 500;
    }
    /* Variantes por tipo */
    .radio-pill.pago    input:checked + label { background: rgba(251,191,36,.1); border-color: var(--warn); color: var(--warn); }
    .radio-pill.activo  input:checked + label { background: rgba(52,211,153,.1); border-color: var(--success); color: var(--success); }
    .radio-pill.inactivo input:checked + label { background: rgba(100,116,139,.1); border-color: var(--text-muted); color: var(--text-muted); }

    /* Contenedor del campo precio (inicialmente oculto) */
    #precio-wrap {
        overflow: hidden; max-height: 0;
        /* Animaciones suaves */
        transition: max-height .35s cubic-bezier(.4,0,.2,1), opacity .3s ease, margin .3s;
        opacity: 0; margin-top: 0;
    }
    /* Estado visible del precio */
    #precio-wrap.visible { max-height: 100px; opacity: 1; margin-top: 1rem; }
    /* Contenedor del input de precio */
    .precio-input-wrap { position: relative; }
    /* Símbolo $ dentro del input */
    .precio-input-wrap .peso-sign {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); font-weight: 700; font-size: .95rem; pointer-events: none;
    }
    /* Espaciado para que no tape el símbolo */
    .precio-input-wrap input { padding-left: 2.2rem !important; }

 
    /* Área tipo drag & drop */
    .drop-zone {
        border: 2px dashed var(--drop-border);
        border-radius: 14px;
        background: var(--drop-bg);
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        position: relative;
    }
    /* Hover o arrastrando archivo */
    .drop-zone:hover, .drop-zone.drag-over {
        border-color: var(--accent);
        background: rgba(91,127,255,.08);
    }
    /* Input file invisible (pero funcional) */
    .drop-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    /* Icono de la zona */
    .drop-zone .drop-icon { font-size: 2rem; color: var(--accent); opacity: .6; margin-bottom: .6rem; }
    /*Titulo*/
    .drop-zone .drop-title { font-family: 'Syne', sans-serif; font-size: .95rem; font-weight: 700; margin-bottom: .25rem; }
    /* Texto de ayuda */
    .drop-zone .drop-hint { font-size: .78rem; color: var(--text-muted); }
    /* Chip que muestra archivo seleccionado */
    .file-selected-chip {
        display: none; align-items: center; gap: 8px;
        background: rgba(52,211,153,.08); border: 1px solid rgba(52,211,153,.2);
        border-radius: 8px; padding: 8px 12px;
        font-size: .82rem; color: var(--success);
        margin-top: .75rem; word-break: break-all; text-align: left;
    }
    /* Mostrar chip */
    .file-selected-chip.visible { display: flex; }
    /* Botón eliminar archivo */
    .file-selected-chip .remove-file { cursor: pointer; margin-left: auto; color: var(--danger); flex-shrink: 0; }

    /* Archivo existente (modo editar) */
    .existing-file {
        display: flex; align-items: center; gap: 8px;
        background: var(--bg-card-alt); border: 1px solid var(--border);
        border-radius: 8px; padding: 9px 13px;
        font-size: .82rem; color: var(--text-soft);
        margin-bottom: .75rem; word-break: break-all;
    }
    /* Icono del archivo */
    .existing-file i { color: var(--accent); }

  
    /* Tarjetas del panel lateral (sticky) */
    .sidebar-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 1.4rem;
        box-shadow: var(--shadow-card);
        position: sticky; top: 84px;
    }
    /* Separación entre cards del sidebar */
    .sidebar-card + .sidebar-card { margin-top: 1rem; }

    /* Preview card (muestra cómo quedará la publicación) */
    /* Contenedor general del mock */
    .preview-mock {
        border-radius: 12px; overflow: hidden;
        border: 1px solid var(--border); background: var(--bg-card-alt);
    }
    /* Imagen del preview */
    .preview-mock-img {
        width: 100%; height: 110px;
        background: var(--bg-card-alt);
        display: flex; align-items: center; justify-content: center;
        border-bottom: 1px solid var(--border);
        position: relative; overflow: hidden;
    }
    /* Imagen cargada */
    .preview-mock-img img { width: 100%; height: 100%; object-fit: cover; }
    /* Icono cuando no hay imagen */
    .preview-mock-img .no-img { font-size: 2rem; color: var(--accent); opacity: .3; }
    /* Contenido del preview */
    .preview-mock-body { padding: .85rem 1rem; }
    /* Título del preview */
    .preview-mock-title { font-family: 'Syne', sans-serif; font-size: .88rem; font-weight: 700; color: var(--text); margin-bottom: .3rem; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    /* Materia del preview */
    .preview-mock-materia { font-size: .75rem; color: var(--text-muted); }

    /* Badges mini */
    /* Estilo base */
    .mbadge {
        font-size: .65rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;
        font-family: 'Syne', sans-serif;
    }
    /* Tipos de badges */
    .mbadge-resumen   { background:rgba(139,92,246,.14); color:#a78bfa; border:1px solid rgba(139,92,246,.22); }
    .mbadge-apunte    { background:rgba(91,127,255,.14);  color:#7ca0ff; border:1px solid rgba(91,127,255,.22); }
    .mbadge-libro     { background:rgba(52,211,153,.12);  color:var(--success); border:1px solid rgba(52,211,153,.22); }
    .mbadge-examen    { background:rgba(251,191,36,.12);  color:var(--warn); border:1px solid rgba(251,191,36,.22); }
    .mbadge-guia      { background:rgba(56,189,248,.12);  color:var(--accent-3); border:1px solid rgba(56,189,248,.22); }
    .mbadge-otro      { background:rgba(100,116,139,.12); color:var(--text-muted); border:1px solid rgba(100,116,139,.2); }
    .mbadge-gratis    { background:rgba(52,211,153,.1); color:var(--success); border:1px solid rgba(52,211,153,.2); }
    .mbadge-pago      { background:rgba(251,191,36,.1); color:var(--warn); border:1px solid rgba(251,191,36,.2); }

    /* Tips */
    /* Item individual */
    .tip-item { display: flex; gap: 10px; align-items: flex-start; padding: .7rem 0; }
    /* Separador entre tips */
    .tip-item + .tip-item { border-top: 1px solid var(--border); }
    /* Icono del tip */
    .tip-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .95rem; }
    /* Texto del tip */
    .tip-text { font-size: .8rem; color: var(--text-soft); line-height: 1.5; }
    /* Título dentro del tip */
    .tip-text strong { color: var(--text); display: block; margin-bottom: 1px; font-family: 'Syne', sans-serif; font-size: .78rem; }

   
    /* Botón principal (submit) */
    .btn-submit {
        background: var(--gradient);
        border: none; color: #fff;
        font-family: 'Syne', sans-serif; font-weight: 700;
        font-size: .95rem; padding: 13px 28px; border-radius: 12px;
        box-shadow: 0 4px 20px rgba(91,127,255,.35);
        display: inline-flex; align-items: center; gap: 9px; white-space: nowrap;
        transition: transform .15s, box-shadow .15s, filter .15s;
        width: 100%;
        justify-content: center;
    }
    /* Hover del botón */
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(91,127,255,.5); filter: brightness(1.08); color: #fff; }
    /* Estado deshabilitado */
    .btn-submit:disabled { opacity: .6; transform: none; cursor: not-allowed; }

    /* Botón cancelar */
    .btn-cancel {
        background: transparent; border: 1px solid var(--border); color: var(--text-soft);
        font-size: .88rem; padding: 11px 20px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 7px; width: 100%; justify-content: center;
        transition: background .15s, color .15s; margin-top: .6rem;
    }
    /* Hover cancelar */
    .btn-cancel:hover { background: rgba(255,255,255,.04); color: var(--text); }

  
    /* Overlay de carga */
    .spinner-overlay {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(12,14,26,.75); backdrop-filter: blur(4px);
        align-items: center; justify-content: center; flex-direction: column; gap: 16px;
    }
    /* Activar overlay */
    .spinner-overlay.active { display: flex; }
    /* Spinner animado */
    .spinner-ring {
        width: 48px; height: 48px; border-radius: 50%;
        border: 3px solid rgba(91,127,255,.15);
        border-top-color: var(--accent);
        animation: spin .75s linear infinite;
    }
    /* Texto del spinner */
    .spinner-text { color: var(--text-soft); font-size: .88rem; font-family: 'Syne', sans-serif; }
    /* Animación */
    @keyframes spin { to { transform: rotate(360deg); } }

    
    /* Zona para subir imagen */
    .img-drop-zone {
        border: 2px dashed var(--drop-border);
        border-radius: 12px;
        background: var(--drop-bg);
        padding: 1.4rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        position: relative;
    }
    /* Hover */
    .img-drop-zone:hover { border-color: var(--accent); background: rgba(91,127,255,.06); }
    /* Input oculto */
    .img-drop-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    /* Imagen preview */
    .img-preview-thumb {
        width: 100%; max-height: 140px; object-fit: cover; border-radius: 8px;
        margin-bottom: .5rem; display: none;
    }
    /* Mostrar imagen */
    .img-preview-thumb.visible { display: block; }
    /* Icono */
    .img-drop-icon { font-size: 1.6rem; color: var(--accent); opacity: .5; margin-bottom: .4rem; }
    /* Texto */
    .img-drop-text { font-size: .8rem; color: var(--text-muted); }

    /* ══ RESPONSIVE ══ */
    /* Tablets */
    @media (max-width: 900px) {
        .form-layout { grid-template-columns: 1fr; }
        .sidebar-card { position: static; }
    }
    /* Móviles */
    @media (max-width: 576px) {
        .form-hero h1 { font-size: 1.35rem; }
        .radio-pills { flex-direction: column; }
        .radio-pill label { justify-content: center; }
    }

    /* ══ REQUIRED CAMPOS OBLIGATORIOS * ══ */
    .req { color: var(--danger); margin-left: 2px; }

    /* scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(128,128,128,.2); border-radius: 3px; }
    </style>
</head>
<body>


<!-- Overlay que se muestra mientras se envía el formulario -->
<div class="spinner-overlay" id="spinner">
    <!-- Animación circular -->
    <div class="spinner-ring"></div>
    <!-- Texto dinámico según modo (editar o crear) -->
    <span class="spinner-text"><?= $modoEdicion ? 'Guardando cambios…' : 'Publicando material…' ?></span>
</div>

<!-- Barra superior de navegación -->
<nav class="univia-navbar">
    <div class="container-lg">
        <div class="d-flex align-items-center gap-3">
        <!-- Logo / nombre de la app -->    
        <a href="<?= site_url('publicaciones/propias') ?>" class="brand-name">Univia</a>
         <!-- Sección derecha -->    
        <div class="ms-auto d-flex align-items-center gap-2">
         <!-- Dropdown del usuario -->       
        <div class="dropdown">
        <!--Avatar con iniciales-->
                    <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                   </div>
                    <!-- Menú desplegable -->
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                    
                         <!-- Separador -->
                        <li><hr class="dropdown-divider"></li>
                        <!-- Toggle de tema (dark/light) -->
                        <li>
                            <div class="theme-row" id="theme-row">
                                <div class="theme-row-left">
                                    <i class="bi bi-moon-stars-fill t-icon" id="t-icon"></i>
                                    <span id="t-label">Modo nocturno</span>
                                </div>
                                <!-- Switch -->
                                <label class="t-switch" title="Cambiar tema" onclick="event.stopPropagation()">
                                    <input type="checkbox" id="t-checkbox">
                                    <span class="t-track"></span>
                                </label>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                         <!-- Links del usuario -->
                        <li><a class="dropdown-item" href="<?= site_url('perfil') ?>"><i class="bi bi-person-circle"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('publicaciones/propias') ?>"><i class="bi bi-speedometer2"></i> Mis publicaciones</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Cerrar sesión -->
                        <li><a class="dropdown-item danger" href="<?= site_url('') ?>" style="color:var(--danger);"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>



<!-- Encabezado del formulario -->
<section class="form-hero">
    <div class="container-lg">
    <!-- Breadcrumb (navegación) -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Mi Panel</a></li>
                 <!-- Texto dinámico -->
                <li class="breadcrumb-item active"><?= $modoEdicion ? 'Editar Publicación' : 'Nueva Publicación' ?></li>
            </ol>
        </nav>
        <!-- Contenido principal del hero -->
        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
            <div>
             <!-- Badge de modo -->
                <span class="mode-badge <?= $modoEdicion ? 'editar-mode' : 'nueva' ?>">
                    <i class="bi <?= $modoEdicion ? 'bi-pencil-square' : 'bi-plus-circle' ?>"></i>
                    <?= $modoEdicion ? 'Modo edición' : 'Nueva publicación' ?>
                </span>
                 <!-- Título -->
                <h1><?= $modoEdicion ? 'Editar Publicación' : 'Subir nuevo material' ?></h1>
                <!-- Descripción -->
                <p class="subtitle mb-0">
                    <?= $modoEdicion
                        ? 'Modificá los campos que necesitás actualizar y guardá los cambios.'
                        : 'Completá todos los campos para compartir tu material con la comunidad.' ?>
                </p>
            </div>
        </div>
    </div>
</section>


<!--Formulario principal-->
<div class="container-lg">
<div class="form-layout">

    <!-- ── COLUMNA PRINCIPAL ── -->
    <div>

        <!--Accion del formulario -->
        <form id="pub-form"
              action="<?= $modoEdicion 
                  ? site_url('publicaciones/actualizar/' . (int)($pub['id_publicacion'] ?? 0))
                  : site_url('publicaciones/guardar') ?>"
              method="POST"
              enctype="multipart/form-data"
              novalidate>

            <?php if ($modoEdicion): ?>
            <!-- ID de la publicación (solo si se edita) -->
            <input type="hidden" name="id" value="<?= (int)($pub['id_publicacion'] ?? 0) ?>">
            <?php endif; ?>


            <!-- Informacion basica -->
            <div class="form-card">
             <!-- Título de sección -->
                <div class="card-section-title">
                    <i class="bi bi-info-circle"></i> Información básica
                </div>

                <!-- Título -->
                <div class="mb-4">
                    <label class="field-label" for="titulo">
                        <span class="required-dot"></span> Título <span class="req">*</span>
                    </label>
                    <!-- Input título -->
                    <input type="text" id="titulo" name="titulo"
                           class="form-control"
                           placeholder="Ej: Apuntes de Análisis Matemático II — 2024"
                           value="<?= fv($pub, 'titulo') ?>"
                           maxlength="255" required>
                           <!-- Error -->
                    <div class="field-error" id="err-titulo">Por favor ingresá un título.</div>
                    <!-- Ayuda -->
                    <div class="field-hint">Sé descriptivo: incluí la materia y el año si aplica.</div>
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label class="field-label" for="descripcion">
                        <span class="required-dot"></span> Descripción <span class="req">*</span>
                    </label>
                    <textarea id="descripcion" name="descripcion"
                              class="form-control"
                              placeholder="Describí el contenido del material: temas cubiertos, año de cursada, condición si es libro físico, etc."
                              rows="4" required><?= fv($pub, 'descripcion') ?></textarea>
                    <div class="field-error" id="err-descripcion">Por favor ingresá una descripción.</div>
                </div>

                <!-- Materia -->
                <div class="mb-0">
                    <label class="field-label" for="materia">
                        <span class="required-dot"></span> Materia <span class="req">*</span>
                    </label>
                    <select id="materia" name="materia" class="form-select" required>
    <option value="" disabled selected>— Seleccioná la materia —</option>
    <option value="1">Programación I</option> 
    <option value="2">Análisis Matemático II</option>
    <option value="3">Álgebra y Geometría</option>
    <option value="4">Ingeniería de software 2</option>
    <option value="5">Economia aplicada</option>
</select>
                        
                    <div class="field-error" id="err-materia">Por favor ingresá el nombre de la materia.</div>
                </div>
            </div>


            <!-- tipo y acuerdo -->
            <div class="form-card">
                <div class="card-section-title">
                    <i class="bi bi-tag"></i> Tipo de recurso y acuerdo
                </div>

                <!-- Tipo de recurso -->
                <div class="mb-4">
                    <label class="field-label" for="tipo_recurso">
                        <span class="required-dot"></span> Tipo de recurso <span class="req">*</span>
                    </label>
                    <select id="tipo_recurso" name="tipo_recurso" class="form-select" required>
                    <!-- Opción por defecto -->    
                    <option value="" disabled <?= empty($pub['tipo_recurso']) ? 'selected' : '' ?>>— Seleccioná un tipo —</option>
                     <!-- Opciones -->    
                        <option value="resumen"    <?= fsel($pub,'tipo_recurso','resumen') ?>>📄 Resumen</option>
                        <option value="apunte"     <?= fsel($pub,'tipo_recurso','apunte') ?>>📝 Apunte de clase</option>
                        <option value="libro"      <?= fsel($pub,'tipo_recurso','libro') ?>>📚 Libro</option>
                        <option value="examen"     <?= fsel($pub,'tipo_recurso','examen') ?>>📋 Examen / Parcial</option>
                        <option value="guia"       <?= fsel($pub,'tipo_recurso','guia') ?>>📘 Guía de ejercicios</option>
                        <option value="otro"       <?= fsel($pub,'tipo_recurso','otro') ?>>📁 Otro</option>
                    </select>
                    <div class="field-error" id="err-tipo-recurso">Por favor seleccioná el tipo de recurso.</div>
                </div>

                <!-- Tipo de acuerdo -->
                <div class="mb-0">
                    <label class="field-label">
                        <span class="required-dot"></span> Tipo de acuerdo <span class="req">*</span>
                    </label>
                    <!-- Opciones tipo botón -->
                    <div class="radio-pills" id="radio-acuerdo">
                    <!-- GRATIS -->
                        <div class="radio-pill gratis">
                            <input type="radio" id="ac-gratis" name="tipo_acuerdo" value="gratis"
                                   <?= ($pub['tipo_acuerdo'] ?? 'gratis') === 'gratis' ? 'checked' : '' ?> required>
                            <label for="ac-gratis"><i class="bi bi-gift"></i> Gratis</label>
                        </div>
                        <!-- PAGO -->
                        <div class="radio-pill pago">
                            <input type="radio" id="ac-pago" name="tipo_acuerdo" value="pago"
                                   <?= ($pub['tipo_acuerdo'] ?? '') === 'pago' ? 'checked' : '' ?>>
                            <label for="ac-pago"><i class="bi bi-currency-dollar"></i> Pago</label>
                        </div>
                    </div>
                    <div class="field-error" id="err-acuerdo">Por favor seleccioná el tipo de acuerdo.</div>

                    <!-- Campo precio (visible solo si selecciona "pago") -->
                    <div id="precio-wrap">
                        <label class="field-label" for="precio">
                            <span class="required-dot"></span> Precio <span class="req">*</span>
                        </label>
                        <div class="precio-input-wrap">
                            <span class="peso-sign">$</span>
                            <input type="number" id="precio" name="precio"
                                   class="form-control"
                                   placeholder="0.00"
                                   value="<?= fv($pub, 'precio', '') ?>"
                                   min="0" step="0.01">
                        </div>
                        <div class="field-error" id="err-precio">Por favor ingresá el precio.</div>
                        <div class="field-hint">Ingresá el precio en pesos argentinos.</div>
                    </div>
                </div>
            </div>


            <!-- Archivo y formato -->
            <div class="form-card">
                <div class="card-section-title">
                    <i class="bi bi-paperclip"></i> Formato y archivo
                </div>

                <!-- Formato del archivo -->
                <div class="mb-4">
                    <label class="field-label" for="formato_archivo">
                        <span class="required-dot"></span> Formato del material <span class="req">*</span>
                    </label>
                    <select id="formato_archivo" name="formato_archivo" class="form-select" required>
                        <option value="" disabled <?= empty($pub['formato_archivo']) ? 'selected' : '' ?>>— Seleccioná el formato —</option>
                        <option value="pdf"         <?= fsel($pub,'formato_archivo','pdf') ?>>📄 PDF</option>
                        <option value="word"        <?= fsel($pub,'formato_archivo','word') ?>>📝 Word (.doc / .docx)</option>
                        <option value="excel"       <?= fsel($pub,'formato_archivo','excel') ?>>📊 Excel (.xls / .xlsx)</option>
                        <option value="powerpoint"  <?= fsel($pub,'formato_archivo','powerpoint') ?>>📊 PowerPoint (.ppt / .pptx)</option>
                        <option value="imagen"      <?= fsel($pub,'formato_archivo','imagen') ?>>🖼️ Imagen (JPG / PNG)</option>
                        <option value="fisico"      <?= fsel($pub,'formato_archivo','fisico') ?>>📚 Libro / Material físico</option>
                        <option value="otro"        <?= fsel($pub,'formato_archivo','otro') ?>>📁 Otro</option>
                    </select>
                    <div class="field-error" id="err-formato">Por favor seleccioná el formato.</div>
                </div>

                <!-- Subida de archivo -->
                <div id="archivo-wrap">
                    <label class="field-label">
                        <span class="required-dot"></span> Archivo del material
                        <span class="req" id="archivo-req">*</span>
                    </label>
                    <!-- Si está en modo edición, muestra archivo existente -->
                    <?php if ($modoEdicion && !empty($pub['nombre_archivo'])): ?>
                    <!-- Archivo existente en modo editar -->
                    <div class="existing-file" id="existing-file">
                        <i class="bi bi-file-earmark"></i>
                        <span><?= fv($pub, 'nombre_archivo') ?></span>
                        <small style="color:var(--text-muted); margin-left:auto;">Archivo actual</small>
                    </div>
                    <div class="field-hint mb-2">Si subís un nuevo archivo, reemplazará al actual.</div>
                    <?php endif; ?>

                    <!-- Zona drag & drop -->
                    <div class="drop-zone" id="drop-zone">
                     <!-- Input oculto para subir archivo -->
                        <input type="file" id="archivo" name="archivo"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.jpeg,.png,.webp"
                               <?= ($modoEdicion && !empty($pub['nombre_archivo'])) ? '' : 'required' ?>>
                        <div id="drop-content">
                         <!-- Icono -->
                            <div class="drop-icon"><i class="bi bi-cloud-upload"></i></div>
                            <!-- Texto principal -->
                            <div class="drop-title">Arrastrá tu archivo aquí</div>
                            <!-- Tip -->
                            <div class="drop-hint">o hacé clic para buscarlo · PDF, Word, PPT, Excel, Imagen (máx. 20 MB)</div>
                        </div>
                    </div>
                     <!-- Chip que muestra archivo seleccionado -->
                    <div class="file-selected-chip" id="file-chip">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="file-name-text"></span>
                        <i class="bi bi-x-circle remove-file" id="remove-file" title="Quitar archivo"></i>
                    </div>
                    <!-- Error -->
                    <div class="field-error" id="err-archivo">Por favor seleccioná un archivo.</div>
                </div>

                <!-- Imagen de portada (opcional) -->
                <div class="mt-4">
                    <label class="field-label">Imagen de portada <span style="color:var(--text-muted);font-weight:400;">(opcional)</span></label>
                    <div class="field-hint mb-2">Una imagen de portada hace tu publicación más atractiva. JPG o PNG, máx. 5 MB.</div>

                    <?php if ($modoEdicion && !empty($pub['nombre_imagen'])): ?>
                    <div class="existing-file">
                        <i class="bi bi-image" style="color:var(--accent-2);"></i>
                        <span><?= fv($pub, 'nombre_imagen') ?></span>
                        <small style="color:var(--text-muted); margin-left:auto;">Imagen actual</small>
                    </div>
                    <?php endif; ?>

                    <!-- Input imagen -->
                    <div class="img-drop-zone" id="img-drop-zone">
                        <input type="file" id="imagen_portada" name="imagen_portada"
                               accept=".jpg,.jpeg,.png,.webp"
                               onchange="previewImagen(this)">
                        <!-- Preview de imagen -->
                        <img src="" alt="Preview" class="img-preview-thumb" id="img-thumb">
                        <div id="img-drop-content">
                        <!-- Icono -->
                            <div class="img-drop-icon"><i class="bi bi-image"></i></div>
                            <!-- Texto -->
                            <div class="img-drop-text">Hacé clic o arrastrá una imagen de portada</div>
                        </div>
                    </div>
                </div>

            </div><!-- /form-card archivo -->


            <?php if ($modoEdicion): ?>
            <!-- estado (solo en modo editar) -->
            <div class="form-card">
            <!-- Título de sección -->
                <div class="card-section-title">
                    <i class="bi bi-toggle2-on"></i> Estado de la publicación
                </div>

                 <!-- Explicación para el usuario -->
                <p style="font-size:.85rem; color:var(--text-soft); margin-bottom:1.2rem; line-height:1.6;">
                    Una publicación <strong style="color:var(--success);">activa</strong> es visible para todos los usuarios. Si la ponés <strong style="color:var(--text-muted);">inactiva</strong>, solo vos podés verla desde tu panel.
                </p>

                <!-- Opciones de estado -->
                <div class="radio-pills">
                <!-- ACTIVO -->
                    <div class="radio-pill activo">
                        <input type="radio" id="est-activo" name="estado" value="1"
                               <?= (string) ($pub['estado'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <label for="est-activo"><i class="bi bi-eye"></i> Activo</label>
                    </div>
                      <!-- INACTIVO -->
                    <div class="radio-pill inactivo">
                        <input type="radio" id="est-inactivo" name="estado" value="0"
                               <?= (string) ($pub['estado'] ?? '1') === '0' ? 'checked' : '' ?>>
                        <label for="est-inactivo"><i class="bi bi-eye-slash"></i> Inactivo</label>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- En modo nueva publicación, el estado siempre es activo -->
            <input type="hidden" name="estado" value="1">
            <?php endif; ?>

        </form>
    </div><!-- /columna principal -->


 
    <div>

        <!-- Vista previa de la card -->
        <div class="sidebar-card">
            <div class="card-section-title" style="margin-bottom:1rem;">
                <i class="bi bi-eye"></i> Vista previa
            </div>
            <!-- Card simulada -->
            <div class="preview-mock">
                <div class="preview-mock-img" id="preview-img-wrap">
                <!-- Imagen -->
                    <img src="" alt="" id="preview-img-el" style="display:none;">
                    <i class="bi bi-file-earmark no-img" id="preview-no-img"></i>
                </div>
                 <!-- Contenido -->
                <div class="preview-mock-body">
                <!-- Badges -->
                    <div class="d-flex gap-1 flex-wrap mb-2">
                        <span class="mbadge" id="preview-badge-tipo" style="display:none;"></span>
                        <span class="mbadge" id="preview-badge-acuerdo" style="display:none;"></span>
                    </div>
                    <!-- Título -->
                    <div class="preview-mock-title" id="preview-titulo">Tu título aparecerá aquí</div>
                    <!-- Materia -->
                    <div class="preview-mock-materia" id="preview-materia" style="display:none;">
                        <i class="bi bi-mortarboard me-1"></i><span id="preview-materia-text"></span>
                    </div>
                </div>
            </div>
             <!-- Botones -->
            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
            <!-- Submit -->
                <button form="pub-form" type="submit" class="btn-submit" id="btn-submit">
                    <i class="bi <?= $modoEdicion ? 'bi-floppy' : 'bi-cloud-upload' ?>"></i>
                    <?= $modoEdicion ? 'Guardar cambios' : 'Publicar material' ?>
                </button>
                <!-- Cancelar -->
<a href="<?= site_url('publicaciones/propias') ?>" class="btn-cancel">
    <i class="bi bi-arrow-left"></i> Cancelar
</a>
            </div>
        </div>

        <!-- Tips para el usuario -->
        <div class="sidebar-card" style="margin-top:1rem;">
            <div class="card-section-title" style="margin-bottom:.8rem;">
                <i class="bi bi-lightbulb"></i> Consejos
            </div>

            <!-- Tip 1 -->
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(91,127,255,.1); color:var(--accent);">
                    <i class="bi bi-type"></i>
                </div>
                <div class="tip-text">
                    <strong>Título claro</strong>
                    Incluí la materia, el tipo de material y el año. Ej: "Resumen Termodinámica 2024".
                </div>
            </div>

            <!-- Tip 2 -->
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(52,211,153,.1); color:var(--success);">
                    <i class="bi bi-image"></i>
                </div>
                <div class="tip-text">
                    <strong>Imagen de portada</strong>
                    Las publicaciones con portada reciben 3× más descargas.
                </div>
            </div>

            <!-- Tip 3 -->
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(251,191,36,.1); color:var(--warn);">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <div class="tip-text">
                    <strong>Formato PDF</strong>
                    Es el formato más compatible. Convertí tus Word o fotos escaneadas a PDF antes de subir.
                </div>
            </div>

            <!-- Tip 4 -->
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(139,92,246,.1); color:var(--accent-2);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="tip-text">
                    <strong>Contenido propio</strong>
                    Solo subí material del que tenés derechos. No infrinjas copyright.
                </div>
            </div>
        </div>

    </div><!-- /sidebar -->

</div><!-- /form-layout -->
</div><!-- /container -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* Sistema de tema*/
/*
- Permite alternar entre modo oscuro y claro
- Guarda la preferencia en localStorage
*/
(function () {
    const ROOT = document.documentElement;
    const KEY  = 'univia_theme';
    const cb   = document.getElementById('t-checkbox');
    const icon = document.getElementById('t-icon');
    const lbl  = document.getElementById('t-label');
    const CFG  = {
        dark:  { icon:'bi-moon-stars-fill', label:'Modo nocturno', checked:true  },
        light: { icon:'bi-sun-fill',        label:'Modo diurno',   checked:false },
    };
    function apply(theme, anim) {
        ROOT.dataset.theme = theme;
        const c = CFG[theme];
        cb.checked = c.checked; lbl.textContent = c.label;
        if (anim) {
            icon.style.transition = 'transform .28s ease, opacity .2s';
            icon.style.opacity = '0'; icon.style.transform = 'rotate(90deg) scale(.7)';
            setTimeout(() => {
                icon.className = 'bi ' + c.icon + ' t-icon';
                icon.style.transform = 'rotate(0deg) scale(1)'; icon.style.opacity = '1';
            }, 200);
        } else { icon.className = 'bi ' + c.icon + ' t-icon'; }
        localStorage.setItem(KEY, theme);
    }
    apply(localStorage.getItem(KEY) || 'dark', false);
    cb.addEventListener('change', function(){ apply(this.checked ? 'dark' : 'light', true); });
    document.getElementById('theme-row').addEventListener('click', function(e){
        if (e.target !== cb && e.target.tagName !== 'LABEL') {
            cb.checked = !cb.checked; apply(cb.checked ? 'dark' : 'light', true);
        }
    });
})();


/* mostrar o ocultar el precio*/
const precioWrap  = document.getElementById('precio-wrap');
const precioInput = document.getElementById('precio');
const errPrecio   = document.getElementById('err-precio');

function togglePrecio() {
    const esPago = document.getElementById('ac-pago').checked;
    precioWrap.classList.toggle('visible', esPago);
    precioInput.required = esPago;
}

document.querySelectorAll('[name="tipo_acuerdo"]').forEach(r =>
    r.addEventListener('change', togglePrecio)
);
// Al cargar la página (puede estar "pago" pre-seleccionado en modo editar)
togglePrecio();


/* la drop zone */
const dropZone   = document.getElementById('drop-zone');
const archivoInp = document.getElementById('archivo');
const fileChip   = document.getElementById('file-chip');
const fileNameTx = document.getElementById('file-name-text');
const removeFile = document.getElementById('remove-file');

function showFileChip(name) {
    fileNameTx.textContent = name;
    fileChip.classList.add('visible');
    dropZone.style.display = 'none';
}
function resetFileChip() {
    fileChip.classList.remove('visible');
    dropZone.style.display = '';
    archivoInp.value = '';
}

archivoInp.addEventListener('change', function() {
    if (this.files[0]) showFileChip(this.files[0].name);
});
removeFile && removeFile.addEventListener('click', resetFileChip);

// Drag & drop visual
['dragover','dragenter'].forEach(ev => dropZone.addEventListener(ev, e => {
    e.preventDefault(); dropZone.classList.add('drag-over');
}));
['dragleave','drop'].forEach(ev => dropZone.addEventListener(ev, e => {
    e.preventDefault(); dropZone.classList.remove('drag-over');
}));
dropZone.addEventListener('drop', e => {
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        archivoInp.files = dt.files;
        showFileChip(file.name);
    }
});


/* vista previa de la imagen de portada */
function previewImagen(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const thumb = document.getElementById('img-thumb');
        const dc    = document.getElementById('img-drop-content');
        thumb.src = e.target.result;
        thumb.classList.add('visible');
        dc.style.opacity = '.4';
        // Actualizar preview de la card
        document.getElementById('preview-img-el').src = e.target.result;
        document.getElementById('preview-img-el').style.display = 'block';
        document.getElementById('preview-no-img').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}


/* la vista previa en tiempo real */
const TIPO_LABEL = {
    resumen:'Resumen', apunte:'Apunte', libro:'Libro',
    examen:'Examen', guia:'Guía', otro:'Otro'
};
const TIPO_BADGE_CLASS = {
    resumen:'mbadge-resumen', apunte:'mbadge-apunte', libro:'mbadge-libro',
    examen:'mbadge-examen', guia:'mbadge-guia', otro:'mbadge-otro'
};
const ACUERDO_LABEL = { gratis:'Gratis', pago:'Pago' };
const ACUERDO_BADGE_CLASS = {
    gratis:'mbadge-gratis', pago:'mbadge-pago'
};

function updatePreview() {
    // Título
    const titulo = document.getElementById('titulo').value.trim();
    const prevTitulo = document.getElementById('preview-titulo');
    prevTitulo.textContent = titulo || 'Tu título aparecerá aquí';
    prevTitulo.style.color = titulo ? 'var(--text)' : 'var(--text-muted)';

    // Materia
    const materia = document.getElementById('materia').value.trim();
    const prevMat = document.getElementById('preview-materia');
    document.getElementById('preview-materia-text').textContent = materia;
    prevMat.style.display = materia ? '' : 'none';

    // Badge tipo recurso
    const tipoR  = document.getElementById('tipo_recurso').value;
    const bTipo  = document.getElementById('preview-badge-tipo');
    bTipo.className  = 'mbadge ' + (TIPO_BADGE_CLASS[tipoR] || '');
    bTipo.textContent = TIPO_LABEL[tipoR] || '';
    bTipo.style.display = tipoR ? '' : 'none';

    // Badge tipo acuerdo
    const tipoA  = document.querySelector('[name="tipo_acuerdo"]:checked');
    const bAcu   = document.getElementById('preview-badge-acuerdo');
    if (tipoA) {
        bAcu.className  = 'mbadge ' + (ACUERDO_BADGE_CLASS[tipoA.value] || '');
        bAcu.textContent = ACUERDO_LABEL[tipoA.value] || '';
        bAcu.style.display = '';
    } else { bAcu.style.display = 'none'; }
}

document.getElementById('titulo').addEventListener('input', updatePreview);
document.getElementById('materia').addEventListener('input', updatePreview);
document.getElementById('tipo_recurso').addEventListener('change', updatePreview);
document.querySelectorAll('[name="tipo_acuerdo"]').forEach(r => r.addEventListener('change', updatePreview));
updatePreview(); // estado inicial


/* Validacion */
const form      = document.getElementById('pub-form');
const spinner   = document.getElementById('spinner');
const btnSubmit = document.getElementById('btn-submit');

function clearErrors() {
    document.querySelectorAll('.field-error.visible').forEach(el => el.classList.remove('visible'));
    document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
}
function showError(fieldId, errId) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errId);
    if (field) field.classList.add('invalid');
    if (err)   err.classList.add('visible');
    return false;
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    clearErrors();
    let valid = true;

    // Título
    if (!document.getElementById('titulo').value.trim()) {
        showError('titulo', 'err-titulo'); valid = false;
    }
    // Descripción
    if (!document.getElementById('descripcion').value.trim()) {
        showError('descripcion', 'err-descripcion'); valid = false;
    }
    // Materia
    if (!document.getElementById('materia').value.trim()) {
        showError('materia', 'err-materia'); valid = false;
    }
    // Tipo recurso
    if (!document.getElementById('tipo_recurso').value) {
        showError('tipo_recurso', 'err-tipo-recurso'); valid = false;
    }
    // Tipo acuerdo
    const acuerdoChecked = document.querySelector('[name="tipo_acuerdo"]:checked');
    if (!acuerdoChecked) {
        document.getElementById('err-acuerdo').classList.add('visible'); valid = false;
    }
    // Precio (si es pago)
    if (acuerdoChecked && acuerdoChecked.value === 'pago') {
        const pv = parseFloat(document.getElementById('precio').value);
        if (isNaN(pv) || pv < 0) {
            showError('precio', 'err-precio'); valid = false;
        }
    }
    // Formato archivo
    if (!document.getElementById('formato_archivo').value) {
        showError('formato_archivo', 'err-formato'); valid = false;
    }
    // Archivo — requerido solo si: nueva publicación, O si no hay archivo existente
    const archivoInp = document.getElementById('archivo');
    const existingFile = document.getElementById('existing-file');
    const esLibroFisico = document.getElementById('formato_archivo').value === 'fisico';
    const tieneArchivoExistente = !!existingFile;

    if (!esLibroFisico && !tieneArchivoExistente && archivoInp.files.length === 0) {
        document.getElementById('err-archivo').classList.add('visible');
        valid = false;
    }

    if (!valid) {
        // Scroll al primer error
        const firstErr = document.querySelector('.field-error.visible');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Todo OK → mostrar spinner y enviar
    btnSubmit.disabled = true;
    spinner.classList.add('active');
    this.submit();
});

// Al cambiar formato, actualizar si archivo es requerido
document.getElementById('formato_archivo').addEventListener('change', function() {
    const esLibroFisico = this.value === 'fisico';
    const req  = document.getElementById('archivo-req');
    const wrap = document.getElementById('archivo-wrap');
    // Para libro físico ocultamos el drop zone de archivo (no tiene archivo digital)
    if (esLibroFisico) {
        wrap.style.opacity = '.5';
        wrap.style.pointerEvents = 'none';
        if (req) req.style.display = 'none';
    } else {
        wrap.style.opacity = '1';
        wrap.style.pointerEvents = 'auto';
        if (req) req.style.display = '';
    }
});
// Trigger al cargar si ya hay valor (modo editar)
document.getElementById('formato_archivo').dispatchEvent(new Event('change'));
</script>
<script>
fetch('/api/materias')
  .then(res => res.json())
  .then(data => {
    let select = document.getElementById('materia');

    data.forEach(m => {
      let option = document.createElement('option');
      option.value = m.id_materia;
      option.text = m.nombre_materia;
      select.appendChild(option);
    });
  });
</script>
</body>
</html>s