<?php
// admin/pages/users/templates/users_list.php

function renderUsersList($users) {
    if (empty($users)) {
        return '<tr><td colspan="6" class="text-center">No users found</td></tr>';
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
                    <a href="?page=users&action=edit&ID=' . $user['ID'] . '" 
                       class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-sm btn-' . ($user['status'] ? 'warning' : 'success') . '"
                            onclick="toggleUserStatus(\'' . $user['ID'] . '\', ' . $user['status'] . ')">
                        <i class="fas fa-' . ($user['status'] ? 'ban' : 'check') . '"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-danger"
                            onclick="deleteUser(\'' . $user['ID'] . '\')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>';
    }
    
    return $html;
}
?>
