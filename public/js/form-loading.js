/**
 * form-loading.js — Indicador global de carga/guardado para formularios POST.
 *
 * - Muestra un overlay con spinner al enviar cualquier formulario POST.
 * - Para formularios con archivos (multipart) usa XMLHttpRequest para
 *   mostrar una barra de progreso real de la subida.
 * - Deshabilita los botones de envío para evitar envíos dobles.
 *
 * Para excluir un formulario, agregue el atributo data-no-loading.
 */
(function () {
    'use strict';

    var OVERLAY_ID = 'sgpro-loading-overlay';

    function createOverlay() {
        var overlay = document.getElementById(OVERLAY_ID);
        if (overlay) return overlay;

        overlay = document.createElement('div');
        overlay.id = OVERLAY_ID;
        overlay.setAttribute('aria-live', 'polite');
        overlay.style.cssText = [
            'position:fixed', 'inset:0', 'z-index:99999', 'display:none',
            'align-items:center', 'justify-content:center',
            'background:rgba(17,24,39,0.6)', 'backdrop-filter:blur(2px)'
        ].join(';');

        overlay.innerHTML =
            '<div style="background:#fff;border-radius:16px;padding:32px 40px;max-width:420px;width:90%;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);">' +
                '<div class="sgpro-spinner" style="margin:0 auto 16px;width:48px;height:48px;border:5px solid #e5e7eb;border-top-color:#4f46e5;border-radius:50%;animation:sgpro-spin 0.8s linear infinite;"></div>' +
                '<p class="sgpro-loading-message" style="font-weight:700;color:#1f2937;font-size:17px;margin:0 0 6px;">Guardando...</p>' +
                '<p class="sgpro-loading-detail" style="color:#6b7280;font-size:13px;margin:0 0 14px;">Por favor espere, no cierre esta ventana.</p>' +
                '<div class="sgpro-progress-wrap" style="display:none;background:#e5e7eb;border-radius:9999px;height:14px;overflow:hidden;">' +
                    '<div class="sgpro-progress-bar" style="width:0%;height:100%;background:linear-gradient(90deg,#4f46e5,#9333ea);border-radius:9999px;transition:width .2s ease;"></div>' +
                '</div>' +
                '<p class="sgpro-progress-text" style="display:none;color:#4f46e5;font-weight:700;font-size:13px;margin:8px 0 0;">0%</p>' +
            '</div>';

        var style = document.createElement('style');
        style.textContent = '@keyframes sgpro-spin{to{transform:rotate(360deg)}}';
        document.head.appendChild(style);
        document.body.appendChild(overlay);
        return overlay;
    }

    function showOverlay(message, withProgress) {
        var overlay = createOverlay();
        overlay.querySelector('.sgpro-loading-message').textContent = message;
        var wrap = overlay.querySelector('.sgpro-progress-wrap');
        var text = overlay.querySelector('.sgpro-progress-text');
        wrap.style.display = withProgress ? 'block' : 'none';
        text.style.display = withProgress ? 'block' : 'none';
        setProgress(0);
        overlay.style.display = 'flex';
    }

    function setProgress(percent) {
        var overlay = document.getElementById(OVERLAY_ID);
        if (!overlay) return;
        var bar = overlay.querySelector('.sgpro-progress-bar');
        var text = overlay.querySelector('.sgpro-progress-text');
        var value = Math.min(100, Math.max(0, Math.round(percent)));
        bar.style.width = value + '%';
        text.textContent = value < 100 ? value + '%' : '100% — Procesando...';
    }

    function hideOverlay() {
        var overlay = document.getElementById(OVERLAY_ID);
        if (overlay) overlay.style.display = 'none';
    }

    function getSubmitButtons(form) {
        return Array.prototype.slice.call(
            form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])')
        );
    }

    function disableButtons(form) {
        getSubmitButtons(form).forEach(function (btn) {
            if (btn.disabled) return;
            btn.dataset.sgproOriginalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'wait';
            if (btn.tagName === 'BUTTON') {
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span style="margin-left:8px;">Procesando...</span>';
            }
        });
    }

    function restoreButtons(form) {
        getSubmitButtons(form).forEach(function (btn) {
            if (btn.dataset.sgproOriginalHtml !== undefined) {
                btn.innerHTML = btn.dataset.sgproOriginalHtml;
                delete btn.dataset.sgproOriginalHtml;
            }
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor = '';
        });
    }

    function formHasFilesToUpload(form) {
        if ((form.enctype || '').toLowerCase() !== 'multipart/form-data') return false;
        var inputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].files && inputs[i].files.length > 0) return true;
        }
        return false;
    }

    function setExpectedUploadCount(form) {
        var count = 0;
        var inputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].files) count += inputs[i].files.length;
        }
        var field = form.querySelector('input[name="expected_upload_count"]');
        if (field) field.value = String(count);
    }

    function uploadWithProgress(form) {
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();

        showOverlay('Subiendo archivos...', true);
        disableButtons(form);
        form.dataset.sgproSubmitting = '1';

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                setProgress((e.loaded / e.total) * 100);
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                setProgress(100);
                var target = xhr.responseURL || form.action;
                if (target && target !== window.location.href) {
                    window.location.assign(target);
                } else {
                    window.location.reload();
                }
            } else {
                hideOverlay();
                restoreButtons(form);
                delete form.dataset.sgproSubmitting;
                var msg = 'Ocurrió un error al subir los archivos (código ' + xhr.status + ').';
                if (xhr.status === 413) {
                    msg = 'El tamaño total de los archivos supera el límite permitido por el servidor. Intente subir menos archivos a la vez.';
                } else if (xhr.status === 419) {
                    msg = 'La sesión expiró. Recargue la página e intente nuevamente.';
                }
                alert(msg);
            }
        });

        xhr.addEventListener('error', function () {
            hideOverlay();
            restoreButtons(form);
            delete form.dataset.sgproSubmitting;
            alert('Error de conexión al subir los archivos. Verifique su conexión e intente nuevamente.');
        });

        xhr.open((form.method || 'POST').toUpperCase(), form.action || window.location.href);
        xhr.send(formData);
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (e.defaultPrevented) return; // otro handler canceló el envío (ej. confirm)
        if (form.hasAttribute('data-no-loading')) return;
        if ((form.method || '').toLowerCase() !== 'post') return;
        if (form.target && form.target !== '_self') return;

        // Evitar envíos dobles
        if (form.dataset.sgproSubmitting === '1') {
            e.preventDefault();
            return;
        }

        setExpectedUploadCount(form);

        if (formHasFilesToUpload(form)) {
            // Subida con barra de progreso real
            e.preventDefault();
            uploadWithProgress(form);
            return;
        }

        // Envío normal: mostrar overlay de guardado y deshabilitar botones
        form.dataset.sgproSubmitting = '1';
        showOverlay('Guardando...', false);
        // Deshabilitar después de que el navegador capture los datos del formulario
        setTimeout(function () {
            disableButtons(form);
        }, 0);
    });

    // Restaurar el estado si el usuario vuelve con el botón "Atrás" (bfcache)
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            hideOverlay();
            document.querySelectorAll('form[data-sgpro-submitting]').forEach(function (form) {
                delete form.dataset.sgproSubmitting;
                restoreButtons(form);
            });
        }
    });
})();
