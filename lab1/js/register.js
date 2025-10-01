$(document).ready(function() {
    $('#registrationForm').submit(function(e) {
        e.preventDefault();

        name = $('#fullname').val();
        email = $('#email').val();
        password = $('#password').val();
        confirm_password = $('#confirm_password').val();
        country = $('#country').val();
        city = $('#city').val();
        phone_number = $('#phone_number').val();
        
        var roleValue = $('input[name="role"]').is(':checked') ? 2 : 1; //determine role value based on checkbox

        // Email regex validation
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (name == '' || email == '' || password == '' || confirm_password =='' || country == '' || city == '' || phone_number == '') {
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

        } else if(!emailRegex.test(email)) { //regex for email format
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Submit the form
        $.ajax({
            url: '../functions/register_user_action.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password,
                country: country,
                city: city,
                phone_number: phone_number,
                role: roleValue
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
                        title: 'New error..',
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

    // Function to validate country and city
    function validateCountryAndCity(country, city) {
        return new Promise((resolve, reject) => {
            // First validate country
            fetch(`https://restcountries.com/v3.1/name/${encodeURIComponent(country)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 404) {
                        resolve(false);
                        return;
                    }
                    
                    // If country is valid, validate city
                    const countryCode = data[0].cca2;
                    return fetch(`https://api.api-ninjas.com/v1/city?name=${encodeURIComponent(city)}&country=${countryCode}`)
                        .then(response => response.json())
                        .then(cityData => {
                            if (cityData.length > 0) {
                                resolve(true);
                            } else {
                                resolve(false);
                            }
                        });
                })
                .catch(error => {
                    // Fallback: Use a simpler validation approach
                    const validCountries = [
                        'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 
                        'France', 'Italy', 'Spain', 'Japan', 'China', 'India', 'Brazil', 
                        'Mexico', 'Netherlands', 'Sweden', 'Norway', 'Denmark', 'Finland',
                        'Switzerland', 'Austria', 'Belgium', 'Poland', 'Czech Republic',
                        'Hungary', 'Portugal', 'Greece', 'Turkey', 'Russia', 'South Korea',
                        'Singapore', 'Thailand', 'Malaysia', 'Indonesia', 'Philippines',
                        'Vietnam', 'New Zealand', 'South Africa', 'Egypt', 'Nigeria',
                        'Kenya', 'Morocco', 'Argentina', 'Chile', 'Colombia', 'Peru',
                        'Venezuela', 'Ecuador', 'Uruguay', 'Paraguay', 'Bolivia'
                    ];
                    
                    const isValidCountry = validCountries.some(validCountry => 
                        validCountry.toLowerCase().includes(country.toLowerCase()) ||
                        country.toLowerCase().includes(validCountry.toLowerCase())
                    );
                    
                    if (!isValidCountry) {
                        resolve(false);
                        return;
                    }
                    
                    // Basic city validation (non-empty and reasonable length)
                    const isValidCity = city.trim().length >= 2 && city.trim().length <= 50;
                    resolve(isValidCity);
                });
        });
    }

});