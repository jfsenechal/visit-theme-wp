<?php

namespace VisitMarche\TheWo;

use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\WpRepository;

get_header();

$cat_ID = get_queried_object_id();
$category = get_category($cat_ID);
$categoryName = single_cat_title('', false);

$wpRepository = new WpRepository();
$children = $wpRepository->getChildrenOfCategory($cat_ID);


try {
    $offers = $wpRepository->findArticlesAndOffersByWpCategory($category->cat_ID);
} catch (\Exception $e) {
    $offers = [];
}

RouterPivot::setLinkOnCommonItems($offers, $category->cat_ID, 'fr');

try {
    $offersJson = json_encode(array_map(fn($item) => [
        'id' => $item->id,
        'type' => $item->type,
        'name' => $item->name,
        'image' => $item->image,
        'description' => strip_tags($item->description),
        'url' => $item->url,
        'tags' => array_map(fn($tag) => ['name' => $tag->name], $item->tags),
    ], $offers), JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    $offersJson = null;
}

Twig::rendPage(
    '@Visit/category.html.twig',
    [
        'name' => $categoryName,
        'excerpt' => $category->description,
        'image' => '',
        'video' => null,
        'bgCat' => '',
        'icon' => '',
        'category' => $category,
        'urlBack' => '',
        'children' => $children,
        'filters' => $children,
        'filterSelected' => null,
        'filterType' => null,
        'nameBack' => '',
        'categoryName' => $categoryName,
        'offersJson' => $offersJson,
        'parentCategoryId' => $category->cat_ID,
        'parentCategoryUrl' => get_category_link($category),
        'bgcat' => '',
        'countArticles' => count($offers),
    ]
);
get_footer();
