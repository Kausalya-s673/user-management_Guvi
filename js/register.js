$(document).ready(function() {
    $('#registerForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '../php/register.php',
            method: 'POST',
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val()
            },
            success: function(response) {
                alert('Registration successful');
                window.location.href = '/login.html';
            }
        });
    });
});


