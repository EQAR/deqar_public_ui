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
        // QA Search Advanced Search
        .on('click', '.js-qa-search__toggle', function(e){
            e.preventDefault();
            $('.js-qa-search__expand').slideToggle();
        })
        // Social Share
        .on('click', '.js-share__link', function(e) {
			e.preventDefault();
			windowPopup($(this).attr('href'), 974, 682);
		})
        // Social Share
        .on('change', '.js-select-go', function(e) {
			e.preventDefault();
			window.location.href = $(this).val();
		})

        // Social Share
        .on('change', '.js-report-witch', function(e) {
			e.preventDefault();

            // console.log( this.checked );
            var checked = this.checked;

            // console.log( $(this).parent().parent().find('.accordion__item.report-invalid') );

            $(this).parent().parent().find('.accordion__item').removeClass('item-visible').addClass('item-hidden');

            if ( checked == true ) {
                $(this).parent().parent().find('.accordion__item.report-invalid').removeClass('item-hidden').addClass('item-visible');
                $(this).parent().parent().find('.accordion__item.report-invalid').show();
            }
            if ( checked == false ) {
                $(this).parent().parent().find('.accordion__item').removeClass('item-hidden').addClass('item-visible');
                $(this).parent().parent().find('.accordion__item.report-invalid').removeClass('item-visible').addClass('item-hidden');
                $(this).parent().parent().find('.accordion__item.report-invalid').hide();
            }

            // paginator.items();

		});


    // Set paginator on programme reports
    var paginator = $('.reports-programme-container').slikPaginator({
        'perPage': '5',
        'items': '.accordion__item',
        'paginator': '.pagination__list--reports',
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
