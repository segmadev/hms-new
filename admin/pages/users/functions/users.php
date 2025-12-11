<?php
require_once ROOT."/functions/helper.php";
require_once ROOT."/functions/formhandler.php";

class users extends database {
    protected $formHand;
    public function __construct() {
        parent::__construct();
        $this->formHand = new formHandler();
    }

    // Get all users with optional filtering and pagination
    public function getUsers($search = null, $status = null, $start = 0, $limit = 50) {
        // Ensure start and limit are non-negative integers
        $start = max(0, (int)$start);
        $limit = max(1, (int)$limit);
        
        $params = [""];
        $query = "ID != ?"; // Start with a valid WHERE clause

        if ($search) {
            $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if ($status !== null) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY date DESC LIMIT $start, $limit";
       
        return $this->getall(
            "users", 
            $query, 
            $params, 
            "ID, first_name, last_name, email, phone_number, status, date", 
            "all"
        );
    }

    // Get total number of users (for pagination)
    public function getTotalUsers($search = null, $status = null) {
        $params = [];
        $query = "ID != null";

        if ($search) {
            $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if ($status !== null) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        return $this->getall("users", $query, $params, "COUNT(*) as total", "value");
    }

    // Get single user details
    public function getUser($id) {
        return $this->getall("users", "ID = ?", [$id]);
    }

    // Create new user
    public function createUser($data) {
        try {
          
            // Generate unique ID
            $userID = utilities::genID("", 20);
            $form_params = $this->getUserForm();
            $info = $this->formHand->validateForm($form_params);
            if ($this->formHand->errors) {
                return utilities::apiMessage("failed", 400, null, $this->formHand->errors);
            }
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);


            // Insert user
            $result = $this->quick_insert("users", $userData);

            if ($result) {
                return utilities::apiMessage("User created successfully", 200, [
                    'id' => $userID,
                    'email' => $data['email']
                ]);
            }

            return utilities::apiMessage("Failed to create user", 500);

        } catch (Exception $e) {
            return utilities::apiMessage("Error: " . $e->getMessage(), 500);
        }
    }

    // Update user
    public function editUser($data) {
        try {
            // Check if user exists
            if (!$this->getUser($data['ID'])) {
                return utilities::apiMessage("User not found", 404);
            }

            // Initialize update data array
            $updateData = [];

            // Validate and sanitize basic fields
            if (!empty($data['first_name'])) {
                $updateData['first_name'] = htmlspecialchars($data['first_name']);
            }
            if (!empty($data['last_name'])) {
                $updateData['last_name'] = htmlspecialchars($data['last_name']);
            }
            if (!empty($data['phone_number'])) {
                $updateData['phone_number'] = $data['phone_number'];
            }

            // Handle email update with duplicate check
            if (!empty($data['email'])) {
                // Check if email exists for another user
                $existingUser = $this->getall("users", "email = ? AND ID != ?", [$data['email'], $data['ID']]);
                if ($existingUser) {
                    return utilities::apiMessage("Email already exists", 400);
                }
                $updateData['email'] = $data['email'];
            }

            // Handle password update
            if (!empty($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }else{
                unset($data['password']);
            }

            // Handle status update
            if (isset($data['status'])) {
                $updateData['status'] = (int)$data['status'];
            }

            // If no fields to update
            if (empty($updateData)) {
                return utilities::apiMessage("No changes to update", 400);
            }

            // Update user
            $result = $this->update("users", $updateData, "ID = '".$data['ID']."'");

            if ($result) {
                return utilities::apiMessage("User updated successfully", 200, [
                    'redirect' => 'users'
                ]);
            }

            return utilities::apiMessage("Failed to update user", 500);

        } catch (Exception $e) {
            return utilities::apiMessage("Error: " . $e->getMessage(), 500);
        }
    }

    // Delete user
    public function deleteUser($id) {
        try {
            if (!$this->getUser($id)) {
                return utilities::apiMessage("User not found", 404);
            }

            $result = $this->delete("users", "ID = ?", [$id]);

            if ($result) {
                return utilities::apiMessage("User deleted successfully", 200);
            }

            return utilities::apiMessage("Failed to delete user", 500);

        } catch (Exception $e) {
            return utilities::apiMessage("Error: " . $e->getMessage(), 500);
        }
    }

    // Toggle user status (block/unblock)
    public function toggleStatus($id, $status) {
        try {
            if (!$this->getUser($id)) {
                return utilities::apiMessage("User not found", 404);
            }

            $result = $this->update("users", ['status' => $status], "ID = ?", [$id]);

            if ($result) {
                $message = $status ? "User unblocked successfully" : "User blocked successfully";
                return utilities::apiMessage($message, 200);
            }

            return utilities::apiMessage("Failed to update user status", 500);

        } catch (Exception $e) {
            return utilities::apiMessage("Error: " . $e->getMessage(), 500);
        }
    }

    public function getUserForm($user = null) {
        return [
            "ID"=>["is_required"=>false, "input_type"=>"hidden"],
            "first_name" => [
                "title" => "First Name",
                "value" => $user ? $user['first_name'] : "",
                "is_required" => true,
                "type" => "input",
                "input_type" => "text",
                "class" => "form-control",
                "placeholder" => "Enter first name"
            ],
            "last_name" => [
                "title" => "Last Name",
                "value" => $user ? $user['last_name'] : "",
                "is_required" => true,
                "type" => "input",
                "input_type" => "text",
                "class" => "form-control",
                "placeholder" => "Enter last name"
            ],
            "email" => [
                "title" => "Email Address",
                "value" => $user ? $user['email'] : "",
                "is_required" => true,
                "type" => "input",
                "input_type" => "email",
                "class" => "form-control",
                "placeholder" => "Enter email address"
            ],
            "phone_number" => [
                "title" => "Phone Number",
                "value" => $user ? $user['phone_number'] : "",
                "is_required" => true,
                "type" => "input",
                "input_type" => "tel",
                "class" => "form-control",
                "placeholder" => "Enter phone number"
            ],
            "password" => [
                "title" => "Password",
                "value" => "",
                "is_required" => false,
                "type" => "input",
                "input_type" => "password",
                "class" => "form-control",
                "placeholder" => $user ? "Leave blank to keep current password" : "Enter password",
                "validate" => true
            ],
            "confirm_password" => [
                "title" => "Confirm Password",
                "value" => "",
                "is_required" => false,
                "type" => "input",
                "input_type" => "password",
                "class" => "form-control",
                "placeholder" => "Confirm password"
            ],
            "status" => [
                "title" => "Status",
                "value" => $user ? $user['status'] : "1",
                "is_required" => true,
                "type" => "select",
                "class" => "form-select",
                "options" => [
                    "1" => "Active",
                    "0" => "Blocked"
                ]
            ]
        ];
    }
}
