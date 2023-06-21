document.addEventListener('DOMContentLoaded', function() {
    var myDiv = document.getElementById('banner-ppd');
    var siteh = document.getElementById('site-header');
    var mainh = document.getElementById('main-header');
    var masthead = document.getElementById('masthead');
    var header = document.getElementById('header');

    if (myDiv && (siteh || mainh || masthead || header)) {
        if (siteh) {
            siteh.appendChild(myDiv);
        } else if (mainh) {
            mainh.appendChild(myDiv);
        } else if (masthead) {
            masthead.appendChild(myDiv);
        } else if (header) {
            header.appendChild(myDiv);
        }
    } else {
        console.error('PPD: Could not find one or both elements');
    }
});
