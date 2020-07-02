<?php

	if (!class_exists('Timber')){

		add_action('admin_enqueue_scripts', function(){
			wp_enqueue_style('admin', get_stylesheet_directory_uri() . '/css/admin.css');
		});

		add_action( 'admin_notices', function(){
			$notice = 	'<div class="error slik-notice"><h1>Import plugins</h1>'.
						'<p>The WordPress Starter Kit is not complete without all '.
						'it\'s powerful plugins. Run the following commands on your '.
						'server:</p><p><pre>sudo mv '.dirname(__FILE__).'/plugins/* '.
						dirname(__FILE__).'/../../plugins/; sudo rm -Rf '.
						dirname(__FILE__).'/plugins/;</pre></p></div>';
			echo $notice;
		});
		return;
	}

	class Site extends TimberSite {

		function __construct(){

			add_theme_support('post-formats');
			add_theme_support('post-thumbnails');
			add_theme_support('menus');

			add_filter('timber_context', [$this, 'context']);
			add_filter('get_twig', [$this, 'twig']);
			add_filter('login_headerurl', [$this, 'login_home_url']);

			add_action('init', [$this, 'acf']);
			add_action('login_enqueue_scripts', [$this, 'login_stylesheet']);

			parent::__construct();
		}

		function context($context){
			$context['menu'] = new TimberMenu('Menu');
			$context['footer_menu'] = new TimberMenu('Footer');
			$context['internal_area'] = new TimberMenu('Internal Area');
			$context['site'] = $this;

			$context['footer'] = [
				'title' => get_field('footer_title', 'option'),
				'text' => get_field('footer_text', 'option'),
				'navigation_title' => get_field('footer_navigation_title', 'option'),
			];

			$context['eu_footer'] = [
				'text' => get_field('eu_footer_text', 'option'),
				'pages' => get_field('eu_footer_pages', 'option'),
			];

			$context['contact'] = [
				'telephone' => get_field('contact_telephone', 'option'),
				'email' => get_field('contact_email', 'option'),
			];

			$context['social'] = [
				'facebook' => get_field('social_facebook', 'option'),
				'twitter' => get_field('social_twitter', 'option'),
				'linkedin' => get_field('social_linkedin', 'option'),
			];

			$context['newsletter'] = [
				'title' => get_field('newsletter_title', 'option'),
				'description' => get_field('newsletter_description', 'option'),
				'form' => get_field('newsletter_form', 'option'),
			];

			$context['agency'] = [
				'title' => get_field('agencies_title', 'option'),
				'list' => get_field('agencies_list', 'option'),
				'map' => get_field('agencies_map', 'option'),
			];

			$context['search'] = [
				'title' => get_field('search_title', 'option'),
				'description' => get_field('search_description', 'option'),
				'button' => get_field('search_button', 'option'),
				'preview' => get_field('search_preview', 'option'),
				'default_target' => get_field('search_default_target', 'option'),
                'facets' => get_field('search_facets', 'option'),
                'ordering' => get_field('search_ordering', 'option'),
			];

			$context['report_search'] = [
                'title' => get_field('report_search_title', 'option'),
                'description' => get_field('report_search_description', 'option'),
                'button' => get_field('report_search_button', 'option'),
                'default_target' => get_field('report_search_default_target', 'option'),
                'facets' => get_field('report_search_facets', 'option'),
                'ordering' => get_field('report_search_ordering', 'option'),
			];

            $context['permalinks'] = [
                'report' =>         get_field('permalink_report', 'option'),
                'institution' =>    get_field('permalink_institution', 'option'),
                'agency' =>         get_field('permalink_agency', 'option'),
                'country' =>        get_field('permalink_country', 'option'),
            ];

			$context['cta'] = [
				'qa' => [
					'form' => get_field('qa_cta_form', 'option'),
				],
				'agency' => [
					'title' => get_field('agency_cta_title', 'option'),
					'description' => get_field('agency_cta_description', 'option'),
					'link' => get_field('agency_cta_link', 'option'),
				]
            ];

            $context['login'] = [
                'internal_area' => get_field('internal_area', 'option'),
			];

			$context['cookie'] = [
                'message' => get_field('cookie_message', 'option'),
            ];

			return $context;
		}

		function twig($twig){
			$twig->addExtension(new Twig_Extension_StringLoader());
            $twig->addFilter(new Twig_SimpleFilter('hasHistorical', [$this,'hasHistorical'] ));
            $twig->addFilter(new Twig_SimpleFilter('countHistorical', [$this,'countHistorical'] ));
            $twig->addFilter(new Twig_SimpleFilter('isChildOf', [$this,'isChildOf'] ));
            $twig->addFilter(new Twig_SimpleFilter('visibleChildren', [$this,'visibleChildren'] ));
            $twig->addFilter(new Twig_SimpleFilter('getSections', [$this,'getSections'] ));
            $twig->addFilter(new Twig_SimpleFilter('addParameters', [$this,'addParameters'] ));
            $twig->addFilter(new Twig_SimpleFilter('relatedTo', [$this,'relatedTo'] ));
			return $twig;
		}



        /**
         * Get the number of invalid / historical reports from an institution
         * @param  array    $reports      Reports
         * @return int                    Returns the numver of invalid / historical reports
         */
        public function countHistorical( $reports = null ) {

            $count = 0;

            // Check needed for inconsistent EQAR API results
            // Programme reports are contained in another 'report' object, institutional reports are not.
            foreach ($reports as $report) {
                if ( isset($report->report) ) {
                    if ( $report->report->report_valid == false ) {
                        $count++;
                    }
                } else {
                    if ( $report->report_valid == false ) {
                        $count++;
                    }
                }
            }

            return $count;

        }




        /**
         * Check if a list of institutional or programme reports includes historical reports
         * @param  array    $reports      Reports
         * @return boolean                Returns true if the list includes one or more historical reports.
         */
        public function hasHistorical( $reports = null ) {

            $hasHistoric = false;

            // Check needed for inconsistent EQAR API results
            // Programme reports are contained in another 'report' object, institutional reports are not.
            foreach ($reports as $report) {
                if ( isset($report->report) ) {
                    if ( $report->report->report_valid == false ) {
                        $hasHistoric = true;
                    }
                } else {
                    if ( $report->report_valid == false ) {
                        $hasHistoric = true;
                    }
                }
            }

            return $hasHistoric;

        }

        /**
         * Check whether Post object is given ID, or a child thereof
         * @param  TimberPost    $post    Post object
         *         int           $id      ID to check for
         * @return bool                   Result
         */
        public function isChildOf( $post, $id ) {
            if ($post->id == $id) {
                return(true);
            } elseif ($post->parent) {
                return($this->isChildOf($post->parent, $id));
            } else {
                return(false);
            }
        }

        /**
         * Return only visible child pages, incl. current page even if hidden
         * @param  TimberPost    $page    Page object
         * @return array/bool             Result (false if none)
         */
        public function visibleChildren( $page, $include_id = null ) {
            $children = array();
            foreach ($page->children('page') as $subpage) {
                if ( ($subpage->get_field('hide_page') != 'true') || ( isset($include_id) && $subpage->id == $include_id) ) {
                    $children[] = $subpage;
                }
            }
            if (count($children) > 0) {
                return($children);
            } else {
                return(false);
            }
        }

        /**
         * Return page section titles and slugs
         * @param  TimberPost    $page    Page object
         * @return array/bool             Result (false if none)
         */
        public function getSections( $page ) {
            $sections = array();
            foreach ($page->get_field('blocks') as $block) {
                if ($block['acf_fc_layout'] == 'text-fields') {
                    foreach ($block['text_fields'] as $subblock) {
                        if ($subblock['acf_fc_layout'] == 'subtitle') {
                            $sections[] = array('link' => get_the_permalink($page) . '#' . sanitize_title($subblock['subtitle']), 'text' => $subblock['subtitle']);
                        }
                    }
                } elseif ( ($block['acf_fc_layout'] == 'accordion' || $block['acf_fc_layout'] == 'image-accordion') && !preg_match('/^\s*$/', $block['title']) ) {
                    $sections[] = array('link' => get_the_permalink($page) . '#' . sanitize_title($block['title']), 'text' => $block['title']);
                }
            }
            if (count($sections) > 0) {
                return($sections);
            } else {
                return(false);
            }
        }

        /**
         * Return URL with additional parameters added or removed
         * @param  string       $base_url   Base URL
         * @param  array        $formdata   Existing parameter array
         * @param  array        $override   Parameters to add/change/remove
         * @param  array        $masks      Parameters to mask
         * @return string                   URL with parameters
         */
        public function addParameters( $base_url, $formdata, $override, $masks = array()) {
            $url = $base_url . '?';
            if (is_array($formdata)) {
                foreach ($formdata as $key => $value) {
                    if ( (! in_array($key, array_keys($override))) && (! in_array($key, $masks)) ) {
                        $url = $url . urlencode($key) . '=' . urlencode($value) . '&';
                    }
                }
            }
            foreach ($override as $key => $value) {
                if ($value !== null && (! in_array($key, $masks)) ) {
                    $url = $url . urlencode($key) . '=' . urlencode($value) . '&';
                }
            }
            if (substr($url, -1) == '&') {
                $url = substr($url,0,-1);
            }
            return($url);
        }

        /**
         * Check if institution has hierarchical or historical relationship with another
         * @param  array        $institution    Institution object (from API)
         * @param  array        $query_id       Institution ID to check
         * @return boolean
         */
        public function relatedTo( $institution, $query_id ) {
            if (isset($institution)) {
                $result = false;
                if (isset($institution->hierarchical_relationships)) {
                    foreach ($institution->hierarchical_relationships->includes as $rel) {
                        if ($rel->institution->id == $query_id) {
                            $result = true;
                        }
                    }
                    foreach ($institution->hierarchical_relationships->part_of as $rel) {
                        if ($rel->institution->id == $query_id) {
                            $result = true;
                        }
                    }
                }
                if (isset($institution->historical_relationships)) {
                    foreach ($institution->historical_relationships as $rel) {
                        if ($rel->institution->id == $query_id) {
                            $result = true;
                        }
                    }
                }
                return($result);
            } else {
                return(false);
            }
        }

		function acf(){
			if( function_exists('acf_add_options_page') ) {

				acf_add_options_page([
					'page_title' 	=> 'EQAR',
					'menu_title'	=> 'EQAR',
					'menu_slug' 	=> 'theme-general-settings',
					'capability'	=> 'edit_posts',
					'redirect'		=> false
				]);

				acf_add_options_sub_page([
					'page_title' 	=> 'Agencies',
					'menu_title' 	=> 'Agencies',
					'parent_slug'	=> 'theme-general-settings',
				]);

				acf_add_options_sub_page([
					'page_title' 	=> 'Search',
					'menu_title' 	=> 'Search',
					'parent_slug'	=> 'theme-general-settings',
				]);

				acf_add_options_sub_page([
					'page_title' 	=> 'Call to Actions',
					'menu_title' 	=> 'Call to Actions',
					'parent_slug'	=> 'theme-general-settings',
				]);

				acf_add_options_sub_page([
					'page_title' 	=> 'Maps',
					'menu_title' 	=> 'Maps',
					'parent_slug'	=> 'theme-general-settings',
				]);
			}
		}

		function login_stylesheet(){
			wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/public/css/login.css' );
		}

		function login_home_url () {
			return home_url();
		}


        public static function do404( $http_status = 404, $error_message = null ) {
            status_header( $http_status );
            nocache_headers();
            $context = Timber::get_context();
            $context['http_status'] = $http_status;
            if (isset($error_message)) {
                $context['error_message'] = $error_message;
            }
            Timber::render('404.twig', $context);
            die();
        }

	}

	new Site();

	function breadcrumbs() {
		$breadcrumbs = [];

		// Retrieve current queried object
		$post = get_queried_object();

		// Get the ancestors based on $post
		if ($post->post_type == 'page') {
			$ancestors = array_reverse(get_ancestors($post->ID, $post->post_type));
			foreach($ancestors as $a) {
				$breadcrumbs[] = new TimberPost($a);
			}
		}

		// At last, add the current post
		$breadcrumbs[] = new TimberPost($post->ID);

		return $breadcrumbs;
	}

	// Disable WordPress admin bar for all users.
	show_admin_bar(false);

	// Initiate and move Gravity Forms scripts to footer
	// https://gist.github.com/eriteric/5d6ca5969a662339c4b3

	add_filter( 'gform_init_scripts_footer', '__return_true' );
	add_filter( 'gform_cdata_open', 'wrap_gform_cdata_open', 1 );
	add_filter( 'gform_cdata_close', 'wrap_gform_cdata_close', 99 );

	function wrap_gform_cdata_open( $content = '' ) {
		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || isset( $_POST['gform_ajax'] ) ) {
			return $content;
		}

		$content = 'document.addEventListener( "DOMContentLoaded", function() { ';
		return $content;
	}

	function wrap_gform_cdata_close( $content = '' ) {
		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || isset( $_POST['gform_ajax'] ) ) {
			return $content;
		}

		$content = ' }, false );';
		return $content;
	}

	// Upload EPS files

	function mime_types( $mimes ) {
		$mimes['eps'] = 'application/postscript';
		
		return $mimes;
	}
	
	add_filter( 'upload_mimes', 'mime_types' );
