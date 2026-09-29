<?php
/**
 * Shared closing footer for every admin page.
 * Replaces the old manual `<script src="js/adminlte.js">` + Bootstrap bundle pair.
 *
 * Provides:
 *   - Toastify toast notifications (CSS + JS from CDN)
 *   - One-shot flash toasts queued via set_flash('success'|'error'|'info', ...)
 *   - A slim top progress bar on page load
 *   - Automatic "Saving…" loading state on any form submit button
 */
$ui_flashes = $_SESSION['flash_msgs'] ?? [];
unset($_SESSION['flash_msgs']);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0"></script>
<script src="js/adminlte.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    /* Slim top progress bar shown while a page finishes rendering */
    .ui-page-loader {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0;
        z-index: 3000;
        background: linear-gradient(90deg, #ff2d7a, #7c3aed, #3fa1e4);
        box-shadow: 0 0 8px rgba(124, 58, 237, .55);
        opacity: 1;
        transition: width .7s cubic-bezier(.2, .8, .3, 1), opacity .45s ease;
    }
    .ui-page-loader.done { opacity: 0; }
</style>
<div class="ui-page-loader" id="uiPageLoader"></div>
<script>
(function () {
    "use strict";

    /* ---------------- 1. Flash toasts (Toastify) ---------------- */
    var flashes = <?= json_encode(array_map(function ($f) {
        return [
            'type'    => $f['type'] ?? 'success',
            'message' => (string) ($f['message'] ?? ''),
        ];
    }, $ui_flashes)) ?>;

    var UI_TOAST_BG = {
        success: 'linear-gradient(135deg, #059669, #10b981)',
        error:   'linear-gradient(135deg, #dc2626, #ef4444)',
        info:    'linear-gradient(135deg, #2563eb, #3b82f6)'
    };
    var UI_TOAST_ICON = { success: '✅', error: '⛔', info: 'ℹ️' };

    if (flashes.length && window.Toastify) {
        window.setTimeout(function () {
            flashes.forEach(function (f) {
                var type = UI_TOAST_BG[f.type] ? f.type : 'info';
                Toastify({
                    text: (UI_TOAST_ICON[type] ? UI_TOAST_ICON[type] + '  ' : '') + f.message,
                    duration: 4000,
                    gravity: 'top',
                    position: 'right',
                    close: true,
                    stopOnFocus: true,
                    className: 'ui-toast',
                    style: { background: UI_TOAST_BG[type] }
                }).showToast();
            });
        }, 350);
    }

    /* ---------------- 2. Slim top progress bar ---------------- */
    var loader = document.getElementById('uiPageLoader');
    if (loader) {
        window.setTimeout(function () { loader.style.width = '100%'; }, 150);
        window.setTimeout(function () {
            loader.classList.add('done');
            window.setTimeout(function () { if (loader.parentNode) loader.parentNode.removeChild(loader); }, 500);
        }, 1100);
    }

    /* ---------------- 3. Auto "Saving…" state on form submit ---------------- */
    function setButtonLoading(btn) {
        if (!btn || btn.dataset.uiLoading === '1') return;
        btn.dataset.uiLoading = '1';
        btn.disabled = true;
        var label = btn.getAttribute('data-loading-text') || 'Saving…';
        btn.dataset.uiLabel = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + label;
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form && form.id === 'profile-form') return; // AJAX form manages its own state
        var btn = form && form.querySelector('[type="submit"]');
        setButtonLoading(btn);
    }, true);
})();
</script>