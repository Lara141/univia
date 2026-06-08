
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
}

document.getElementById('titulo').addEventListener('input', updatePreview);
document.getElementById('materia').addEventListener('input', updatePreview);
document.getElementById('tipo_recurso').addEventListener('change', updatePreview);
document.querySelectorAll('[name="tipo_acuerdo"]').forEach(r => r.addEventListener('change', updatePreview));


/* Validacion */
const form      = document.getElementById('pub-form');
const spinner   = document.getElementById('spinner');
const btnSubmit = document.getElementById('btn-submit');

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
