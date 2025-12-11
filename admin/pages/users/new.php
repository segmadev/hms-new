<?php
// Get instance of users class
$u = new users();

// Get the form structure
$form = $u->getUserForm();

// Create form using formhandler
$formHandler = new formHandler();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New User</h3>
                </div>
                <div class="card-body">
                    <form id="foo" method="post">
                        <input type="hidden" name="create_user" value="1">
                        <div class="row">
                         <?php echo $c->create_form($form); ?>
                         <input type="hidden" name="page" value="user">
                         <input type="hidden" name="">
                        </div>
                        <div class="custommessage"></div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create User
                                </button>
                                <a href="users" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
