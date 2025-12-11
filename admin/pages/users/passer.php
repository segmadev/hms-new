<?php
// admin/pages/users/passer.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(utilities::apiMessage("Invalid request method", 405));
}

// Initialize users class
$u = new users();

// Handle edit user
if (isset($_POST['edit_user']) && isset($_POST['ID'])) {
    $formHandler = new formHandler();
    
    // Get form parameters for validation
    $form_params = $u->getUserForm();
    
    // Validate form data
    $validatedData = $formHandler->validateForm($form_params);
    
    if ($formHandler->errors) {
        die(utilities::apiMessage("Validation failed", 400, null, $formHandler->errors));
    }
    
    // Add ID to validated data
    $validatedData['ID'] = $_POST['ID'];
    
    // Call editUser method
    $result = $u->editUser($validatedData);
    
    // Return the result directly as it's already formatted by utilities::apiMessage
    die($result);
}

// Handle fetcher requests
if (isset($_POST['what']) && $_POST['what'] === 'users') {
    
    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20;
    
    // Get search parameters from URL
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    try {
        // Get users
        $users = $u->getUsers($search, $status, $start, $limit);
        
        if (empty($users) && $start === 0) {
            // If no users found on first load
            echo json_encode([
                'status' => 'ok',
                'data' => '<tr><td colspan="6" class="text-center">No users found</td></tr>'
            ]);
            exit;
        } else if (empty($users)) {
            // If no more users to load
            echo '';
            exit;
        }

        $html = '';
        foreach ($users as $user) {
            $html .= '<tr>
                <td>' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>' . htmlspecialchars($user['phone_number']) . '</td>
                <td>
                    <span class="badge bg-' . ($user['status'] ? 'success' : 'danger') . ' user-status-badge">
                        ' . ($user['status'] ? 'Active' : 'Blocked') . '
                    </span>
                </td>
                <td>' . date('Y-m-d', strtotime($user['date'])) . '</td>
                <td class="user-actions">
                    <div class="btn-group">
                        <a href="users/edit/' . $user['ID'] . '" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i>Edit
                        </a>
                        
                    </div>
                </td>
            </tr>';
        }

        // Return JSON response
        echo json_encode([
            'status' => 'ok',
            'data' => $html
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'data' => '<tr><td colspan="6" class="text-center text-danger">Error loading users</td></tr>'
        ]);
        exit;
    }
}

// Handle create user
if (isset($_POST['create_user'])) {
    $formHandler = new formHandler();
    
    // Get form parameters for validation
    $form_params = $u->getUserForm();
    
    // Validate form data
    $validatedData = $formHandler->validateForm($form_params);
    
    if ($formHandler->errors) {
        die(utilities::apiMessage("Validation failed", 400, null, $formHandler->errors));
    }
    
    // Validate password match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        die(utilities::apiMessage("Passwords do not match", 400));
    }
    
    try {
        // Create user
        $result = $u->createUser($validatedData);
        
        // If successful, add redirect to users list
        if (is_string($result)) {
            $response = json_decode($result, true);
            if ($response['code'] === 200) {
                $response['data']['redirect'] = 'users';
                $result = json_encode($response);
            }
        }
        
        die($result);
    } catch (Exception $e) {
        die(utilities::apiMessage("An error occurred", 500));
    }
}

// Rest of the passer code for handling actions...
