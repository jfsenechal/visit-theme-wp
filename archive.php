<?php

namespace VisitMarche\TheWo;

use VisitMarche\ThemeWp\Inc\CategoryMetaData;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\WpRepository;

get_header();

$cat_ID = get_queried_object_id();
$category = get_category($cat_ID);
$categoryName = single_cat_title('', false);

$returnName = null;
if ($category->parent > 0) {
    $returnName = get_category($category->parent)->name;
}

$wpRepository = new WpRepository();
$children = $wpRepository->getChildrenOfCategory($cat_ID);

try {
    $offers = $wpRepository->findArticlesAndOffersByWpCategory($category->cat_ID, true);
} catch (\Exception $e) {
    $offers = [];
}

RouterPivot::setLinkOnCommonItems($offers, $category->cat_ID, 'fr');

try {
    $offersJson = json_encode(array_map(fn($item) => $item->toArray(), $offers), JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    $offersJson = null;
}

$image = CategoryMetaData::getImage($category);
$video = CategoryMetaData::getVideo($category);
$icon = CategoryMetaData::getIcon($category);
$color = CategoryMetaData::getColor($category);

Twig::rendPage(
    '@Visit/category.html.twig',
    [
        'name' => $categoryName,
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
