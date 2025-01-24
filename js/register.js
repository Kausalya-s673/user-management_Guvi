$(document).ready(function() {
    $('#registerForm').on('submit', function(event) {
        event.preventDefault();

  
        $('#nameError').text('');
        $('#emailError').text('');
        $('#passwordError').text('');

        var valid = true;
        var name = $('#name').val().trim();
        var email = $('#email').val().trim();
        var password = $('#password').val().trim();

     
        if (name === '') {
            $('#nameError').text('Username is required.');
            valid = false;
        }

  
        if (email === '') {
            $('#emailError').text('Email is required.');
            valid = false;
        } else if (!validateEmail(email)) {
            $('#emailError').text('Invalid email format.');
            valid = false;
        }

        
        if (password === '') {
            $('#passwordError').text('Password is required.');
            valid = false;
        } else if (password.length < 6) {
            $('#passwordError').text('Password must be at least 6 characters long.');
            valid = false;
        }

        if (valid) {
            $.ajax({
                url: '../php/register.php',
                method: 'POST',
                data: {
                    name: name,
                    email: email,
                    password: password
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert('Registration successful');
                        window.location.href = '/login.html';
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
