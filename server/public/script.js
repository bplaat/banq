if ('serviceWorker' in navigator) {
    // navigator.serviceWorker.register('/serviceworker.js');
}

var navbar_burger = document.getElementById('navbar-burger');
var navbar_menu = document.getElementById('navbar-menu');
if (navbar_burger != undefined && navbar_menu != undefined) {
    navbar_burger.addEventListener('click', function (event) {
        event.preventDefault();
        navbar_burger.classList.toggle('is-active');
        navbar_menu.classList.toggle('is-active');
    }, false);
}
