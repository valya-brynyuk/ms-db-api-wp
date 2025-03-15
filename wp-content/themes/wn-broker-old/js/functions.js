jQuery(document).ready(function(){

	/*------------------------------------------*\
		CAROUSEL
	\*------------------------------------------*/
	
	jQuery('.banner-carousel').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 1,
		arrows: false
	});

});

/*------------------------------------------------------------------------------------*\
	MOBILE WIDTH CHECK (JQUERY WINDOW WIDTH IS INACCURATE IF THERE IS A SCROLLBAR)
\*------------------------------------------------------------------------------------*/

function isMobileWidth() {
	return window.matchMedia('(max-width: 767px)').matches;
}