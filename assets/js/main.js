"use strict";
// burger_dropdown
function toggleEventsSubmenu(event) {
    event.preventDefault();
    const btn = event.target;
    const submenu = btn.nextElementSibling; Ы
    btn.classList.toggle('activeee');

    submenu.classList.toggle('show');
}

document.addEventListener('click', function (e) {
    const container = document.querySelector('.events-menu-container');
    if (!container.contains(e.target)) {
        const btn = container.querySelector('.events-menu-btn');
        const submenu = container.querySelector('.events-submenu');

        btn.classList.remove('activeee');
        submenu.classList.remove('show');
    }
});

// burger
function openMenu(event) {
    event.preventDefault();
    const cart = document.querySelector(".menu-section");
    cart.classList.add("menu-open");
    document.getElementById("overlay").style.display = "block";
    document.body.style.overflow = 'hidden';
}

function closeMenu() {
    const cart = document.querySelector(".menu-section");
    cart.classList.remove("menu-open");
    document.getElementById("overlay").style.display = "none";
    document.body.style.overflow = '';
}

// drop_desctope
function myFunction(event) {
    event.preventDefault();
    const dropdown = event.target.closest('.dropdown').querySelector('.dropdown-content');
    dropdown.classList.toggle("show");

    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest('.dropdown')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// dropdown for menu-burger
function toggleEventsSubmenu(event) {
    event.preventDefault();
    event.stopPropagation();
    const submenu = event.target.closest('.events-menu-container').querySelector('.events-submenu');
    submenu.classList.toggle('show');
}

// Закрытие при клике вне меню
document.addEventListener('click', function (e) {
    if (!e.target.closest('.events-menu-container')) {
        const openSubmenus = document.querySelectorAll('.events-submenu.show');
        openSubmenus.forEach(submenu => {
            submenu.classList.remove('show');
        });
    }
});