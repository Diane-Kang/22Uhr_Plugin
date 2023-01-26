
// Javascript für die Seite mit Karte
//  Der hier geschriebene Code sollte nichts leaflet.js zu tun haben.

jQuery(document).ready(function($){
$('.site-header-primary-section-right').click(function() {
   $('body').addClass('nav-show-22');
});

	$('.navicon-close').click(function() {
   $('body').removeClass('nav-show-22');
});
});



// var open_childblock_bnt = document.querySelector('.ionicon-chevron-down');
jQuery('.icon-click-area').click(function(e){
   e.target.parentNode.parentNode.parentNode.classList.toggle('child-block-open');
})


