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
        // Member
        .on('click', '.js-member__read-more', function(){
            $(this).next('.js-member__expandable').slideToggle();
        })
        // Accordion
        .on('click', '.js-accordion__definition', function(){
            $(this).toggleClass('accordion__definition--is-toggled');
            $(this).next('.js-accordion__description').slideToggle();
        });
        
})(jQuery);