<?php

namespace VisitMarche\TheWo;

use VisitMarche\ThemeWp\Dto\CommonItem;
use VisitMarche\ThemeWp\Inc\CategoryMetaData;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$cat_ID = get_queried_object_id();
$category = get_category($cat_ID);

$pivotRepository = new PivotRepository();
$offers = $pivotRepository->loadRestaurants();

$returnName = '';
$filters = [];

$children = [];
$image = CategoryMetaData::getImage($category);
$video = CategoryMetaData::getVideo($category);
$icon = CategoryMetaData::getIcon($category);
$color = CategoryMetaData::getColor($category);

$items = [];

foreach ($offers as $offer) {
    $items[$offer->codeCgt] = CommonItem::createFromOffer($offer);

}

$items = array_values($items);

try {
    $offersJson = json_encode(array_map(fn($item) => $item->toArray(), $items), JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    $offersJson = null;
}

Twig::rendPage(
    '@Visit/category.html.twig',
    [
        'name' => $category->name,
        'image' => $image,
        'returnName' => $returnName,
        'returnUrl' => $returnName,
        'excerpt' => $category->description,
        'icon' => $icon,
        'video' => $video,
        'color' => $color,
        'children' => $children,
        'filters' => $children,
        'offersJson' => $offersJson,
        'parentCategoryId' => $category->cat_ID,
        'parentCategoryUrl' => get_category_link($category),
        'countArticles' => count($offers),
    ]
);
get_footer();
