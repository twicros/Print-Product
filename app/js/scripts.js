$(function () {
	$('section.testimonials .carousel').carousel({
		interval: false
	});



	$("a[href^='#']").on('click', function(e) {

	   // prevent default anchor click behavior
	   e.preventDefault();

	   // store hash
	   var hash = this.hash;

	   // animate
	   $('html, body').animate({
	       scrollTop: $(hash).offset().top-100
	     }, 1000, function(){

	       // when done, add hash to url
	       // (default click behaviour)
	       window.location.hash = hash;
	     });

	});

	 
	$('.nav a').on('click', function(){
    	$('.navbar-toggle').click() //Скрываем мобильное меню по клику на ссылке
	});

	$('.nav #my-cart-9339076').on('click', function(){
    	$('.navbar-toggle').click() //Скрываем мобильное меню по клику на корзине
	});
	
});

$(document).ready(function(){
  $(":input").inputmask();
});

