const profileMenu = document.getElementById('profile-menu');
const profileDropdown = document.getElementById('profile-dropdown');

profileMenu.addEventListener('click', function() {

    if (profileDropdown.style.display === 'block') {
        profileDropdown.style.display = 'none';
    } else {
        profileDropdown.style.display = 'block';
    }
});


document.addEventListener('click', function(event) {
    if (!profileMenu.contains(event.target)) {
        profileDropdown.style.display = 'none';
    }
});
