const profileMenu = document.getElementById('profile-menu');
const profileDropdown = document.getElementById('profile-dropdown');

profileMenu.addEventListener('click', function () {
    profileDropdown.classList.toggle('show');
});

document.addEventListener('click', function (event) {
    if (!profileMenu.contains(event.target)) {
        profileDropdown.classList.remove('show');
    }
});


