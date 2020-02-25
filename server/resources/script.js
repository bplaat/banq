// When serviceworker is available register the serviceworker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/serviceworker.min.js');
}

// Select the hamburger menu button from the navabar and toggle the navbar when you click it
var navbar_burger = document.getElementById('navbar-burger');
var navbar_menu = document.getElementById('navbar-menu');
if (navbar_burger != undefined && navbar_menu != undefined) {
    navbar_burger.addEventListener('click', function (event) {
        event.preventDefault();
        navbar_burger.classList.toggle('is-active');
        navbar_menu.classList.toggle('is-active');
    }, false);
}

// Select all the close buttons of every notification and delete the notification when clicked on it
(document.querySelectorAll('.notification .delete') || []).forEach(function (delete_button) {
    delete_button.addEventListener('click', function (event) {
        event.preventDefault();
        this.parentNode.parentNode.removeChild(this.parentNode);
    });
});
