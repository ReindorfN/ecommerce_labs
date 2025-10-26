$(document).ready(function() {
    $('#logoutBtn').click(function(e) {
        e.preventDefault();

        // Optional: Confirm logout with user
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {

                window.location.href = 'functions/logout_user_action.php';
                

                // Method 1: Try AJAX first
                $.ajax({
                    url: 'functions/logout_user_action.php',
                    type: GET,
                    dataType: 'json',
                    timeout: 5000, // 5 second timeout
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged Out',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'login/login.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Logout Failed',
                                text: response.message
                            });
                        }
                    },
                });
            }
        });
    });
});
