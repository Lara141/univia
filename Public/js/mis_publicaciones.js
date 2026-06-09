document.addEventListener('DOMContentLoaded', function () {

    /* LÓGICA DE FILTRADO DE PUBLICACIONES */
    const filterPills = document.querySelectorAll('.filter-pill');
    const pubGrid = document.getElementById('pub-grid');
    const pubCards = pubGrid ? pubGrid.querySelectorAll('.col-12[data-tipo]') : [];

    if (filterPills.length > 0 && pubCards.length > 0) {
        filterPills.forEach(pill => {
            pill.addEventListener('click', function () {
                // 1. Actualizar estado visual de los botones
                filterPills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');

                const filterValue = this.dataset.filter;

                // 2. Filtrar las tarjetas
                pubCards.forEach(card => {
                    if (filterValue === 'todos' || card.dataset.tipo === filterValue) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    /* LÓGICA DEL MODAL DE DETALLES */
    const modalDetalle = document.getElementById('modalDetalle');
    if (modalDetalle) {
        modalDetalle.addEventListener('show.bs.modal', function (event) {
            const card = event.relatedTarget;
            const data = card.dataset;

            // Poblar campos del modal
            document.getElementById('modal-titulo').textContent = data.titulo;
            document.getElementById('modal-materia').textContent = data.materia;
            document.getElementById('modal-tipo-recurso').textContent = data.tipoRecurso;
            document.getElementById('modal-tipo-acuerdo').textContent = data.tipoAcuerdo;
            document.getElementById('modal-precio').textContent = data.tipoAcuerdo === 'pago' ? `$${parseFloat(data.precio).toFixed(2)}` : 'Gratis';
            document.getElementById('modal-estado').textContent = data.estado;
            document.getElementById('modal-fecha').textContent = data.fecha;
            document.getElementById('modal-nombre-archivo').textContent = data.nombreArchivo || '—';
            document.getElementById('modal-nombre-imagen').textContent = data.nombreImagen || '—';
            document.getElementById('modal-descripcion').innerHTML = data.descripcion.replace(/\n/g, '<br>');

            // Poblar badges
            const badgesContainer = document.getElementById('modal-badges');
            badgesContainer.innerHTML = `
                <span class="badge-tipo badge-${data.tipoRecurso}">${data.tipoRecurso}</span>
                <span class="badge-acuerdo badge-${data.tipoAcuerdo}">
                    <i class="bi ${data.tipoAcuerdo === 'gratis' ? 'bi-gift' : 'bi-currency-dollar'}"></i>
                    ${data.tipoAcuerdo}
                </span>
            `;

            // Lógica de vista previa
            const previewWrap = document.getElementById('modal-preview-wrap');
            previewWrap.style.display = 'none';
            previewWrap.innerHTML = '';
            const urlImagen = data.urlImagen || '';
            const urlArchivo = data.urlArchivo || '';
            const ext = (data.nombreArchivo || '').split('.').pop().toLowerCase();

            const ARCHIVO_ICON = {
                pdf:'bi-file-earmark-pdf', doc:'bi-file-earmark-word', docx:'bi-file-earmark-word',
                ppt:'bi-file-earmark-slides', pptx:'bi-file-earmark-slides',
                xls:'bi-file-earmark-excel', xlsx:'bi-file-earmark-excel',
                zip:'bi-file-earmark-zip', rar:'bi-file-earmark-zip',
                jpg:'bi-image', jpeg:'bi-image', png:'bi-image', webp:'bi-image',
            };

            if (urlImagen) {
                previewWrap.style.display = 'block';
                previewWrap.innerHTML = `<img src="${urlImagen}" alt="Vista previa" class="modal-cover-img">`;
            } else if (ext === 'pdf' && urlArchivo) {
                previewWrap.style.display = 'block';
                previewWrap.innerHTML = `<iframe src="${urlArchivo}#toolbar=0&navpanes=0&scrollbar=0&page=1&view=FitH" class="modal-pdf-frame" title="Vista previa PDF"></iframe>`;
            } else if (urlArchivo) {
                const ic = ARCHIVO_ICON[ext] || 'bi-file-earmark';
                previewWrap.style.display = 'block';
                previewWrap.innerHTML = `
                    <div class="modal-preview-ph">
                        <i class="bi ${ic}"></i>
                        <span>Vista previa no disponible para <strong>.${ext}</strong></span>
                        <span style="font-size:.75rem; color:var(--text-muted);">${data.nombreArchivo}</span>
                    </div>`;
            }

            // Botón de descarga
            const descargaWrap = document.getElementById('modal-descarga-wrap');
            const btnDescargar = document.getElementById('modal-btn-descargar');
            if (urlArchivo) {
                descargaWrap.style.display = 'block';
                btnDescargar.href = `${site_url}/publicaciones/descargar/${data.id}`;
            } else {
                descargaWrap.style.display = 'none';
            }

            // Botones de acción (Editar y Eliminar)
            const btnEditar = document.getElementById('modal-btn-editar');
            const btnEliminar = document.getElementById('modal-btn-eliminar');
            btnEditar.href = `${site_url}/publicaciones/editar/${data.id}`;
            btnEliminar.href = `${site_url}/publicaciones/eliminar/${data.id}`;
            btnEliminar.onclick = function(e) {
                if (!confirm('¿Estás seguro de que querés eliminar esta publicación? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            };
        });
    }

    /* LÓGICA DEL TEMA (copiada para consistencia) */
    (function () {
        const ROOT = document.documentElement;
        const KEY = 'univia_theme';
        const cb = document.getElementById('t-checkbox');
        const icon = document.getElementById('t-icon');
        const lbl = document.getElementById('t-label');
        if (!cb || !icon || !lbl) return; // Salir si no se encuentran los elementos
        const CFG = {
            dark: { icon: 'bi-moon-stars-fill', label: 'Modo nocturno', checked: true },
            light: { icon: 'bi-sun-fill', label: 'Modo diurno', checked: false },
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
        cb.addEventListener('change', function () { apply(this.checked ? 'dark' : 'light', true); });
        const themeRow = document.getElementById('theme-row');
        if (themeRow) {
            themeRow.addEventListener('click', function (e) {
                if (e.target !== cb && e.target.tagName !== 'LABEL') {
                    cb.checked = !cb.checked; apply(cb.checked ? 'dark' : 'light', true);
                }
            });
        }
    })();
});