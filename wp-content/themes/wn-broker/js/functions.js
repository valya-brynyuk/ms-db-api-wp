jQuery(document).ready(function () {


	/*------------------------------------------*\
		CAROUSEL
	\*------------------------------------------*/

	jQuery('.quote-carousel').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 1,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 3800

	});
	jQuery('.quote-carousel').show();

	jQuery('.button-carousel').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 1,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 2700,
		fade: true,
		cssEase: 'linear'
	});

	jQuery('.button-carousel').show();

});


ScrollReveal().reveal('.slide-in', { delay: 290, distance: '15px', duration: 1500 });
ScrollReveal().reveal('.sequenced', { delay: 290, interval: 150, duration: 1500 });
ScrollReveal().reveal('.scale-up', { delay: 290, scale: 0.85, duration: 1500 });

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
	SORTING API TABLE
\*------------------------------------------------------------------------------------*/
var listOptions = {
    valueNames: [ 'client', 'matter-number','progress' ]
};

var userList = new List('sortable-bs', listOptions);



var listOptions2 = {
    valueNames: [ 'type', 'applicant','price' ]
};

var userList2 = new List('sortable-bs-2', listOptions2);

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