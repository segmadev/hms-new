<?php
$script[] = "sweetalert";
// Get user ID from GET parameter
$userID = isset($_GET['ID']) ? $_GET['ID'] : 0;
// Get user data
$user = $u->getUser($userID);

// If user not found, redirect to users list
if (!is_array($user)) {
    utilities::loadpage("users");
    exit;
}

// Get form parameters
$form_params = $u->getUserForm($user);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit User</h4>
        <div>
            <a href="users" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="foo" method="post">
            <input type="hidden" name="ID" value="<?php echo $user['ID']; ?>">
            <input type="hidden" name="page" value="users">
            <div class="row">
                <?php echo $c->create_form($form_params); ?>
                <input type="hidden" name="edit_user" value="yes">
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='users'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
