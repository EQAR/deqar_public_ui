(function($){

    $('body')
        // Navigation
        .on('click', '.js-header__menu', function(){
            $('.js-navigation').slideToggle();
        })
		.on('click', '.js-toggle-children', function(){
            $(this).toggleClass('menu__toggle--is-active');
			$(this).next('.js-menu__children').slideToggle();
        })
        .on('click', '.js-toggle-grandchildren', function(){
            $(this).toggleClass('menu__toggle--is-active');
			$(this).next('.js-menu__grandchildren').slideToggle();
        })
        // Image Accordion
        .on('click', '.js-image-accordion__read-more', function(){
            $(this).next('.js-image-accordion__expandable').slideToggle();
        })
        // Accordion
        .on('click', '.js-accordion__definition', function(){
            $(this).toggleClass('accordion__definition--is-toggled');
            $(this).next('.js-accordion__description').slideToggle();
        })
        // Social Share
        .on('click', '.js-share__link', function(e) {
			e.preventDefault();
			windowPopup($(this).attr('href'), 974, 682);
		});
        

    // Responsible Social Share Links enhancement by https://jonsuh.com/blog/social-share-links/
	function windowPopup(url, width, height) {
		
		var left = (screen.width / 2) - (width / 2),
			top = (screen.height / 2) - (height / 2);
	
		window.open(
			url,
			"",
			"menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left
		);
    }
    
})(jQuery);