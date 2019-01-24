<?php
/**
 * Template Name: DEQAR news
 * Description: show news items from the DEQAR category only
 *
 */

$query = array(
        'post_type' => 'post',
        'posts_per_page' => 100,
        'category_name' => 'deqar'
    );

$context = Timber::get_context();
$context['post']         = new TimberPost();
$context['posts']        = Timber::get_posts($query);

Timber::render('deqar-news.twig', $context);

