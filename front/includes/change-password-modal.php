<?php
/**
 * Reusable Change Password modal.
 * Site-wide component included from the header so it is available on every page.
 * Opens in-page (no navigation) and posts to the existing /change-password.php endpoint.
 */
?>
<style>
.cp-modal-overlay{
    position:fixed;inset:0;z-index:1080;
    background:rgba(10,16,28,.58);
    display:flex;align-items:center;justify-content:center;
    padding:1rem;
    opacity:0;visibility:hidden;
    transition:opacity .18s ease, visibility .18s ease;
}
.cp-modal-overlay.open{opacity:1;visibility:visible;}
.cp-modal{
    background:#fff;
    border:1px solid #e7e9ee;
    border-radius:1rem;
    box-shadow:0 18px 50px rgba(10,16,28,.28);
    width:100%;
    max-width:430px;
    max-height:calc(100vh - 2rem);
    overflow-y:auto;
    transform:translateY(10px) scale(.98);
    transition:transform .18s ease;
}
.cp-modal-overlay.open .cp-modal{transform:none;}
.cp-modal-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:1.15rem 1.35rem .9rem;
    border-bottom:1px solid #eef0f5;
}
.cp-modal-head h5{margin:0;font-weight:700;color:#12213a;}
.cp-modal-close{
    background:none;border:none;font-size:1.15rem;color:#6b7686;cursor:pointer;
    padding:.25rem;line-height:1;border-radius:.375rem;transition:color .15s ease;
}
.cp-modal-close:hover{color:#12213a;}
.cp-modal-body{padding:1.35rem;}
.cp-password-input-group{position:relative;}
.cp-password-input-group .form-control{padding-right:2.75rem;}
.cp-password-input-group .cp-eye{
    position:absolute;top:50%;right:.45rem;transform:translateY(-50%);
    background:none;border:none;color:#6b7686;font-size:1.05rem;cursor:pointer;
    padding:.35rem;line-height:1;border-radius:.375rem;
}
.cp-password-input-group .cp-eye:hover{color:#12213a;}
.cp-modal-actions{display:flex;gap:.7rem;}
@media (max-width: 575.98px){
    .cp-modal-overlay{padding:.75rem;}
    .cp-modal-actions{flex-direction:column;}
    .cp-modal-actions .btn{width:100%;}
}
</style>

<div class="cp-modal-overlay" id="cpModalOverlay" role="dialog" aria-modal="true" aria-labelledby="cpModalTitle" aria-hidden="true">
    <div class="cp-modal">
        <div class="cp-modal-head">
            <h5 id="cpModalTitle"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
            <button type="button" class="cp-modal-close" data-cp-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-modal-body">
            <div class="cp-alert-wrap"></div>
            <form id="cpForm" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="cpCurrent">Current Password</label>
                    <div class="cp-password-input-group">
                        <input type="password" class="form-control" id="cpCurrent" name="current_password" autocomplete="current-password" required>
                        <button type="button" class="cp-eye" data-cp-eye="cpCurrent" aria-label="Show / hide password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="cpNew">New Password</label>
                    <div class="cp-password-input-group">
                        <input type="password" class="form-control" id="cpNew" name="new_password" autocomplete="new-password" minlength="6" required>
                        <button type="button" class="cp-eye" data-cp-eye="cpNew" aria-label="Show / hide password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="cpConfirm">Confirm New Password</label>
                    <div class="cp-password-input-group">
                        <input type="password" class="form-control" id="cpConfirm" name="confirm_password" autocomplete="new-password" minlength="6" required>
                        <button type="button" class="cp-eye" data-cp-eye="cpConfirm" aria-label="Show / hide password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <div class="cp-modal-actions mt-4">
                    <button type="submit" class="btn btn-gold flex-fill"><i class="bi bi-shield-check"></i> Save Password</button>
                    <button type="button" class="btn btn-outline-secondary" data-cp-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('cpModalOverlay');
    if (!overlay) return;
    var form = document.getElementById('cpForm');
    var alertWrap = overlay.querySelector('.cp-alert-wrap');
    var lastTrigger = null;
    var wasLocked = false;

    function showAlert(type, msg) {
        alertWrap.innerHTML = '<div class="alert alert-' + type + ' d-flex align-items-center gap-2 mb-3">' +
            '<i class="bi ' + (type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle') + '"></i>' +
            '<div class="flex-grow-1">' + msg + '</div></div>';
    }
    function clearAlert() { alertWrap.innerHTML = ''; }
    function resetForm() {
        if (!form) return;
        form.reset();
        clearAlert();
        var submit = form.querySelector('button[type="submit"]');
        if (submit) { submit.disabled = false; }
    }

    function open() {
        lastTrigger = document.activeElement;
        resetForm();
        wasLocked = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        setTimeout(function () { var i = document.getElementById('cpCurrent'); if (i) i.focus(); }, 60);
    }
    function close() {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = wasLocked || '';
        if (lastTrigger && lastTrigger.focus && typeof lastTrigger.focus === 'function') {
            try { lastTrigger.focus(); } catch (e) {}
        }
    }

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-change-password-open]');
        if (opener) { e.preventDefault(); open(); return; }
        if (e.target.closest('[data-cp-close]')) { close(); return; }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('open')) close();
    });

    document.addEventListener('click', function (e) {
        var eye = e.target.closest('[data-cp-eye]');
        if (!eye) return;
        var input = document.getElementById(eye.getAttribute('data-cp-eye'));
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        eye.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearAlert();
        var current = document.getElementById('cpCurrent').value.trim();
        var fresh = document.getElementById('cpNew').value;
        var confirm = document.getElementById('cpConfirm').value;

        if (current === '') { showAlert('danger', 'Please enter your current password.'); return; }
        if (fresh.length < 6) { showAlert('danger', 'New password must be at least 6 characters long.'); return; }
        if (fresh !== confirm) { showAlert('danger', 'New password and confirm password do not match.'); return; }

        var submit = form.querySelector('button[type="submit"]');
        submit.disabled = true;

        var data = new FormData(form);
        data.append('ajax', '1');

        var endpoint = window.location.pathname.indexOf('/front/') !== -1 ? '/front/change-password.php' : 'change-password.php';

        fetch(endpoint, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(function (r) {
            return r.json().catch(function() {
                throw new Error('Invalid JSON response');
            });
        })
        .then(function (res) {
            if (res && res.success) {
                showAlert('success', res.message || 'Password changed successfully.');
                form.reset();
                setTimeout(function () { close(); }, 1600);
            } else {
                showAlert('danger', (res && res.message) || 'Could not change your password. Please try again.');
                submit.disabled = false;
            }
        })
        .catch(function (err) {
            showAlert('danger', 'Something went wrong. Please check your current password and try again.');
            submit.disabled = false;
        });
    });
})();
</script>