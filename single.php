<?php

namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$post = get_post();

$image = null;
$image_srcset = null;
$image_sizes = null;

if (has_post_thumbnail()) {
    $attachment_id = get_post_thumbnail_id();
    $image = wp_get_attachment_image_url($attachment_id, 'hero-header');
    $image_srcset = wp_get_attachment_image_srcset($attachment_id, 'hero-header');
    $image_sizes = wp_get_attachment_image_sizes($attachment_id, 'hero-header');
}

$tags = [];
foreach (get_the_category($post->ID) as $category) {
    $tags[] = ['id' => $category->term_id, 'name' => $category->name, 'url' => get_category_link($category->term_id)];
}

$content = get_the_content(null, null, $post);
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]&gt;', $content);

$pivotRepository = new PivotRepository();
$events = $pivotRepository->loadEvents(skip: true);
$events = array_slice($events, 0, 3);

if (!$currentCategory = get_category_by_slug(get_query_var('category_name'))) {
    $currentCategory = get_category(1);
}
$returnUrl = get_category_link($currentCategory);

Twig::renderPage(
    '@Visit/article.html.twig',
    [
        'post' => $post,
        'name' => $post->post_title,
        'content' => $content,
        'returnName' => $currentCategory->name,
        'returnUrl' => $returnUrl,
        'categoryName' => $currentCategory->name,
        'nameBack' => $currentCategory->name,
        'image' => $image,
        'thumbnail' => $image,
        'thumbnail_srcset' => $image_srcset,
        'thumbnail_sizes' => $image_sizes,
        'excerpt' => $post->post_excerpt,
        'tags' => $tags,
        'events' => $events,
    ]
);
get_footer();
