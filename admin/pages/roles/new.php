<?php
$script[] = "roles";

?>
<div class="container mt-5">
    <h2>Role</h2>
    <form id="roleForm">
        <div class="mb-3">
            <label for="roleName" class="form-label">Role Name</label>
            <input type="text" class="form-control" id="roleName" value="<?= $role['name'] ?? "" ?>"
                placeholder="e.g., Customer Support" required>
            <input type="hidden" class="form-control" id="roleID" name="ID" value="<?= $role['ID'] ?? "" ?>">
        </div>

        <!-- Dynamic Role Sections -->
        <div class="accordion" id="roleAccordion">
            <?php foreach ($defaultRoles as $category => $permissions): 
                        
                    ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $category ?>">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse<?= $category ?>" aria-expanded="true"
                        aria-controls="collapse<?= $category ?>">
                        <?= htmlspecialchars($category) ?>
                    </button>
                </h2>
                <div id="collapse<?= $category ?>" class="accordion-collapse collapse show"
                    aria-labelledby="heading<?= $category ?>" data-bs-parent="#roleAccordion">
                    <div class="accordion-body">
                        <div class="form-check">
                            <input class="form-check-input category-check" type="checkbox"
                                value="<?= htmlspecialchars($category) ?>" id="<?= $category ?>CategoryCheck"
                                data-category="<?= $category ?>">
                            <label class="form-check-label fw-bold" for="<?= $category ?>CategoryCheck">
                                <?= ucfirst(str_replace("_", " ", htmlspecialchars($category))) ?>
                            </label>
                        </div>
                        <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $permissionKey => $permissionLabel): 
                    $checked = (isset($rolePermissions[$category]) && in_array($permissionKey, $rolePermissions[$category])) ?  "checked" : "";
                    ?>
                        <div class="form-check ms-3">
                            <input class="form-check-input permission-check" type="checkbox"
                                value="<?= htmlspecialchars($permissionKey) ?>" id="<?= $category . $permissionKey ?>"
                                data-category="<?= $category ?>" <?= $checked ?>>
                            <label class="form-check-label" for="<?= $category . $permissionKey ?>">
                                <?= htmlspecialchars($permissionLabel) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </div>
        <div class="custommessage"></div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>