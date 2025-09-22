$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();

        const email = $('#email').val();
        const password = $('#password').val();
        const remember = $('#remember').is(':checked');

        // Email regex validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Basic validation
        if (email === '' || password === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });
            return;
        }

        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Submit login request
        $.ajax({
            url: '../functions/login_user_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password,
                remember: remember
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome Back!',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect based on user role
                            if (response.user_data.role === 1) {
                                window.location.href = '../index.php'; // Admin dashboard
                            } else {
                                window.location.href = '../index.php'; // Customer dashboard
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});
