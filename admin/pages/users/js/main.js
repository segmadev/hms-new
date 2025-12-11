// admin/pages/user/js/main.js

const UserManager = {
    toggleStatus: function(userId, currentStatus) {
        const newStatus = currentStatus ? 0 : 1;
        const action = currentStatus ? 'block' : 'unblock';
        
        Swal.fire({
            title: `Are you sure?`,
            text: `Do you want to ${action} this user?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${action} user!`
        }).then((result) => {
            if (result.isConfirmed) {
                this.showLoading('Processing...');
                
                const formData = new FormData();
                formData.append('action', 'toggleStatus');
                formData.append('ID', userId);
                formData.append('status', newStatus);

                this.sendRequest(formData);
            }
        });
    },

    deleteUser: function(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete user!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.showLoading('Processing...');
                
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('ID', userId);

                this.sendRequest(formData);
            }
        });
    },

    handleFormSubmit: function(formElement) {
        this.showLoading('Updating User');
        
        const formData = new FormData(formElement);
        this.sendRequest(formData);
    },

    sendRequest: function(formData) {
        fetch('passer', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        });
    },

    showLoading: function(message = 'Please wait...') {
        Swal.fire({
            title: message,
            html: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Edit form handler
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            UserManager.handleFormSubmit(this);
        });
    }

    validatePassword();
});

function validatePassword() {
    const password = document.querySelector('input[name="password"]');
    const confirm = document.querySelector('input[name="confirm_password"]');
    
    if (password && confirm) {
        confirm.addEventListener('input', function() {
            if (this.value !== password.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        password.addEventListener('input', function() {
            if (confirm.value !== '') {
                if (confirm.value !== this.value) {
                    confirm.setCustomValidity('Passwords do not match');
                } else {
                    confirm.setCustomValidity('');
                }
            }
        });
    }
}