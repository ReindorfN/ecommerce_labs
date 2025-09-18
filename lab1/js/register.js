$(document).ready(function(){
    $('#registrationForm').submit(function(e) {
        e.preventDefault();

        username = $('#name').val();
        email = $('#email').val();
        password = $('#password').val();
        confirm_password = $('#confirm_password').val();
        country = $('#country').val();
        city = $('#city').val();
        phone_number = $('#phone_number').val();
        role = $('input[name="role"]:checked').val();

        // Email regex validation
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (username == '' || email == '' || password == '' || confirm_password ==''|| country == '' | city == '' || phone_number == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });

            return;
        } else if(password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });

            return;
        } else if(password.test(confirm_password)){ //checking is password is the same as confirm password
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords do not match!',
            });
            return;
        } else if(!emailRegex.test(email)) { //regex for email format
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        };



        $.ajax({
            url: '../functions/register_user_action.php',
            type: 'POST',
            data: {
                name: username,
                email: email,
                password: password,
                country: country,
                city: city,
                phone_number: phone_number,
                role: role
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
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