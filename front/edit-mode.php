<?php
/**
 * edit-mode.php
 * ---------------------------------------------------------------
 * Drop-in "Edit Mode" for property-listing.php.
 *
 * HOW TO WIRE IT UP
 * 1. Save this file as: front/includes/edit-mode.php
 * 2. In property-listing.php, right after:
 *        <?php include __DIR__ . '/includes/header.php'; ?>
 *    add:
 *        <?php include __DIR__ . '/includes/edit-mode.php'; ?>
 *    (Only inside the `<?php else: ?>` branch where $project exists.)
 * 3. On each card-panel you want editable, add:
 *      - data-section="project_details" (or amenities / company_details / maps)
 *      - the .section-edit-controls block (copy from README below)
 *      - data-editable="true" data-field="COLUMN_NAME" on the actual
 *        text you want to be editable (or use <input>/<textarea>
 *        with data-field="COLUMN_NAME" for structured fields)
 *
 *    See the chat answer for the exact snippets for:
 *      Project Details, Amenities, Company Details, Maps, Floor Plans.
 *
 * 4. Adjust $isAdminEditor below to match your real admin session check.
 * ---------------------------------------------------------------
 */

// ---- Change this to match how your admin panel marks a logged-in admin ----
$isAdminEditor = !empty($_SESSION['admin_id']);

if ($isAdminEditor && !empty($project)):
?>
<button type="button" id="editModeToggle" class="edit-mode-fab">
    <i class="bi bi-pencil-square"></i> <span>Edit Mode</span>
</button>

<style>
/* ===================== EDIT MODE ===================== */
.edit-mode-fab{
    position:fixed; bottom:24px; right:24px; z-index:1050;
    background:#12213a; color:#fff; border:none; border-radius:2rem;
    padding:0.7rem 1.3rem; font-weight:700; font-size:0.9rem;
    box-shadow:0 6px 20px rgba(0,0,0,0.25); cursor:pointer;
    display:flex; align-items:center; gap:0.5rem; transition:all .25s ease;
}
.edit-mode-fab.active{ background:#c9a84c; }
.edit-mode-fab:hover{ transform:translateY(-2px); }

body.edit-mode-active .card-panel[data-section]{
    outline:2px dashed rgba(201,168,76,0.5); outline-offset:4px; position:relative;
}
body.edit-mode-active .card-panel[data-section] .section-edit-controls{ display:flex; }

.section-edit-controls{
    display:none; position:absolute; top:0.75rem; right:0.75rem; gap:0.5rem; z-index:5;
}
.section-edit-btn,.section-save-btn,.section-cancel-btn,.variant-save-btn{
    border:none; border-radius:0.4rem; padding:0.3rem 0.8rem;
    font-size:0.75rem; font-weight:700; cursor:pointer;
}
.section-edit-btn{ background:#eef1f5; color:#12213a; }
.section-save-btn,.variant-save-btn{ background:#22c55e; color:#fff; }
.section-cancel-btn{ background:#e74c3c; color:#fff; }

.card-panel.section-editing .section-edit-btn{ display:none; }
.section-save-btn,.section-cancel-btn{ display:none; }
.card-panel.section-editing .section-save-btn,
.card-panel.section-editing .section-cancel-btn{ display:inline-flex; }

/* text that becomes contenteditable */
[data-editable="true"]{ border-radius:0.3rem; }
.card-panel.section-editing [data-editable="true"]{
    background:#fffbe8; outline:1px dashed #c9a84c; padding:0.15rem 0.3rem; min-height:1.2em; cursor:text;
}

/* input/textarea fields used for structured data */
.editable-input{
    border:1px solid #c9a84c; border-radius:0.4rem; padding:0.3rem 0.5rem;
    font:inherit; background:#fffbe8; width:100%;
}

/* toggle helpers: view-only vs edit-only content inside a section */
.edit-only{ display:none; }
.card-panel.section-editing .edit-only{ display:block; }
.card-panel.section-editing .variant-edit-fields.edit-only{ display:flex; flex-direction:column; gap:0.3rem; }
.card-panel.section-editing .view-only{ display:none; }

.edit-save-toast{
    position:fixed; bottom:90px; right:24px; background:#12213a; color:#fff;
    padding:0.6rem 1rem; border-radius:0.5rem; font-size:0.85rem; z-index:2000;
    opacity:0; transform:translateY(10px); transition:all .3s;
}
.edit-save-toast.show{ opacity:1; transform:translateY(0); }
.edit-save-toast.error{ background:#b6435a; }
</style>

<script>
(function(){
    var PROJECT_ID = <?php echo (int) $project['id']; ?>;
    var SAVE_SECTION_URL   = 'save-section.php';
    var SAVE_VARIANT_URL   = 'save-floorplan-variant.php';

    var toggleBtn = document.getElementById('editModeToggle');
    if(!toggleBtn) return;

    function showToast(msg, isError){
        var t = document.createElement('div');
        t.className = 'edit-save-toast' + (isError ? ' error' : '');
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function(){ t.classList.add('show'); }, 10);
        setTimeout(function(){
            t.classList.remove('show');
            setTimeout(function(){ t.remove(); }, 300);
        }, 2500);
    }

    toggleBtn.addEventListener('click', function(){
        var active = document.body.classList.toggle('edit-mode-active');
        toggleBtn.classList.toggle('active', active);
        toggleBtn.querySelector('span').textContent = active ? 'Exit Edit Mode' : 'Edit Mode';
        if(!active){
            document.querySelectorAll('.card-panel.section-editing').forEach(function(p){
                p.classList.remove('section-editing');
            });
        }
    });

    document.addEventListener('click', function(e){
        var editBtn   = e.target.closest('.section-edit-btn');
        var saveBtn   = e.target.closest('.section-save-btn');
        var cancelBtn = e.target.closest('.section-cancel-btn');
        var variantSaveBtn = e.target.closest('.variant-save-btn');

        if (editBtn) {
            var panel = editBtn.closest('.card-panel');
            panel.classList.add('section-editing');
            panel.querySelectorAll('[data-editable="true"]').forEach(function(el){
                el.setAttribute('contenteditable', 'true');
            });
        }

        if (cancelBtn) {
            location.reload();
        }

        if (saveBtn) {
            var panel2 = saveBtn.closest('.card-panel');
            saveSection(panel2, saveBtn);
        }

        if (variantSaveBtn) {
            saveVariant(variantSaveBtn);
        }
    });

    function collectFields(container){
        var fields = {};
        container.querySelectorAll('[data-field]').forEach(function(el){
            // skip fields that belong to a nested variant card when collecting at section level
            if (el.closest('.variant-edit-fields') && container.classList.contains('card-panel') && container.id === 'floor-plans') return;
            var key = el.getAttribute('data-field');
            var val;
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') {
                val = el.value;
            } else {
                val = el.innerText.trim();
            }
            fields[key] = val;
        });
        return fields;
    }

    function saveSection(panel, saveBtn){
        var section = panel.getAttribute('data-section');
        if(!section) return;

        var fields = collectFields(panel);
        var originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = 'Saving...';

        fetch(SAVE_SECTION_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'project_id=' + PROJECT_ID + '&section=' + encodeURIComponent(section) +
                  '&fields=' + encodeURIComponent(JSON.stringify(fields))
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (data.status === 'success') {
                showToast('Saved ✔');
                setTimeout(function(){ location.reload(); }, 700);
            } else {
                showToast(data.message || 'Save failed', true);
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        })
        .catch(function(){
            showToast('Network error while saving.', true);
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    }

    function saveVariant(btn){
        var card = btn.closest('[data-variant-id]');
        if (!card) return;
        var variantId = card.getAttribute('data-variant-id');
        if (!variantId) {
            showToast('This is sample data (not saved in the database) — nothing to update.', true);
            return;
        }

        var fields = {};
        card.querySelectorAll('[data-field]').forEach(function(el){
            var key = el.getAttribute('data-field');
            fields[key] = (el.tagName === 'INPUT') ? el.value : el.innerText.trim();
        });

        btn.disabled = true;
        var originalText = btn.innerHTML;
        btn.innerHTML = 'Saving...';

        fetch(SAVE_VARIANT_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'variant_id=' + encodeURIComponent(variantId) +
                  '&fields=' + encodeURIComponent(JSON.stringify(fields))
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (data.status === 'success') {
                showToast('Floor plan row updated ✔');
                setTimeout(function(){ location.reload(); }, 700);
            } else {
                showToast(data.message || 'Save failed', true);
            }
        })
        .catch(function(){ showToast('Network error while saving.', true); })
        .finally(function(){
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
})();
</script>
<?php endif; ?>
