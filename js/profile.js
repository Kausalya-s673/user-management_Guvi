$(document).ready(function() {
    function fetchProfile() {
        $.ajax({
            url: '../php/profile.php',
            method: 'GET',
            data: {
                email: localStorage.getItem('email')
            },
            success: function(response) {
                const profile = JSON.parse(response);
                if (profile) {
                    $('#age').val(profile.age || '');
                    $('#dob').val(profile.dob || '');
                    $('#contact').val(profile.contact || '');
                    if (profile.profilePicture) {
                        $('#profileImg').attr('src', 'data:image/jpeg;base64,' + profile.profilePicture).show();
                        $('.alert-info').hide(); 
                    }
                } else {
                    $('.alert-info').show(); 
                    $('#profileImg').hide(); 
                    $('#age').val('');
                    $('#dob').val('');
                    $('#contact').val('');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error fetching profile data:', error);
            }
        });
    }

    fetchProfile();

    $('#profileForm').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append('email', localStorage.getItem('email'));
        $.ajax({
            url: '../php/profile.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('Profile updated successfully');
                fetchProfile();
            }
        });
    });

    document.getElementById('profilePicture').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImg').src = e.target.result;
                document.getElementById('profileImg').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});
