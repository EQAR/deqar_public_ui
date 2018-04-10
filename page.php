<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

require_once( get_template_directory() . '/classes/EqarApi.class.php');

$eqarApi = new EqarApi();

$context = Timber::get_context();
$context['post'] = new TimberPost();
$context['institutions']            = $eqarApi->getInstitutions();
$context['countries']               = $eqarApi->getCountries();
$context['agencies']                = $eqarApi->getAgencies();

$template = 'page.twig';

if(is_front_page()) {

    $context['page_for_posts'] = get_option('page_for_posts');
    $context['posts'] = Timber::get_posts([
        'posts_per_page' => 3,
    ]);

    $template = 'frontpage.twig';
}

Timber::render($template, $context);
