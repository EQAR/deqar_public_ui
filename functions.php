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

			parent::__construct();
		}

		function context($context){
			$context['menu'] = new TimberMenu();
			$context['site'] = $this;
			return $context;
		}

		function twig($twig){
			$twig->addExtension(new Twig_Extension_StringLoader());
			return $twig;
		}

	}

	new Site();

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