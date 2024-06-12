document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        document.querySelector('.wrapper').classList.add('loaded');
    }, preloaderSettings.timeout);
});
