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
        .on('click', '.js-image-accordion__read-more', function(e){
            $(this).toggleClass('image-accordion__read-more--is-toggled');
            $(this).next('.js-image-accordion__expandable').slideToggle();
            if ($(this).hasClass('image-accordion__read-more--is-toggled')) {
                history.replaceState(null, null, this.hash);
            }
            e.preventDefault();
        })
        // Accordion
        .on('click', '.js-accordion__definition', function(e){
            $(this).toggleClass('accordion__definition--is-toggled');
            $(this).next('.js-accordion__description').slideToggle();
            if ($(this).hasClass('accordion__definition--is-toggled')) {
                history.replaceState(null, null, this.hash);
            }
            e.preventDefault();
        })
        // Sidebar as accordion
        .on('click', '.js-sidebar--accordion', function() {
            $(this).toggleClass('sidebar--accordion--is-toggled');
            $(this).find('.sidebar__menu').slideToggle();
        })
        .on('click', '.js-toggle-modal', function(){
			var value = $(this).data('modal');

			$('.js-modal[data-modal="'+ value +'"]').toggleClass('modal--is-open');
		})
		.on('click', '.js-close-modal', function(){
			$(this).closest('.js-modal').removeClass('modal--is-open');
		})
        // Search term field
        .on('click', '.js-search-query', function(e) {
            $(e.currentTarget).find('.search-query__text').focus().select();
        })
        // Clear button
        .on('click', '.js-search-query__clear', function(e) {
            $(e.currentTarget).parent().find('.search-query__text').val('');
        })
	    // search order popup
        .on('click', '.js-search-bar__order', function(e) {
            if ($(this).find('ul.search-order').hasClass('search-order--expanded')) {
                if (e.target.nodeName != "A") {
                    $(this).find('ul.search-order').removeClass('search-order--expanded');
                    $(this).find('ul.search-order li.js-not-current').slideToggle();
                }
            } else {
                $(this).find('ul.search-order').addClass('search-order--expanded');
                $(this).find('ul.search-order li.js-not-current').slideToggle();
                e.preventDefault();
            }
        })
        // submit page select form on change
        .on('change', '.js-pagination__select', function(e) {
            $(this.form).submit();
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
        // Toggle checkbox when clicking label
        .on('click', '.report-switch label', function(e) {
            $(this).parent().find('input[type=checkbox]').click();
        })
        // Apply filters
        .on('change', '.js-report-switch', function(e) {
            e.preventDefault();

            var filters = $(this).data("filters");

            for (var field in filters) {
                for (var value in filters[field].values) {
                    filters[field].values[value].checked = filters[field].values[value].input.prop("checked");
                    filters[field].values[value].counter = 0;
                    if (filters[field].values[value].checked) {
                        filters[field].values[value].outer.addClass('report-switch--active');
                    } else {
                        filters[field].values[value].outer.removeClass('report-switch--active');
                    }
                }
            }

            $(this).data("containers").forEach(function(i) {
                var visible_reports = 0;
                $(i.container).find('.facet-search__item').each(function() {
                    $(this).removeClass('item-hidden').addClass('item-visible');
                    for (var field in filters) {
                        var value = $(this).attr("data-" + field);
                        if ( (typeof value !== typeof undefined) && (value !== false) && (! filters[field].values[value].checked) ) {
                            // hide entry if value is set, but not contained in selected values
                            $(this).addClass('item-hidden').removeClass('item-visible');
                        }
                    }
                    if ($(this).hasClass('item-visible')) {
                        visible_reports++;
                    }
                    for (var field in filters) {
                        var value = $(this).attr("data-" + field);
                        if ( (typeof value !== typeof undefined) && (
                                ($(this).hasClass('item-visible') && filters[field].values[value].checked)
                           || (! $(this).hasClass('item-visible') && ! filters[field].values[value].checked) ) ) {
                            filters[field].values[value].counter++;
                        }
                    }
                });
                // Reset the paginators
                if (i.paginator) {
                    i.paginator.init();
                    if (visible_reports <= 10) {
                        // fix indicator if no pages
                        $(i.indicator).text(
                            visible_reports == 0 ? "No reports match your filter" :
                            visible_reports + " report" + ( visible_reports == 1 ? "" : "s" )
                        );
                    }
                } else {
                    $(i.indicator).text(
                        visible_reports == 0 ? "No items match your filter" :
                        visible_reports + " item" + ( visible_reports == 1 ? "" : "s" )
                    );
                }
            });
            for (var field in filters) {
                for (var value in filters[field].values) {
                    filters[field].values[value].container.text(filters[field].values[value].checked ? "(" + filters[field].values[value].counter + ")" : "");
                    if (filters[field].values[value].checked && filters[field].values[value].counter == 0) {
                        filters[field].values[value].input.prop("disabled", true);
                        filters[field].values[value].outer.removeClass('report-switch--active');
                    } else {
                        filters[field].values[value].input.prop("disabled", false);
                    }
                    //filters[field][value].container.text("(" + filters[field][value].counter + ")");
                }
            }
		})
        // Run once
        .ready(function() {
            // open (image) accordion if addressed in URL fragment
            $('.js-accordion__definition').each(function() {
                if (this.hash == window.location.hash) {
                    $(this).toggleClass('accordion__definition--is-toggled');
                    $(this).next('.js-accordion__description').toggle();
                }
            });
            $('.js-image-accordion__read-more').each(function() {
                if (this.hash == window.location.hash) {
                    $(this).toggleClass('image-accordion__read-more--is-toggled');
                    $(this).next('.js-image-accordion__expandable').toggle();
                }
            });
            // sort programme level QA reports
            $('.reports-programme-container > .accordion__item').sort( function(a,b) {
                var cmpName = a.dataset.programme_name.localeCompare(b.dataset.programme_name);
                if (cmpName == 0) {
                    var cmpLevel = a.dataset.programme_level.localeCompare(b.dataset.programme_level);
                    if (cmpLevel == 0) {
                        var aDate = new Date(a.dataset.valid_from);
                        var bDate = new Date(b.dataset.valid_from);
                        if (aDate < bDate) {
                            return(-1);
                        } else if (aDate > bDate) {
                            return(1);
                        } else {
                            return(0);
                        }
                    } else {
                        return(cmpLevel);
                    }
                } else {
                    return(cmpName);
                }
            }).appendTo('.reports-programme-container');
            // sort institution level QA reports
            $('.reports-institutional-container > .accordion__item').sort( function(a,b) {
                var cmpName = a.dataset.report_title.localeCompare(b.dataset.report_title);
                if (cmpName == 0) {
                    var aDate = new Date(a.dataset.valid_from);
                    var bDate = new Date(b.dataset.valid_from);
                    if (aDate < bDate) {
                        return(-1);
                    } else if (aDate > bDate) {
                        return(1);
                    } else {
                        return(0);
                    }
                } else {
                    return(cmpName);
                }
            }).appendTo('.reports-institutional-container');
            // initialise report filters
            initFacetSearch('.js-report-switch', [
                { 'container': '.facet-search-container',          'paginator': null,                   'indicator': '.facet-search__indicator'},
                { 'container': '.reports-institutional-container', 'paginator': paginatorInstitutional, 'indicator': '.pagination__indicator--institutional'},
                { 'container': '.reports-programme-container',     'paginator': paginatorProgramme,     'indicator': '.pagination__indicator--reports'}
            ]);
            // collapse search facets on narrow display
            if (window.innerWidth < 1024) {
                $('.search-facets .js-accordion__description').hide();
                $('.search-facets .js-accordion__definition').removeClass('accordion__definition--is-toggled');
            }
        });

    var lastWidth = $(window).width();

    $(window).on('resize', function() {
        if ( (lastWidth < 1024) && ($(window).width() >= 1024) ) {
            $('.search-facets .js-accordion__description').show();
            $('.search-facets .js-accordion__definition').addClass('accordion__definition--is-toggled');
        } else if ( (lastWidth >= 1024) && ($(window).width() < 1024) ) {
            $('.search-facets .js-accordion__description').hide();
            $('.search-facets .js-accordion__definition').removeClass('accordion__definition--is-toggled');
        }
        lastWidth = $(window).width();
    });

    // Set paginator on programme reports
    var paginatorInstitutional = new $('.reports-institutional-container').slikPaginator({
        'perPage': 10,
        'items': '.accordion__item',
        'paginator': '.pagination__list--institutional',
        'indicator': {
            'selector': '.pagination__indicator--institutional',
            'text': '{start} - {end} of {total} reports',
        }
    });

    // Set paginator on programme reports
    var paginatorProgramme = new $('.reports-programme-container').slikPaginator({
        'perPage': 10,
        'items': '.accordion__item',
        'paginator': '.pagination__list--reports',
        'indicator': {
            'selector': '.pagination__indicator--reports',
            'text': '{start} - {end} of {total} reports',
        }
    });

    function initFacetSearch(facet_container, item_containers) {

        var filters = { };

        $(facet_container).find('ul.search-facet__choices').each(function() {
            filters[$(this).attr('data-report-field')] = {
                container: $(this),
                values: { }
            }
        });

        item_containers.forEach(function(i) {
            $(i.container).find('.facet-search__item').each(function() {
                $(this).removeClass('item-hidden').addClass('item-visible');
                for (var field in filters) {
                    var value = $(this).attr("data-" + field);
                    if (typeof value !== typeof undefined) {
                        if (! filters[field].values.hasOwnProperty(value)) {
                            filters[field].values[value] = true;
                        }
                    }
                }
            });
        });

        for (var field in filters) {
            var i = 0
            Object.keys(filters[field].values).sort().forEach(function(value) {
                var pre = filters[field].container.find('[data-value="'+value+'"]');
                if (pre.length > 0) {
                    var input = pre.find('input[type=checkbox]');
                    filters[field].values[value] = {
                        checked:    input.prop('checked'),
                        counter:    0,
                        container:  pre.find('.report-counter'),
                        input:      input,
                        outer:      pre
                    };
                } else {
                    var outer = $('<li/>', { class: 'report-switch', 'data-value': value });
                    var input = $('<input/>', { type: 'checkbox', name: 'switch-' + field + '-' + i, value: value, checked: true });
                    var counter = $('<span/>',  { class: 'report-counter' });
                    input.appendTo(outer);
                    $('<label/>', { for: 'switch-' + field + '-' + i, text: value + ' '}).append(counter).appendTo(outer);
                    outer.appendTo(filters[field].container);
                    filters[field].values[value] = {
                        checked:    true,
                        counter:    0,
                        container:  counter,
                        input:      input,
                        outer:      outer
                    };
                }
                i++;
            });
            if (Object.keys(filters[field].values).length == 0) {
                filters[field].container.closest('.search-facet__item').remove();
            }
        }
        $('.js-report-switch').data("filters", filters).data("containers", item_containers).trigger('change');
    }

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
