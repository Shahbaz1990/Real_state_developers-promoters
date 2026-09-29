<?php
// Component expects the caller to provide configuration variables.
$currentPage = isset($currentPage) ? max(1, intval($currentPage)) : (isset($page) ? max(1, intval($page)) : 1);
$totalPages = isset($totalPages) ? max(1, intval($totalPages)) : 1;
?>
<?php if(!defined('TABLE_COMPONENT_CSS_INCLUDED')): define('TABLE_COMPONENT_CSS_INCLUDED', true); ?>
<link rel="stylesheet" href="components/table/table.css">
<?php endif; ?>
<div class="container-fluid py-4 bg-light min-vh-100"> 
    <!-- Top Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2"> 
        <h3 class="fw-semibold mb-0 text-dark" style="font-size: 22px;"><?= htmlspecialchars($pageTitle) ?></h3> 
        <?php if(!empty($showAddButton) && !empty($addModalId)): ?> 
            <button class="btn btn-primary-custom rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#<?= htmlspecialchars($addModalId) ?>"> 
                <span><?= htmlspecialchars($addButtonText) ?></span> 
            </button> 
        <?php endif; ?> 
    </div> 

    <!-- Main Content Table Card -->
    <div class="card card-custom border-0 shadow-sm rounded-3 mx-2"> 
        <div class="card-header card-header-custom bg-white border-0"> 
            <div class="row align-items-center g-3"> 
                <div class="col-md-6"> 
                    <h5 class="fw-semibold mb-0 text-dark" style="font-size: 16px;"><?= htmlspecialchars($tableTitle) ?></h5> 
                </div> 
                <?php if(!empty($showSearch)): ?> 
                    <div class="col-md-6 text-md-end"> 
                        <input type="search" class="form-control d-inline-block search-input-custom" placeholder="<?= htmlspecialchars($searchPlaceholder ?? '') ?>"> 
                    </div> 
                <?php endif; ?> 
            </div> 
        </div> 

        <!-- Responsive Table Container -->
        <div class="table-responsive"> 
            <table class="table table-custom align-middle"> 
                <thead> 
                    <tr> 
                        <?php foreach($headers as $header): ?> 
                            <th><?= htmlspecialchars($header) ?></th> 
                        <?php endforeach; ?> 
                    </tr> 
                </thead> 
                <tbody> 
                    <?php if(!empty($rows)): ?> 
                        <?php foreach($rows as $row): ?> 
                            <tr> 
                                <?php foreach($row as $cell): ?> 
                                    <td><?= $cell ?></td> 
                                <?php endforeach; ?> 
                            </tr> 
                        <?php endforeach; ?> 
                    <?php else: ?> 
                        <tr> 
                            <td colspan="<?= count($headers) ?>" class="text-center py-5 text-muted">No records found</td> 
                        </tr> 
                    <?php endif; ?> 
                </tbody> 
            </table> 
        </div> 

        <!-- Optional Footer Panel -->
        <?php if(!empty($showFooter)): ?>
<div class="card-footer bg-white d-flex justify-content-between align-items-center px-4 py-3 ">
    
    <nav>
        <ul class="pagination custom-pagination mb-0">
            <!-- Previous -->
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?page=<?= $currentPage-1 ?>">
                    &lsaquo;
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <li class="page-item <?= ($currentPage==$i)?'active':'' ?>">
                    <a class="page-link"
                       href="?page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?page=<?= $currentPage+1 ?>">
                    &rsaquo;
                </a>
            </li>
        </ul>
    </nav>

</div>
<?php endif; ?>
    </div> 
</div>

<?php if(!empty($addModalId) && !empty($formFields) && is_array($formFields)): ?>
<!-- Reusable Add New Record Popup Form Modal -->
<div class="modal fade" id="<?= htmlspecialchars($addModalId) ?>" tabindex="-1" aria-labelledby="<?= htmlspecialchars($addModalId) ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom border-0 shadow rounded-3">
            
            <form method="POST" action="" enctype="multipart/form-data">
                <?php echo function_exists('csrf_field') ? csrf_field() : ''; ?>
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-semibold text-dark" id="<?= htmlspecialchars($addModalId) ?>Label" style="font-size: 16px;">
                        <?= htmlspecialchars($addButtonText) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <?php
                    // Allow page-specific HTML override for the add modal body.
                    if(!empty($page_add_modal_html)){
                        echo $page_add_modal_html;
                    } else {
                        foreach($formFields as $field): ?>
                            <div class="mb-3">
                                <label class="form-label-custom fw-medium text-secondary"><?= htmlspecialchars($field['label'] ?? '') ?></label>
                                <?php if(($field['type'] ?? 'text') === 'select' && !empty($field['options']) && is_array($field['options'])): ?>
                                    <select name="<?= htmlspecialchars($field['name']) ?>" class="form-select form-input-custom" <?= !empty($field['required']) ? 'required' : '' ?>>
                                        <option value="" selected disabled><?= htmlspecialchars($field['placeholder'] ?? 'Select an option') ?></option>
                                        <?php foreach($field['options'] as $optionValue => $optionLabel): ?>
                                            <option value="<?= htmlspecialchars($optionValue) ?>"><?= htmlspecialchars($optionLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif(($field['type'] ?? 'text') === 'textarea'): ?>
                                    <textarea name="<?= htmlspecialchars($field['name']) ?>" class="form-control form-input-custom" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>" <?= !empty($field['required']) ? 'required' : '' ?>><?= htmlspecialchars($field['value'] ?? '') ?></textarea>
                                <?php else: ?>
                                    <input
                                        type="<?= htmlspecialchars($field['type'] ?? 'text') ?>"
                                        name="<?= htmlspecialchars($field['name']) ?>"
                                        class="form-control form-input-custom"
                                        placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                        value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                        <?= !empty($field['required']) ? 'required' : '' ?>
                                        <?= !empty($field['accept']) ? 'accept="' . htmlspecialchars($field['accept']) . '"' : '' ?>
                                        <?= !empty($field['multiple']) ? 'multiple' : '' ?>
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach;
                    }
                    ?>
                </div>

                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 justify-content-end">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px; font-weight: 500;">Cancel</button>
                    <button type="submit" name="<?= htmlspecialchars($saveButtonName) ?>" class="btn btn-primary-custom rounded-pill px-4">
                        <?= htmlspecialchars($saveButtonText) ?>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!empty($editModalId) && !empty($editFormFields) && is_array($editFormFields)): ?>
<!-- Reusable Edit Record Popup Form Modal -->
<div class="modal fade" id="<?= htmlspecialchars($editModalId) ?>" tabindex="-1" aria-labelledby="<?= htmlspecialchars($editModalId) ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom border-0 shadow rounded-3">
            
            <form method="POST" action="" enctype="multipart/form-data">
                <?php echo function_exists('csrf_field') ? csrf_field() : ''; ?>
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-semibold text-dark" id="<?= htmlspecialchars($editModalId) ?>Label" style="font-size: 16px;">
                        <?= htmlspecialchars($editButtonText) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <?php foreach($editFormFields as $field): ?>
                        <div class="mb-3">
                            <label class="form-label-custom fw-medium text-secondary"><?= htmlspecialchars($field['label'] ?? '') ?></label>
                            <?php if(($field['type'] ?? 'text') === 'select' && !empty($field['options']) && is_array($field['options'])): ?>
                                <select id="<?= htmlspecialchars($editModalId . '_' . $field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>" class="form-select form-input-custom" <?= !empty($field['required']) ? 'required' : '' ?>>
                                    <option value="" selected disabled><?= htmlspecialchars($field['placeholder'] ?? 'Select an option') ?></option>
                                    <?php foreach($field['options'] as $optionValue => $optionLabel): ?>
                                        <option value="<?= htmlspecialchars($optionValue) ?>"><?= htmlspecialchars($optionLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif(($field['type'] ?? 'text') === 'textarea'): ?>
                                <textarea id="<?= htmlspecialchars($editModalId . '_' . $field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>" class="form-control form-input-custom" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>" <?= !empty($field['required']) ? 'required' : '' ?>><?= htmlspecialchars($field['value'] ?? '') ?></textarea>
                            <?php else: ?>
                                <input
                                    id="<?= htmlspecialchars($editModalId . '_' . $field['name']) ?>"
                                    type="<?= htmlspecialchars($field['type'] ?? 'text') ?>"
                                    name="<?= htmlspecialchars($field['name']) ?>"
                                    class="form-control form-input-custom"
                                    placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                    value="<?= htmlspecialchars($field['value'] ?? '') ?>"
                                    data-edit-name="<?= htmlspecialchars($field['name']) ?>"
                                    <?= !empty($field['required']) ? 'required' : '' ?>
                                    <?= !empty($field['accept']) ? 'accept="' . htmlspecialchars($field['accept']) . '"' : '' ?>
                                    <?= !empty($field['multiple']) ? 'multiple' : '' ?>
                                >
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 justify-content-end">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px; font-weight: 500;">Cancel</button>
                    <button type="submit" name="<?= htmlspecialchars($updateButtonName) ?>" class="btn btn-primary-custom rounded-pill px-4">
                        <?= htmlspecialchars($updateButtonText) ?>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
(function(){
    document.addEventListener('click', function(event) {
        var btn = event.target.closest('.edit-btn');
        if (!btn) return;
        if (btn.getAttribute('data-bs-target') !== '#<?= htmlspecialchars($editModalId) ?>') return;
        var modal = document.getElementById('<?= htmlspecialchars($editModalId) ?>');
        if (!modal) return;
        modal.querySelectorAll('[data-edit-name]').forEach(function(field) {
            var fieldName = field.getAttribute('data-edit-name');
            var value = btn.getAttribute('data-' + fieldName);

            if ((fieldName === 'menu_id') && (value === null || value === undefined || value === '')) {
                value = btn.getAttribute('data-id');
            }

            if (value !== null && value !== undefined) {
                field.value = value;
            }
        });
    });
})();
</script>
<?php endif; ?>
