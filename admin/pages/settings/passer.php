<?php
$test_from = [
    "upload_image" => ["input_type" => "file", "path" => "upload/", "file_name" => uniqid(), "formart" => ["pdf", "doc", "png"]]
];
if (isset($_POST['upload_image'])) {
    $d->validate_form($test_from);
}

if (isset($_POST['updatesettings'])) {
    $return = $s->message("You can not perfrom this action", "error", "json");
    if ($r->validate_action(["settings" => $_POST['settings']])) {
        $return = match (htmlspecialchars($_POST['settings'])) {
            "settings" => $s->update_settings($settings_form),
            "Withdraw" => $s->update_settings($settings_withdraw_form),
            "payment" => $s->update_settings($settings_deposit_form),
            "term_and_policy_condition" => $s->update_settings($term_and_policy_condition),
            "logo" => $s->update_settings($logo_from),
            "social_media" => $s->update_settings($settings_social_media),
            "seo" => $s->update_settings($settings_seo),
            "about" => $s->update_settings($about),
            "backup" => $s->update_settings($settings_backup),
            "rentals" => $s->update_settings($rentals_settings),
            "edit_admin" => $s->edit_admin($adminID, $admin_account),
        };
    }

    echo $return;
}



// Define backup directory
$backupDir = 'gdrive';

// Ensure directory exists
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

if(isset($_GET['delete_backups'])) {
    $success = false;
    $fullPath = $backupDir . '/' . trim($_GET['delete_backups']);
    if(file_exists($fullPath)) {
        if(!unlink($fullPath)) {
            $success = false;
            $message .= "Failed to delete $file\n";
        }else {
            $success = true;
        }
    }
    
    if($success) {
        echo "<div>Selected backup files deleted successfully. <button onclick='window.close();'>Close</button></div>";
    } else {
        echo "<div>Error deleting selected backup file or file does not exist. <button onclick='window.close();'>Close</button></div>";
    }

    exit;
}

// Handle backup process and file operations
if(isset($_POST['download']) || isset($_GET['download_backup']) || isset($_POST['check_backup_progress']) || isset($_POST['delete_backup']) || isset($_POST['delete_backups'])) {
    require_once "functions/backup.php";
    $bk = new backup;
    
    // Handle single backup deletion
    if(isset($_POST['delete_backup'])) {
        $file = $backupDir . '/' . $_POST['delete_backup'];
        if(file_exists($file) && unlink($file)) {
            echo json_encode(["status" => "success", "message" => "Backup file deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete backup file"]);
        }
        exit;
    }
    
    // Check if this is a progress check request
    if(isset($_POST['check_backup_progress'])) {
        $progressFile = $backupDir . '/backup_progress.json';
        if(file_exists($progressFile)) {
            $progress = json_decode(file_get_contents($progressFile), true);
            if($progress['status'] === 'in_progress') {
                echo json_encode([
                    "status" => "error",
                    "message" => "Backup is still in progress. Please wait.",
                    "progress" => [
                        "completed_tables" => $progress['completed_tables'],
                        "total_tables" => $progress['total_tables'],
                        "processed_rows" => $progress['processed_rows'],
                        "total_rows" => $progress['total_rows'],
                        "elapsed_time" => time() - $progress['start_time']
                    ]
                ]);
                exit;
            } else if($progress['status'] === 'completed') {
                echo json_encode(["status" => "success"]);
                exit;
            } else if($progress['status'] === 'failed') {
                echo json_encode([
                    "status" => "error",
                    "message" => "Backup failed: " . ($progress['error'] ?? 'Unknown error')
                ]);
                exit;
            }
        }
        echo json_encode(["status" => "error", "message" => "No backup in progress"]);
        exit;
    }
    
    // Start new backup or handle download request
    
}

if(isset($_GET['download_backup']) || isset($_POST['download_backup_ajax'])) {
    require_once "functions/backup.php";
    $bk = new backup;
    
    // Determine the file name from either GET or POST
    $fileName = isset($_GET['download_backup']) ? $_GET['download_backup'] : $_POST['download_backup_ajax'];
    $bk->downloadBackupFile($fileName);
} else if (isset($_POST['download'])) {
     // This is a new backup request triggered by the start button
    try {
        $export = $bk->exportLargeDatabase();
        if($export) {
            echo json_encode(["status" => "success", "message" => "Backup completed successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Backup failed"]);
        }
    } catch(Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}