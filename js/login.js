$(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();

        // Clear previous error messages
        $('#emailError').text('');
        $('#passwordError').text('');

        var valid = true;
        var email = $('#email').val().trim();
        var password = $('#password').val().trim();

        // Email validation
        if (email === '') {
            $('#emailError').text('Email is required.');
            valid = false;
        } else if (!validateEmail(email)) {
            $('#emailError').text('Invalid email format.');
            valid = false;
        }

        // Password validation
        if (password === '') {
            $('#passwordError').text('Password is required.');
            valid = false;
        } else if (password.length < 6) {
            $('#passwordError').text('Password must be at least 6 characters long.');
            valid = false;
        }

        if (valid) {
            $.ajax({
                url: '../php/login.php',
                method: 'POST',
                data: {
                    email: email,
                    password: password
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    console.log(result);

                    if (result.success) {
                        localStorage.setItem('email', email);
                        alert('Login successful');
                        window.location.href = '/profile.html';
                    } else {
                        alert(result.message);
                    }
                }
            });
        }
    });

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
