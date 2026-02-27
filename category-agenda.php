<?php

namespace VisitMarche\TheWo;

use VisitMarche\ThemeWp\Inc\CategoryMetaData;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$cat_ID = get_queried_object_id();
$category = get_category($cat_ID);

$pivotRepository = new PivotRepository();
$events = $pivotRepository->loadEvents();

$image =  get_template_directory_uri() . '/assets/tartine/bg_events.png';
$filters = [];

Twig::renderPage(
    '@Visit/agenda.html.twig',
    [
        'events' => $events,
        'category' => $category,
        'name' => $category->name,
        'nameBack' => '',
        'categoryName' => $category->name,
        'image' => $image,
        'filters' => $filters,
        'icon' => null,
    ]
);
get_footer();
