<?php
/**
 * Template Name: Agenda
 */
namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Inc\Theme;
use VisitMarche\ThemeWp\Lib\PivotRepository;
use VisitMarche\ThemeWp\Lib\Twig;

get_header();

$pivotRepository = new PivotRepository();
$events = $pivotRepository->loadEvents();

$category = get_category(Theme::CATEGORY_PATRIMOINES);
$image = '';
$filtres = [];
dd($events[0]);
Twig::rendPage(
    '@Visit/agenda.html.twig',
    [
        'events' => $events,
        'category' => $category,
        'name' => $category->name,
        'nameBack' => '',
        'categoryName' => $category->name,
        'image' => $image,
        'filters' => $filtres,
        'icone' => null,
    ]
);

get_footer();
