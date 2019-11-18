<?php
/**
 * Template Name: Custom news | ex DEQAR news
 * Description: show news items from the DEQAR category only
 *
 */

$context = Timber::get_context();
$context['post']         = new TimberPost();

$query = array(
        'post_type' => 'post',
        'posts_per_page' => 100,
        'category__in' => $context['post']->get_field('category')
    );

$context['posts']        = Timber::get_posts($query);

Timber::render('deqar-news.twig', $context);

