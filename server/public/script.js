var navbar_menu = document.getElementById('navbar-menu');
var navbar_burger = document.getElementById('navbar-burger');
navbar_burger.addEventListener('click', function (event) {
    event.preventDefault();
    navbar_menu.classList.toggle('is-active');
    navbar_burger.classList.toggle('is-active');
}, false);
