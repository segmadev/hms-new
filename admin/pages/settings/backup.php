<?php 
    $role = new roles;
    if($role->validate_action(["settings"=>"list"], true)) {
        $backupFiles = $bk->getbackfiles();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Database Backup</h4>
                    <div class="alert alert-warning"><b class="text-danger">Important:</b> Please make sure to delete all backups after downloading.</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form id="deleteBackupsForm" method="POST">
                            <input type="hidden" name="page" value="settings">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>Backup File</th>
                                        <th>Size</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($backupFiles)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No backup files found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($backupFiles as $file): 
                                            $fileName = basename($file);
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input backup-checkbox" type="checkbox" name="selected_backups[]" value="<?php echo htmlspecialchars($fileName); ?>">
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($fileName); ?></td>
                                                <td><?php echo number_format(filesize($file) / 1024 / 1024, 2); ?> MB</td>
                                                <td><?php echo date('Y-m-d H:i:s', filemtime($file)); ?></td>
                                                <td>
                                                    <a target="_BLANK" type="button" class="btn btn-sm btn-info download-backup" href="passer.php?pg=settings&download_backup_ajax=true&download_backup=<?php echo htmlspecialchars($fileName); ?>">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                    <a target="_BLANK" href="passer.php?pg=settings&delete_backups=<?php echo htmlspecialchars($fileName); ?>" 
                                                       class="btn btn-sm btn-danger delete-backup"
                                                       onclick="return confirm('Are you sure you want to delete this backup file?');">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <?php if(!empty($backupFiles)): ?>
                                <button type="button" id="deleteSelected" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Checkbox
    const selectAll = document.getElementById('selectAll');
    const backupCheckboxes = document.querySelectorAll('.backup-checkbox');
    
    selectAll.addEventListener('change', function() {
        backupCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Individual Delete Buttons


    // Individual Download Buttons
    document.querySelectorAll('.download-backup').forEach(button => {
        button.addEventListener('click', function() {
            const file = this.dataset.file;
            downloadBackup(file);
        });
    });

    // Delete Selected Button
    document.getElementById('deleteSelected')?.addEventListener('click', function() {
        const selectedFiles = Array.from(document.querySelectorAll('.backup-checkbox:checked')).map(cb => cb.value);
        if(selectedFiles.length === 0) {
            alert('Please select at least one backup file to delete');
            return;
        }
        if(confirm(`Are you sure you want to delete ${selectedFiles.length} selected backup file(s)?`)) {
            deleteBackups(selectedFiles);
        }
    });

    // Delete single backup
    function deleteBackup(file) {
        fetch('passer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `delete_backup=${encodeURIComponent(file)}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message || 'Error deleting backup file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting backup file');
        });
    }

    // Delete multiple backups
    function deleteBackups(files) {
        fetch('passer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `delete_backups=${JSON.stringify(files)}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message || 'Error deleting backup files');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting backup files');
        });
    }

    // Download single backup via AJAX
    function downloadBackup(file) {
        // Show loading state
        const button = document.querySelector(`[data-file="${file}"]`);
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
        button.disabled = true;

        // Create form data
        const formData = new FormData();
        formData.append('download_backup_ajax', file);
        formData.append('page', 'settings');

        // Send AJAX request
        fetch('passer.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Download failed');
                });
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = file;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error downloading file. Please try again.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
});
</script>
<?php
    }
?>