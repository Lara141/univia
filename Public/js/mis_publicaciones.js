 

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

    // Re-crear el botón para limpiar listeners de eventos anteriores
    const newBtnDesc = btnDesc.cloneNode(true);
    btnDesc.parentNode.replaceChild(newBtnDesc, btnDesc);

    // Como propietario, siempre puedes descargar tu propio archivo si existe.
    if (urlArchivo && !esLibroFisico) {
        descWrap.style.display = '';
        // Usar siempre el endpoint de descarga segura.
        newBtnDesc.href = `${site_url}/descargar/${d.id}`;
        newBtnDesc.setAttribute('target', '_blank');
    } else {
        descWrap.style.display = 'none';
    }

    /* —— Rutas de acción —— */
    document.getElementById('modal-btn-editar').href   = `${site_url}/publicaciones/editar/${d.id}`;
    document.getElementById('modal-btn-eliminar').href = `${site_url}/publicaciones/eliminar/${d.id}`;
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



document.getElementById('modal-btn-eliminar').addEventListener('click', function (e) {
    const t = document.getElementById('modal-titulo').textContent;
    if (!confirm(`¿Seguro que querés eliminar "${t}"?\nEsta acción no se puede deshacer.`)) {
        e.preventDefault();
    }
});
