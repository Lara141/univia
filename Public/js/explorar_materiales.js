document.addEventListener('DOMContentLoaded', function () {
    const modalDetalle = document.getElementById('modalDetalle');
    const modalPagoRequeridoEl = document.getElementById('modalPagoRequerido');
    const modalPagoSimuladoEl = document.getElementById('modalPagoSimulado');

    if (!modalDetalle || !modalPagoRequeridoEl || !modalPagoSimuladoEl) {
        console.error('Uno o más modales no se encontraron en el DOM.');
        return;
    }

    const modalPagoRequerido = new bootstrap.Modal(modalPagoRequeridoEl);
    const modalPagoSimulado = new bootstrap.Modal(modalPagoSimuladoEl);

    let currentPubData = {};
    let isProceedingToPayment = false; // Flag para controlar el flujo de modales

    const ARCHIVO_ICON = {
        pdf:'bi-file-earmark-pdf',   doc:'bi-file-earmark-word', docx:'bi-file-earmark-word',
        ppt:'bi-file-earmark-slides',pptx:'bi-file-earmark-slides',
        xls:'bi-file-earmark-excel', xlsx:'bi-file-earmark-excel',
        zip:'bi-file-earmark-zip',   rar:'bi-file-earmark-zip',
        jpg:'bi-image', jpeg:'bi-image', png:'bi-image', webp:'bi-image',
    };

    modalDetalle.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        currentPubData = card.dataset;

        // 1. Poblar los campos del modal de detalles
        document.getElementById('modal-titulo').textContent = currentPubData.titulo;
        document.getElementById('modal-materia').textContent = currentPubData.materia;
        document.getElementById('modal-autor').textContent = currentPubData.autor;
        document.getElementById('modal-descripcion').innerHTML = currentPubData.descripcion.replace(/\n/g, '<br>');
        document.getElementById('modal-tipo-recurso').textContent = currentPubData.tipoRecurso;
        document.getElementById('modal-tipo-acuerdo').textContent = currentPubData.tipoAcuerdo;
        document.getElementById('modal-precio').textContent = currentPubData.tipoAcuerdo === 'pago' ? `$${parseFloat(currentPubData.precio).toFixed(2)}` : 'Gratis';
        document.getElementById('modal-estado').textContent = currentPubData.estado;
        document.getElementById('modal-fecha').textContent = currentPubData.fecha;
        document.getElementById('modal-nombre-archivo').textContent = currentPubData.nombreArchivo || '—';
        document.getElementById('modal-nombre-imagen').textContent = currentPubData.nombreImagen || '—';

        // 2. Poblar los badges
        const badgesContainer = document.getElementById('modal-badges');
        badgesContainer.innerHTML = `
            <span class="badge-tipo badge-${currentPubData.tipoRecurso}">${currentPubData.tipoRecurso}</span>
            <span class="badge-acuerdo badge-${currentPubData.tipoAcuerdo}">
                <i class="bi ${currentPubData.tipoAcuerdo === 'gratis' ? 'bi-gift' : 'bi-currency-dollar'}"></i>
                ${currentPubData.tipoAcuerdo}
            </span>
        `;

        // 2.5 Lógica para la vista previa del archivo
        const previewWrap = document.getElementById('modal-preview-wrap');
        previewWrap.style.display = 'none';
        previewWrap.innerHTML = '';

        const esLibroFisico = currentPubData.esLibroFisico === '1';
        const urlImagen = currentPubData.urlImagen || '';
        const urlArchivo = currentPubData.urlArchivo || '';
        const ext = (currentPubData.nombreArchivo || '').split('.').pop().toLowerCase();

        if (urlImagen) {
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
                    <span style="font-size:.75rem; color:var(--text-muted);">${currentPubData.nombreArchivo}</span>
                 </div>`;
        }

        // 3. Lógica para el botón de descarga
        const descargaWrap = document.getElementById('modal-descarga-wrap');
        const btnDescargar = document.getElementById('modal-btn-descargar');
        
        const tieneArchivo = currentPubData.urlArchivo && currentPubData.urlArchivo !== '';
        const esPago = currentPubData.tipoAcuerdo === 'pago';
        const yaPagado = currentPubData.pagado === '1';

        if (tieneArchivo) {
            descargaWrap.style.display = 'block';
            
            if (esPago && !yaPagado) {
                // De pago, pero no comprado. El botón activará la notificación.
                btnDescargar.href = '#';
                btnDescargar.removeAttribute('target');
                btnDescargar.dataset.requiresPayment = 'true';
            } else {
                // Gratis o ya pagado. Descarga directa.
                btnDescargar.href = `${site_url}/publicaciones/descargar/${currentPubData.id}`;
                btnDescargar.setAttribute('target', '_blank');
                btnDescargar.dataset.requiresPayment = 'false';
            }
        } else {
            descargaWrap.style.display = 'none'; 
        }

        // 4. Preparar el modal de pago
        if (esPago) {
            document.getElementById('pago-titulo-material').textContent = currentPubData.titulo;
            document.getElementById('pago-autor-material').textContent = currentPubData.autor;
            document.getElementById('pago-precio-material').textContent = `$${parseFloat(currentPubData.precio).toFixed(2)}`;
            const formPago = document.getElementById('form-pago-simulado');
            const queryString = window.location.search;
            formPago.action = `${site_url}/pago/procesarPago/${currentPubData.id}${queryString}`;
        }
    });

    // Listener para el botón de descarga
    document.getElementById('modal-btn-descargar').addEventListener('click', function(e) {
        if (this.dataset.requiresPayment === 'true') {
            e.preventDefault();
            const detailModalInstance = bootstrap.Modal.getInstance(modalDetalle);
            if (detailModalInstance) {
                detailModalInstance.hide();
            }
            modalPagoRequerido.show();
        }
    });

    // Listener para el botón "Pagar" en la notificación
    const btnProcederPago = document.getElementById('btn-proceder-pago');
    if (btnProcederPago) {
        btnProcederPago.addEventListener('click', function() {
            isProceedingToPayment = true; // 1. Marcamos que vamos a pagar
            modalPagoRequerido.hide();
            // El evento 'hidden.bs.modal' se encargará de mostrar el siguiente modal
        });
    }

    // Al cerrar el modal de pago, volver al de detalles
    modalPagoSimuladoEl.addEventListener('hidden.bs.modal', function (event) {
        const detailModalInstance = bootstrap.Modal.getInstance(modalDetalle);
        if (detailModalInstance && !modalDetalle.classList.contains('show')) {
             detailModalInstance.show();
        }
    });

    // Al cerrar la notificación, decidir a dónde ir
    modalPagoRequeridoEl.addEventListener('hidden.bs.modal', function (event) {
        if (isProceedingToPayment) {
            // 2. Si marcamos que íbamos a pagar, ahora mostramos el modal de pago
            modalPagoSimulado.show();
            isProceedingToPayment = false; // 3. Reseteamos el flag para el próximo uso
        } else {
            // Si no, es que el usuario canceló (con la 'X' o el botón Cancelar), así que volvemos a los detalles
            const detailModalInstance = bootstrap.Modal.getInstance(modalDetalle);
            if (detailModalInstance && !modalDetalle.classList.contains('show')) {
                 detailModalInstance.show();
            }
        }
    });
});