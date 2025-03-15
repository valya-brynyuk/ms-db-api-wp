jQuery(document).ready(function(){

	/*------------------------------------------*\
		CAROUSEL
	\*------------------------------------------*/
	
	jQuery('.quote-carousel').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 1,
		arrows: false
	});

	jQuery('.button-carousel').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 1,
		arrows: false
	});

});

/*------------------------------------------------------------------------------------*\
   TRIGGERS
\*------------------------------------------------------------------------------------*/

body = jQuery('body');
trigger = jQuery('.nav-trigger');
menu = jQuery('.top-nav');
trigger.click(function(e){
	e.preventDefault();
	menu.fadeToggle('fast').css('display', 'flex');
	trigger.toggleClass('is-clicked');
	body.toggleClass('nav-open');
});



/*------------------------------------------------------------------------------------*\
	DEBOUNCE FUNCTIONS
\*------------------------------------------------------------------------------------*/

jQuery(window).on("debouncedresize", function (event) {
	if (isMobileWidth()) {
		menu.css('display', '');
		body.removeClass('nav-open');
		trigger.removeClass('is-clicked');
		// menu.css('display', '');
	}
	else {
		menu.css('display', '');
		body.removeClass('nav-open');
		trigger.removeClass('is-clicked');
		// menu.css('display', '');
	}
});

/*------------------------------------------------------------------------------------*\
	MOBILE WIDTH CHECK (JQUERY WINDOW WIDTH IS INACCURATE IF THERE IS A SCROLLBAR)
\*------------------------------------------------------------------------------------*/

function isMobileWidth() {
	return window.matchMedia('(max-width: 767px)').matches;
}