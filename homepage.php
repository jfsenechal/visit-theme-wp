<?php

namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Dto\CommonItem;
use VisitMarche\ThemeWp\Inc\Theme;
use VisitMarche\ThemeWp\Lib\Menu;
use VisitMarche\ThemeWp\Lib\Sort\SortLink;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;
use VisitMarche\ThemeWp\Repository\WpRepository;

get_header();

$wpRepository = new WpRepository();
$pivotRepository = new PivotRepository();
$menu = new Menu();

$intro = $wpRepository->getIntro();
try {
    $ideas = $wpRepository->getIdeas();
} catch (\Exception $exception) {
    $ideas = [];
}

$inspirationCat = get_category(Theme::CATEGORY_INSPIRATION);
$inspirations = [];
foreach ($wpRepository->findArticlesByCategory($inspirationCat->term_id) as $post) {
    $item = CommonItem::createFromPost($post);
    if (!isset($seenIds[$item->id])) {
        $seenIds[$item->id] = true;
        $inspirations[] = $item;
    }
}

$urlAgenda = '/';
$urlInspiration = get_category_link($inspirationCat);

//$events = $pivotRepository->loadEvents(skip: true);
//$events = array_slice($events, 0, 4);
$events = [];
$sortLink = false;
if (current_user_can('edit_post', 2)) {
    $sortLink = SortLink::linkSortArticles(2);
}

$inspirations = array_slice($inspirations, 0, 4);
$icones = $menu->getIcons();

$imgs = [
    '01.jpg',
    '02.jpg',
    '03.jpg',
    '04.jpg',
    '05.jpg',
    '06.jpg',
    '07.jpg',
    '08.jpg',
];
$img = array_rand($imgs);
$bgImg = $imgs[$img];

Twig::rendPage(
    '@Visit/homepage.html.twig',
    [
        'events' => $events,
        'inspirations' => $inspirations,
        'urlAgenda' => $urlAgenda,
        'urlInspiration' => $urlInspiration,
        'intro' => $intro,
        'icons' => $icones,
        'ideas' => $ideas,
        'bgimg' => $bgImg,
        'sortLink' => $sortLink,
    ]
);

get_footer();
