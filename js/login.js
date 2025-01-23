$(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '../php/login.php',
            method: 'POST',
            data: {
                email: $('#email').val(),
                password: $('#password').val()
            },
            success: function(response) {
                console.log(response);
                
                if (response.includes('success')) {
                    localStorage.setItem('email', $('#email').val());
                    alert('Login successful');
                    window.location.href = '/profile.html';
                } else {
                    alert('Login failed');
                }
            }
        });
    });
});
