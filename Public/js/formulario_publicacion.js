
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
    if (this.files[0]) {
        showFileChip(this.files[0].name);
    }
    // Siempre validar, incluso si se quita el archivo
    validarCoherenciaArchivoFormato();
});
removeFile && removeFile.addEventListener('click', () => {
    resetFileChip();
    validarCoherenciaArchivoFormato(); // Validar al quitar
});

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
        archivoInp.dispatchEvent(new Event('change')); // Disparamos el evento change para que se valide
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

        // UX Improvement: si no hay un archivo principal, auto-seleccionar formato 'imagen'
        if (document.getElementById('archivo').files.length === 0) {
            const formatoSelect = document.getElementById('formato_archivo');
            formatoSelect.value = 'imagen';
            formatoSelect.dispatchEvent(new Event('change')); // Para actualizar la vista previa
        }
    };
    reader.readAsDataURL(input.files[0]);
}


/* la vista previa en tiempo real */
let TIPO_LABEL = {
    resumen:'Resumen', apunte:'Apunte', libro:'Libro',
    examen:'Examen', guia:'Guía', otro:'Otro'
};
let TIPO_BADGE_CLASS = {
    resumen:'mbadge-resumen', apunte:'mbadge-apunte', libro:'mbadge-libro',
    examen:'mbadge-examen', guia:'mbadge-guia', otro:'mbadge-otro'
};
const ACUERDO_LABEL = { gratis:'Gratis', pago:'Pago' };
const ACUERDO_BADGE_CLASS = {
    gratis:'mbadge-gratis', pago:'mbadge-pago'
};
const FORMATO_ICONO = {
    'pdf': 'bi-file-earmark-pdf',
    'word': 'bi-file-earmark-word',
    'excel': 'bi-file-earmark-excel',
    'powerpoint': 'bi-file-earmark-slides',
    'imagen': 'bi-image',
    'comprimido': 'bi-file-earmark-zip',
    'fisico': 'bi-book'
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
    const badgeTipo  = document.getElementById('preview-badge-tipo');
    badgeTipo.className  = 'mbadge ' + (TIPO_BADGE_CLASS[tipoR] || '');
    badgeTipo.textContent = TIPO_LABEL[tipoR] || '';
    badgeTipo.style.display = tipoR ? '' : 'none';

    // Badge tipo acuerdo
    const tipoA  = document.querySelector('[name="tipo_acuerdo"]:checked');
    const bAcu   = document.getElementById('preview-badge-acuerdo');
    if (tipoA) {
        bAcu.className  = 'mbadge ' + (ACUERDO_BADGE_CLASS[tipoA.value] || '');
        bAcu.textContent = ACUERDO_LABEL[tipoA.value] || '';
        bAcu.style.display = '';
    } else { bAcu.style.display = 'none'; }

    // Icono de archivo
    const formato = document.getElementById('formato_archivo').value;
    const previewIcon = document.getElementById('preview-no-img');
    const previewImg = document.getElementById('preview-img-el');

    // Si hay una imagen de portada, esa tiene prioridad.
    if (previewImg.style.display === 'block') {
        previewIcon.style.display = 'none';
    } else {
        // Si no, mostramos el icono del formato.
        previewIcon.style.display = 'block';
        const iconClass = FORMATO_ICONO[formato] || 'bi-file-earmark';
        previewIcon.className = `bi ${iconClass} no-img`;
    }
}

document.getElementById('titulo').addEventListener('input', updatePreview);
document.getElementById('materia').addEventListener('input', updatePreview);
document.getElementById('tipo_recurso').addEventListener('change', updatePreview);
document.querySelectorAll('[name="tipo_acuerdo"]').forEach(r => r.addEventListener('change', updatePreview));
document.getElementById('formato_archivo').addEventListener('change', updatePreview);

/* LÓGICA DE VALIDACIÓN DE FORMATO */

// Asocia el 'slug' del formato con las extensiones de archivo permitidas.
const MAPA_FORMATO_EXTENSION = {
    'pdf': ['pdf'],
    'word': ['doc', 'docx'],
    'excel': ['xls', 'xlsx'],
    'powerpoint': ['ppt', 'pptx'],
    'imagen': ['jpg', 'jpeg', 'png', 'webp'],
    'comprimido': ['zip', 'rar'],
    'fisico': [] // 'fisico' no tiene extensiones, es un caso especial.
};

/**
 * Valida si la extensión del archivo seleccionado corresponde al formato elegido.
 * Muestra u oculta el mensaje de error correspondiente.
 * @returns {boolean} - true si es válido, false si no.
 */
function validarCoherenciaArchivoFormato() {
    const formatoSelect = document.getElementById('formato_archivo');
    const archivoInput = document.getElementById('archivo');
    const errArchivo = document.getElementById('err-archivo');
    const dropZone = document.getElementById('drop-zone');

    const formatoSeleccionado = formatoSelect.value;
    const archivo = archivoInput.files[0];

    // Si no hay formato, no hay archivo, o es físico, no hay nada que validar aquí.
    // Ocultamos cualquier error previo de formato.
    if (!formatoSeleccionado || !archivo || formatoSeleccionado === 'fisico') {
        errArchivo.style.display = 'none';
        errArchivo.classList.remove('visible'); // Para la validación de 'requerido'
        dropZone.classList.remove('has-error');
        return true;
    }

    const extensionArchivo = archivo.name.split('.').pop().toLowerCase();
    const extensionesPermitidas = MAPA_FORMATO_EXTENSION[formatoSeleccionado];

    if (extensionesPermitidas && !extensionesPermitidas.includes(extensionArchivo)) {
        const nombreFormato = formatoSelect.options[formatoSelect.selectedIndex].text;
        errArchivo.textContent = `El archivo no es un ${nombreFormato}. Por favor, subí un archivo con la extensión correcta (${extensionesPermitidas.join(', ')}).`;
        errArchivo.style.display = 'block';
        dropZone.classList.add('has-error');
        return false;
    }

    errArchivo.style.display = 'none';
    errArchivo.classList.remove('visible');
    dropZone.classList.remove('has-error');
    return true;
};

/* Validacion */
const form      = document.getElementById('pub-form');
const spinner   = document.getElementById('spinner');
const btnSubmit = document.getElementById('btn-submit');
const errorModalElement = document.getElementById('errorModal');
const errorModal = errorModalElement ? new bootstrap.Modal(errorModalElement, {
    keyboard: false // Opcional: previene que se cierre con ESC
}) : null;

/** 
 * Populates a <select> element from an API endpoint.
 * @param {string} selectId - The ID of the select element.
 * @param {string} apiUrl - The URL of the API endpoint.
 * @param {object} options - Configuration options.
 * @param {string} options.valueField - The field name for the option value.
 * @param {string} options.textField - The field name for the option text.
 * @param {string} [options.preselectedId] - The ID to preselect (for edit mode).
 * @param {string} [options.placeholder] - The placeholder text.
 */
function populateSelect(selectId, apiUrl, { valueField, textField, preselectedId = null, placeholder = '— Seleccioná una opción —' }) {
    const select = document.getElementById(selectId);
    if (!select) return;

    fetch(apiUrl)
        .then(res => res.json())
        .then(response => {
            if (!response.success || !response.data) {
                select.innerHTML = `<option value="">Error al cargar datos</option>`;
                return;
            }

            select.innerHTML = `<option value="" disabled>${placeholder}</option>`;

            response.data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                if (preselectedId && String(item[valueField]) === String(preselectedId)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            // Trigger change for any dependent logic
            select.dispatchEvent(new Event('change'));
        })
        .catch(err => {
            console.error(`Error populating ${selectId}:`, err);
            select.innerHTML = `<option value="">Error de red</option>`;
        });
}

function clearErrors() {
    document.querySelectorAll('.field-error.visible').forEach(el => el.classList.remove('visible'));
    document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
}
function showError(fieldId, errId) {
    const field = document.getElementById(fieldId);
    const err = document.getElementById(errId);
    if (field) field.classList.add('invalid');
    if (err) err.classList.add('visible');
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
    // Archivo (Validación combinada)
    const archivoInp = document.getElementById('archivo');
    const imagenPortadaInp = document.getElementById('imagen_portada');
    const tieneArchivoExistente = !!document.getElementById('existing-file');
    const esLibroFisico = document.getElementById('formato_archivo').value === 'fisico';
    const errArchivo = document.getElementById('err-archivo');

    // 1. Validar si es requerido un archivo (principal o de portada)
    if (!esLibroFisico && !tieneArchivoExistente && archivoInp.files.length === 0 && imagenPortadaInp.files.length === 0) {
        errArchivo.textContent = 'Por favor, seleccioná un archivo o una imagen de portada.';
        errArchivo.style.display = 'block';
        valid = false;
    } else {
        errArchivo.style.display = 'none';
    }

    // 2. Validar coherencia de formato si se subió un archivo principal
    if (archivoInp.files.length > 0 && !validarCoherenciaArchivoFormato()) {
        valid = false;
    }

    if (!valid) {
        // Scroll al primer error visible para guiar al usuario
        const firstErr = document.querySelector('.field-error.visible, .field-error[style*="display: block"]');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Reemplazamos la alerta nativa con nuestro modal de Bootstrap
        if (errorModal) {
            errorModal.show();
        } else {
            alert('Por favor, revisá y corregí los errores marcados en el formulario antes de continuar.');
        }

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
    const wrap = document.getElementById('archivo-wrap');
    const archivoInp = document.getElementById('archivo');

    if (esLibroFisico) {
        wrap.style.display = 'none';
        // Limpiamos el archivo para que no se envíe por error y actualizamos la UI
        if (archivoInp.value) {
            resetFileChip();
        }
    } else {
        wrap.style.display = 'block';
    }
    // Re-validamos por si el usuario cambia el formato después de elegir un archivo.
    validarCoherenciaArchivoFormato();
});
// Trigger al cargar si ya hay valor (modo editar)
document.getElementById('formato_archivo').dispatchEvent(new Event('change'));


// --- INICIALIZACIÓN DE DATOS ---
window.addEventListener('DOMContentLoaded', () => {
    // 1. Cargar Materias
    populateSelect('materia', `${site_url}/api/materias`, {
        valueField: 'id_materia',
        textField: 'nombre_materia',
        preselectedId: MODO_EDICION ? DATOS_PUB.id_materia : null,
        placeholder: '— Seleccioná la materia —'
    });

    // 2. Cargar Tipos de Recurso
    populateSelect('tipo_recurso', `${site_url}/api/tipos_recurso`, {
        valueField: 'slug',
        textField: 'nombre_tipo',
        preselectedId: MODO_EDICION ? DATOS_PUB.tipo_recurso : null,
        placeholder: '— Seleccioná un tipo —'
    });

    // 3. Cargar Formatos
    populateSelect('formato_archivo', `${site_url}/api/formatos`, {
        valueField: 'slug',
        textField: 'nombre_formato',
        preselectedId: MODO_EDICION ? DATOS_PUB.formato : null, // Asumiendo que el campo se llama 'formato'
        placeholder: '— Seleccioná el formato —'
    });

    updatePreview(); // Llamar para actualizar la vista previa con los datos cargados
});
