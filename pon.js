jQuery(document).ready(function($){
$('.site-header-primary-section-right').click(function() {
   $('body').addClass('nav-show-22');
});

	$('.navicon-close').click(function() {
   $('body').removeClass('nav-show-22');
});
});