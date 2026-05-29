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

    <style>
    /* 
    temas oscuro y claro, permite cambiar dinámicamente entre modo oscuro y modo claro 
      */
    :root,
    [data-theme="dark"] {
        --bg-base:       #0c0e1a; /*Fondo principal*/
        --bg-surface:    #111422; /*secciones*/
        --bg-card:       #181c30; /*tarjetas*/
        --bg-card-alt:   #1c2035; /*Alternativa*/
        --bg-input:      rgba(255,255,255,.04);
        --border:        rgba(255,255,255,.07);
        --border-hover:  rgba(91,127,255,.38);
        --accent:        #5b7fff; /*Color principal*/
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
        /* Badges de acuerdos */
        --badge-gratis-bg:        rgba(52,211,153,.12);
        --badge-gratis-color:     #34d399;
        --badge-gratis-border:    rgba(52,211,153,.22);
        --badge-pago-bg:          rgba(251,191,36,.12);
        --badge-pago-color:       #fbbf24;
        --badge-pago-border:      rgba(251,191,36,.22);
    
        --switch-track:  rgba(255,255,255,.12);
        --preview-ph-bg: rgba(255,255,255,.03);
    }

     /* Tema claro */
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
        /* Badges */
        --badge-gratis-bg:        rgba(5,150,105,.1);
        --badge-gratis-color:     #059669;
        --badge-gratis-border:    rgba(5,150,105,.2);
        --badge-pago-bg:          rgba(217,119,6,.1);
        --badge-pago-color:       #d97706;
        --badge-pago-border:      rgba(217,119,6,.2);
        --switch-track:  rgba(0,0,0,.1);
        --preview-ph-bg: rgba(0,0,0,.02);
    }

    /* ── transicion al cambiar tema ── */
    html { transition: background-color .28s ease; }
    body, .univia-navbar, .page-hero, .stat-chip, .pub-card,
    .card-preview, .modal-content, .dropdown-menu, .detail-desc-box,
    .file-name-chip { transition: background-color .28s ease, border-color .28s ease, color .2s ease; }

    /* Estilos*/
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
        background-color: var(--bg-base);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: .95rem;
        min-height: 100vh;
    }
    /* Tipografía para títulos */
    h1,h2,h3,h4,h5,.brand-name { font-family: 'Syne', sans-serif; }
    a { text-decoration: none; }

    /* cabecera */
    .univia-navbar {
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        padding: .6rem 0;
        position: sticky; top: 0; z-index: 1030;
        backdrop-filter: blur(14px);
    }
    /* Logo*/
    .brand-name {
        font-size: 1.45rem; font-weight: 800; letter-spacing: -.5px;
        background: var(--gradient);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* buscador */
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

    /* avatar del usuario*/
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--gradient);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; color: #fff;
        cursor: pointer; border: 2px solid rgba(91,127,255,.4);
        user-select: none;
    }

     /* menu */
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
     /* Track del switch */
    .t-track {
        position: absolute; inset: 0;
        background: var(--switch-track);
        border: 1px solid var(--border);
        border-radius: 22px; cursor: pointer;
        transition: background .25s;
    }
    /* Botón interno del switch */
    .t-track::before {
        content: '';
        position: absolute; left: 3px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px;
        background: #fff; border-radius: 50%;
        transition: transform .25s;
        box-shadow: 0 1px 4px rgba(0,0,0,.25);
    }
      /* Estado activo */
    .t-switch input:checked + .t-track { background: var(--accent); border-color: var(--accent); }
    .t-switch input:checked + .t-track::before { transform: translate(18px,-50%); }

    .t-icon { font-size: 1rem; }

    /* cabecera */
    .page-hero {
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        padding: 2rem 0 1.8rem;
    }
    .page-hero h1 { font-size: 1.85rem; font-weight: 700; margin-bottom: .2rem; letter-spacing: -.4px; }
    .page-hero .subtitle { color: var(--text-muted); font-size: .92rem; }

     /* boton de nueva publicacion */
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

     /*tarjetas estaticas */
    .stat-chip {
        background: var(--bg-card);
        border: 1px solid var(--border); border-radius: 12px;
        padding: 12px 18px; display: flex; align-items: center; gap: 12px;
        box-shadow: var(--shadow-card);
    }
    .stat-chip .icon-wrap { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .stat-chip .stat-value { font-family: 'Syne', sans-serif; font-size: 1.35rem; font-weight: 700; line-height: 1; }
    .stat-chip .stat-label { font-size: .77rem; color: var(--text-muted); margin-top: 2px; }

     /* filtros */
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
    .filtros-card{
    position: sticky;
    top: 100px;
}

.filtro-titulo{
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    margin-bottom: .9rem;
    font-family: 'Syne', sans-serif;
    font-weight: 700;
}

.filtro-divider{
    border-color: var(--border);
    opacity: 1;
    margin: 0;
}
.w-48{
    width: 48%;
    text-align: center;
}
    .filter-pill:hover, .filter-pill.active { background: rgba(91,127,255,.1); border-color: var(--accent); color: var(--accent); }

    /* tarjetas de publicaciones */
    .pub-card {
        background: var(--bg-card);
        border: 1px solid var(--border); border-radius: 16px;
        overflow: hidden; display: flex; flex-direction: column; height: 100%;
        box-shadow: var(--shadow-card); cursor: pointer;
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .pub-card:hover { border-color: var(--border-hover); box-shadow: var(--glow); transform: translateY(-2px); }

   
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

    
    .modal-content { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; color: var(--text); box-shadow: 0 20px 60px rgba(0,0,0,.45); }
    .modal-header  { border-bottom: 1px solid var(--border); padding: 1.3rem 1.6rem .9rem; }
    .modal-body    { padding: 1.4rem 1.6rem; }
    .modal-footer  { border-top: 1px solid var(--border); padding: .85rem 1.6rem; }
    .modal-title   { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; }
    .btn-close     { filter: var(--close-filter); }

    /* contenedor de la vista previa de la imagen o documento*/
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

    /* botones */
    
    /* Descargar */
    .btn-descargar { background: rgba(52,211,153,.08); border: 1px solid rgba(52,211,153,.22); color: var(--success); font-size: .86rem; padding: 8px 16px; border-radius: 9px; display: inline-flex; align-items: center; gap: 7px; transition: background .15s; }
    .btn-descargar:hover { background: rgba(52,211,153,.16); color: var(--success); }

   
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(128,128,128,.2); border-radius: 3px; }
    /* ===== BUSCADOR GRANDE ===== */

.search-box-big{
    max-width: 950px;
    margin: 0 auto;
}

.search-group{
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: var(--shadow-card);
}

.search-group .input-group-text{
    background: transparent;
    border: none;
    color: var(--text-muted);
    padding-left: 1.2rem;
}

.search-big-input{
    background: transparent !important;
    border: none !important;
    color: var(--text) !important;
    padding: 1rem;
    font-size: 1rem;
}
 .search-big-input::placeholder{
    color: var(--text-soft);
    opacity: 1;
}
.search-big-input:focus{
    box-shadow: none !important;
    outline: none;
}

.search-group:focus-within{
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(91,127,255,.15);
}

.btn-buscar{
    background: var(--gradient);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0 1.5rem;
}
    @media (max-width:576px) {
        .page-hero h1 { font-size: 1.45rem; }
        .btn-nueva-lbl { display: none; }
        .detail-grid { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>

<nav class="univia-navbar">
    <div class="container-lg">
        <div class="d-flex align-items-center gap-3">

           <a href="<?= site_url('publicaciones/propias') ?>" class="brand-name">Univia</a>

            

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
                         <a class="dropdown-item" href="<?= site_url('publicaciones/propias') ?>">
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

   <a href="<?= site_url('publicaciones/propias') ?>" class="btn btn-nueva">
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
    <form action="<?= site_url('publicaciones/explorar') ?>" method="GET">
        <div class="input-group search-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>

            <input 
                type="text"
                name="q"
                class="form-control search-big-input"
                placeholder="¿Qué estás buscando hoy? (ej. Álgebra, Resumen...)"
            >

            <button class="btn btn-buscar" type="submit">
                Buscar
            </button>
        </div>
    </form>
</div>

    <form class="d-md-none mb-3" action="<?= site_url('materiales/buscar') ?>" method="GET">
        <div class="search-wrap" style="max-width:100%;">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control search-input"
                   placeholder="Buscar materiales…" autocomplete="off">
        </div>
    </form>

    <div class="row g-4">

    <!-- FILTROS -->
    <div class="col-lg-3">

            <div class="pub-card filtros-card p-3">

            <h5 class="mb-4">Filtros</h5>

            <!-- TIPO DE RECURSO -->
            <div class="mb-4">

                <h6 class="filtro-titulo">
                    Tipo de recurso
                </h6>

                <div class="d-flex flex-wrap gap-2">

                    <button class="filter-pill w-48 active" data-filter="todos">
                        Todos
                    </button>

                    <button class="filter-pill w-48"  data-filter="resumen">
                        Resúmenes
                    </button>

                    <button class="filter-pill w-48"  data-filter="apunte">
                        Apuntes
                    </button>

                    <button class="filter-pill w-48" data-filter="examen">
                        Exámenes
                    </button>

                    <button class="filter-pill w-48"  data-filter="libro">
                        Libros
                    </button>

                    <button class="filter-pill w-48" data-filter="guia">
                        Guías
                    </button>

                </div>

            </div>

            <hr class="filtro-divider">

            <!-- DISPONIBILIDAD -->
            <div class="my-4">

                <h6 class="filtro-titulo">
                    Disponibilidad
                </h6>

                <div class="d-flex flex-wrap gap-2">

                    <button class="filter-pill w-48">
                        Gratis
                    </button>

                    <button class="filter-pill w-48">
                        Todos
                    </button>

                    <button class="filter-pill w-48">
                        De pago
                    </button>

                </div>

            </div>

            <hr class="filtro-divider">

            <!-- FORMATO -->
            <div class="mt-4">

                <h6 class="filtro-titulo">
                    Formato
                </h6>

                <div class="d-flex flex-wrap gap-2">

                    <button class="filter-pill w-48" >
                        PDF
                    </button>

                    <button class="filter-pill w-48" >
                        Word
                    </button>

                    <button class="filter-pill w-48" >
                        PNG / JPG
                    </button>

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
                 data-url-imagen=""
                 data-url-archivo="<?= $ruta_archivo ?>">

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
                </div>
            </div>

        </div>
    </div>
</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

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

   
    checkbox.addEventListener('change', function () {
        apply(this.checked ? 'dark' : 'light', true);
    });

    document.getElementById('theme-row').addEventListener('click', function (e) {
        if (e.target !== checkbox && e.target.tagName !== 'LABEL') {
            checkbox.checked = !checkbox.checked;
            apply(checkbox.checked ? 'dark' : 'light', true);
        }
    });
})();


const TIPO_RECURSO_LABEL = {
    resumen: 'Resumen',           apunte:  'Apunte de clase',
    libro:   'Libro',             examen:  'Examen / Parcial',
    guia:    'Guía de ejercicios',otro:    'Otro',
};
const TIPO_ACUERDO_LABEL = {
    gratis:      'Gratuito',
    pago:        'Pago',
};
const TIPO_RECURSO_BADGE_CLASS = {
    resumen:'badge-resumen', apunte:'badge-apunte', libro:'badge-libro',
    examen:'badge-examen',   guia:'badge-guia',     otro:'badge-otro',
};
const TIPO_ACUERDO_BADGE_CLASS = {
    gratis:'badge-gratis', pago:'badge-pago', 
};
const TIPO_ACUERDO_ICON = {
    gratis:'bi-gift', pago:'bi-currency-dollar',
};
const ARCHIVO_ICON = {
    pdf:'bi-file-earmark-pdf',   doc:'bi-file-earmark-word', docx:'bi-file-earmark-word',
    ppt:'bi-file-earmark-slides',pptx:'bi-file-earmark-slides',
    xls:'bi-file-earmark-excel', xlsx:'bi-file-earmark-excel',
    zip:'bi-file-earmark-zip',   rar:'bi-file-earmark-zip',
    jpg:'bi-image', jpeg:'bi-image', png:'bi-image', webp:'bi-image',
};


const site_url = "<?= rtrim(site_url(), '/') . '/' ?>";

const modalEl = document.getElementById('modalDetalle');

modalEl.addEventListener('show.bs.modal', function (e) {
    const card = e.relatedTarget;
    if (!card) return;
    const d = card.dataset;

    /* —— Título y materia —— */
    document.getElementById('modal-titulo').textContent  = d.titulo   || '—';
    document.getElementById('modal-materia').textContent = d.materia  || '—';

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

    // Precio
    const precioVal = parseFloat(d.precio) || 0;
    if (d.tipoAcuerdo === 'pago') {
        document.getElementById('modal-precio').textContent = '$' + precioVal;
    } else {
        document.getElementById('modal-precio').textContent = 'Gratis';
    }

    // Estado
    const activo = d.estado === 'activo';
    document.getElementById('modal-estado').innerHTML =
        `<span class="status-dot ${activo ? 'status-active' : 'status-inactive'}" style="display:inline-block;margin-right:5px;"></span>` +
        (activo ? 'Activo' : 'Inactivo');

    // Nombre del archivo
    const archEl = document.getElementById('modal-nombre-archivo');
    archEl.innerHTML = d.nombreArchivo
        ? `<span class="file-name-chip"><i class="bi bi-paperclip"></i>${d.nombreArchivo}</span>`
        : '<span style="color:var(--text-muted);">Sin archivo adjunto</span>';

    // Nombre de laimagen
    const imgEl = document.getElementById('modal-nombre-imagen');
    imgEl.innerHTML = d.nombreImagen
        ? `<span class="file-name-chip"><i class="bi bi-image"></i>${d.nombreImagen}</span>`
        : '<span style="color:var(--text-muted);">Sin imagen de portada</span>';

   
    const previewWrap   = document.getElementById('modal-preview-wrap');
    previewWrap.style.display = 'none';
    previewWrap.innerHTML     = '';

    const esLibroFisico = d.esLibroFisico === '1';
    const urlImagen     = d.urlImagen  || '';
    const urlArchivo    = d.urlArchivo || '';
    const ext           = (d.nombreArchivo || '').split('.').pop().toLowerCase();

    if (urlImagen) {
        // Imagen de portada 
        previewWrap.style.display = 'block';
        const label = esLibroFisico ? 'Libro físico' : 'Vista previa';
        const labelIcon = esLibroFisico ? 'bi-book-half' : 'bi-image';
        previewWrap.innerHTML =
            `<img src="${urlImagen}" alt="Vista previa" class="modal-cover-img">
             <span class="preview-tag"><i class="bi ${labelIcon} me-1"></i>${label}</span>`;

    } else if (ext === 'pdf' && urlArchivo) {
        
        previewWrap.style.display = 'block';
        previewWrap.innerHTML =
            `<iframe src="${urlArchivo}#toolbar=0&navpanes=0&scrollbar=0&page=1&view=FitH"
                     class="modal-pdf-frame" title="Primeras páginas" loading="lazy"></iframe>
             <span class="preview-tag"><i class="bi bi-file-earmark-pdf me-1"></i>Primeras páginas</span>`;

    } else if (urlArchivo) {
        
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
    let btnDesc  = document.getElementById('modal-btn-descargar');
    
    
    const newBtnDesc = btnDesc.cloneNode(true);
    btnDesc.parentNode.replaceChild(newBtnDesc, btnDesc);

    // Solo muestra la descarga si hay URL de archivo y no es libro físico
    if (urlArchivo && !esLibroFisico) {
        descWrap.style.display = '';
        
        if (d.tipoAcuerdo === 'pago') {
            newBtnDesc.href = '#';
            newBtnDesc.removeAttribute('target');
            newBtnDesc.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Debe realizar el pago antes de poder realizar la descarga');
            });
        } else {
            newBtnDesc.href = urlArchivo;
            newBtnDesc.setAttribute('target', '_blank');
        }
    } else {
        descWrap.style.display = 'none';
    }

    /* —— Rutas de acción —— */
    document.getElementById('modal-btn-editar').href   = site_url + 'publicaciones/editar/'   + d.id;
    document.getElementById('modal-btn-eliminar').href = site_url + 'publicaciones/eliminar/' + d.id;
});


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


</script>

</body>
</html>