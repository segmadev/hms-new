<?php
$script[] = "fetcher";
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Users Management</h4>
        <div>
            <a href="users/new" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filter -->
        <div class="row mb-3">
            <div class="col-md-8">
                <form class="fetcher-form" data-target="usersTableBody">
                    <div class="d-flex gap-2">
                        <input type="text" 
                               class="form-control" 
                               placeholder="Search users..." 
                               name="search" 
                               value="">
                        <select class="form-select" name="status" style="width: 150px;">
                            <option value="">All Status</option>
                            <option value="1" >Active</option>
                            <option value="0">Blocked</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" 
                       data-load="users" 
                       data-path="passer/users" 
                       data-limit="20" 
                       data-interval="1000"
                       data-isreplace="false">
                </tbody>
            </table>
        </div>
    </div>
</div>