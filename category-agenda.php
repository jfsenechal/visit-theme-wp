<?php

namespace VisitMarche\TheWo;

use VisitMarche\ThemeWp\Dto\CommonItem;
use VisitMarche\ThemeWp\Lib\LocaleHelper;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$cat_ID = get_queried_object_id();
$category = get_category($cat_ID);

$pivotRepository = new PivotRepository();
$events = $pivotRepository->loadEvents();

$locale = LocaleHelper::getSelectedLanguage();
$image = get_template_directory_uri().'/assets/tartine/bg_events.png';
$filters = [];
foreach ($events as $event) {
    foreach ($event->eventCategories as $urn => $specification) {
        if (!isset($filters[$urn])) {
            $filters[$urn] = [
                'urn' => $urn,
                'name' => $specification->getLabelByLang($locale) ?? $urn,
            ];
        }
    }
}
$filters = array_values($filters);

$eventsData = [];
foreach ($events as $event) {
    $eventsData[] = CommonItem::createFromOffer($event);
}

Twig::renderPage(
    '@Visit/agenda.html.twig',
    [
        'events' => $events,
        'eventsJson' => json_encode($eventsData, JSON_THROW_ON_ERROR),
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
