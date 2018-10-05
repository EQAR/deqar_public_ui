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

        // Display or hide historical reports
        .on('change', '.js-report-witch-institutional', function(e) {
			e.preventDefault();

            var checked = this.checked;

            var $container  = $(this).parent().parent();
            var all         = $container.find('.accordion__item').length;
            var invalid     = $container.find('.accordion__item.report-invalid').length;
            var valid       = (all - invalid);

            $container.find('.accordion__item')
                .removeClass('item-hidden')
                .addClass('item-visible');

            // Include historical reports (invalid)
            if ( checked == true ) {
                $container.find('.accordion__item.report-invalid')
                    .removeClass('item-hidden')
                    .addClass('item-visible');
                $('.reports-institutional-title .subtitle-superscript').text('(' + all + ')')
            }

            // Exclude historical reports (invalid)
            if ( checked == false ) {
                $container.find('.accordion__item.report-invalid')
                    .removeClass('item-visible')
                    .addClass('item-hidden');
                $('.reports-institutional-title .subtitle-superscript').text('(' + valid + ')')
            }

            // Reset the paginator
            paginatorInstitutional.init();

		})

        // Display or hide historical reports
        .on('change', '.js-report-witch-programme', function(e) {
			e.preventDefault();

            var checked = this.checked;

            var $container  = $(this).parent().parent();
            var all         = $container.find('.accordion__item').length;
            var invalid     = $container.find('.accordion__item.report-invalid').length;
            var valid       = (all - invalid);

            $container.find('.accordion__item')
                .removeClass('item-hidden')
                .addClass('item-visible');

            // Include historical reports (invalid)
            if ( checked == true ) {
                $container.find('.accordion__item.report-invalid')
                    .removeClass('item-hidden')
                    .addClass('item-visible');
                $('.reports-programme-title .subtitle-superscript').text('(' + all + ')')
            }

            // Exclude historical reports (invalid)
            if ( checked == false ) {
                $container.find('.accordion__item.report-invalid')
                    .removeClass('item-visible')
                    .addClass('item-hidden');
                $('.reports-programme-title .subtitle-superscript').text('(' + valid + ')')
            }

            // Reset the paginator
            paginatorProgramme.init();

		});


    // Set paginator on programme reports
    var paginatorInstitutional = new $('.reports-institutional-container').slikPaginator({
        'perPage': 10,
        'items': '.accordion__item',
        'paginator': '.pagination__list--institutional',
        'indicator': {
            'selector': '.pagination__indidator--institutional',
            'text': '{start} - {end} of {total} institutional level reports',
        }
    });

    // Set paginator on programme reports
    var paginatorProgramme = new $('.reports-programme-container').slikPaginator({
        'perPage': 10,
        'items': '.accordion__item',
        'paginator': '.pagination__list--reports',
        'indicator': {
            'selector': '.pagination__indidator--reports',
            'text': '{start} - {end} of {total} programme level reports',
        }
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
