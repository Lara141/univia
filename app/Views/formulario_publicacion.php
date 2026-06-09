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
    <link href="<?= base_url('Public/css/formulario_publicacion.css') ?>" rel="stylesheet">
    
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
        <a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>" class="brand-name">Univia</a>
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
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>"><i class="bi bi-speedometer2"></i> Mis publicaciones</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Cerrar sesión -->
                        <li><a class="dropdown-item danger" href="<?= site_url('auth/logout') ?>" style="color:var(--danger);"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
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
                <li class="breadcrumb-item"><a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>">Mi Panel</a></li>
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

<!-- Bloque para mostrar mensajes de error -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="container-lg mt-4">
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert"
             style="background-color: rgba(248, 113, 113, 0.1); color: var(--danger); border-radius: 12px; border-left: 5px solid var(--danger) !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.2rem;"></i>
                <div>
                    <span class="fw-bold" style="font-family: 'Syne', sans-serif;">¡Error!</span><br>
                    <?= session()->getFlashdata('error') ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: var(--close-filter);"></button>
        </div>
    </div>
<?php endif; ?>


<!--Formulario principal-->
<div class="container-lg">
<div class="form-layout">

    <!-- ── COLUMNA PRINCIPAL ── -->
    <div>

        <!--Accion del formulario -->
        <form id="pub-form"
              action="<?= $modoEdicion 
                  ? site_url('publicaciones/actualizar/' . (int)($pub['id_publicacion'] ?? 0))
                  : site_url('publicaciones/guardar/' . ($usuario['dni_usuario'] ?? '')) ?>"
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
                        <option value="" disabled selected>— Cargando materias... —</option>
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
                        <option value="" disabled selected>— Cargando tipos... —</option>
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
                        <option value="" disabled selected>— Cargando formatos... —</option>
                    </select>
                    <div class="field-error" id="err-formato">Por favor seleccioná el formato.</div>
                </div>

                <!-- Subida de archivo -->
                <div id="archivo-wrap">
                    <label class="field-label">
                        <span class="required-dot"></span> Archivo del material
                        <span class="req" id="archivo-req">*</span>
                    </label>
                    <!-- Si está en modo edición y hay un archivo, lo muestra -->
                    <?php if ($modoEdicion && !empty($pub['file_name'])): ?>
                    <!-- Archivo existente en modo editar -->
                    <div class="existing-file" id="existing-file">
                        <i class="bi bi-file-earmark"></i>
                        <span><?= fv($pub, 'file_name') ?></span>
                        <small style="color:var(--text-muted); margin-left:auto;">Archivo actual</small>
                    </div>
                    <div class="field-hint mb-2">Si subís un nuevo archivo, reemplazará al actual.</div>
                    <?php endif; ?>

                    <!-- Zona drag & drop -->
                    <div class="drop-zone" id="drop-zone">
                     <!-- Input oculto para subir archivo -->
                        <input type="file" id="archivo" name="archivo"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.jpeg,.png,.webp"
                               <?= ($modoEdicion && !empty($pub['file_name'])) ? '' : 'required' ?>>
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
                    <div class="field-error" id="err-archivo"></div>
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
<a href="<?= site_url('publicaciones/propias/' . ($usuario['dni_usuario'] ?? '')) ?>" class="btn-cancel">
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

<!-- Modal de Error de Validación -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-dark" id="errorModalLabel">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Univia
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-dark">Por favor, revisá y corregí los errores marcados en el formulario antes de continuar.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const site_url = "<?= site_url() ?>";
    const MODO_EDICION = <?= $modoEdicion ? 'true' : 'false' ?>;
    const DATOS_PUB = <?= json_encode($pub) ?>;
</script>
<script src="<?= base_url('Public/js/formulario_publicacion.js') ?>"></script>

</body> 
</html> 