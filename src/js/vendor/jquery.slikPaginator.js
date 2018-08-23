/*!
 * Slik jQuery Paginator 1.0
 * (c) 2018 Internetbureau Slik; Jurgen Oldenburg <jurgen@slik.eu>
 * Paginates items in a dom element
 * See readme.md for usage and options
 */
(function($){

    var SlikPaginator = function( element, options ) {

        var elem        = $(element);
        var obj         = this;

        var $container  = $(element);
        var $items      = $container.find( options.items + ".item-visible" );
        var perPage     = options.perPage;


        /**
         * Pick up the options passed to the plugin
         */
        var settings = $.extend({
            param: 'defaultValue'
        }, options || {});


        /**
         * Get all visible items from the container
         * @return integer    The total number of reports
         */
        this.items = function() {
            $items = $container.find( options.items + ".item-visible" );
            console.log( 'items' + $items.length );
            return $items;
            // return $( options.items + ".item-visible" ).length;
        }



        /**
         * Count the total reports in a report container
         * @return integer    The total number of reports
         */
        this.total = function() {
            $count = this.items();
            console.log( 'total: ' + $count.length );
            return this.items().lenght;
        }


        /**
         * Calc the number of pages to paginate
         * @return integer    The total number of reports
         */
        this.pages = function() {
            return (this.total() / perPage);
        }


        /**
         * Show items x to x in the container
         * @param  integer start    Show items from x
         * @param  integer end      Show items to x
         * @return array            The items to show
         */
        this.show = function( start, end ) {
            $items.slice( start, end ).show();
        }


        /**
         * Hide all items in the $container
         * @return na
         */
        this.hideAll = function() {
            $items.hide();
        }


        /**
         * Plot the pagination items
         * @return mixed    Write html to the paging container
         */
        this.plot = function( $paginator ) {

            /** Get paginator container */
            // var $pagingContainer = $( options.paginator );

            console.log( this.pages() );

            /** Get the number of pages to paginate */
            var pages = new Array( this.pages() );

            /** Empty / reset the pagination container */
            $paginator.html('');

            /** Build the pagination container links */
            $.each(pages, function(index) {

                /** Create li item */
                var $item = $('<li></li>', {
                    'class'     : 'pagination__item',
                });

                /** Create and append a link to the li item */
                var $link = $('<a></a>', {
                    'text'      : (index + 1),
                    'href'      : '#',
                    'class'     : 'pagination__link',
                    'data-item' : (index + 1)
                });

                /** Register click action on pagination item */
                $link.on('click', function(event) {

                    event.preventDefault();

                    var current     = $(this).data('item');
                    var pageItems   = (current * perPage);
                    var start       = (pageItems - perPage);
                    var end         = (pageItems);

                    obj.hideAll();
                    obj.show( start, end );

                    return false;

                });

                $item.append($link);
                $paginator.append($item);

            })

        }


        /**
         * Reset pagination (if number of items changes)
         */
        this.reset = function() {

            this.$items = $container.find( options.items );

            obj.hideAll();
            obj.show( 0, perPage );

            obj.plot( $( options.paginator ) );

        }


        /**
         * Reset the whole pagination (if number of items changes)
         */
        this.init = function() {

            this.$items = $container.find( options.items );

            obj.hideAll();
            obj.show( 0, perPage );

            obj.plot( $( options.paginator ) );

        }

        // Initiate the paginator
        this.init();

    };



    $.fn.slikPaginator = function( options ) {

        var element = $(this);

        // Return early if this element already has a plugin instance
        if (element.data('slikPaginator')) return element.data('slikPaginator');

        // pass options to plugin constructor
        var slikPaginator = new SlikPaginator( this, options );

        // Store plugin object in this element's data
        element.data('slikPaginator', slikPaginator);

        return slikPaginator;

    };

})(jQuery);
