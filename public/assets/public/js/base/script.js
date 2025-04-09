const profileMenu = document.getElementById('profile-menu');
const profileDropdown = document.getElementById('profile-dropdown');

if (profileMenu && profileDropdown) {
    profileMenu.addEventListener('click', function () {
        profileDropdown.classList.toggle('show');
    });

    document.addEventListener('click', function (event) {
        if (!profileMenu.contains(event.target)) {
            profileDropdown.classList.remove('show');
        }
    });
}


const mobileBurger = document.getElementById('mobile-burger');
const mobileMenu = document.getElementById('mobile-menu');

mobileBurger.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
});


