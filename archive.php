<?php

namespace VisitMarche\TheWo;

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

Twig::rendPage(
    '@Visit/category.html.twig',
    [
        'name' => $categoryName,
        'excerpt' => $category->description,
        'image' => '',
        'video' => null,
        'bgCat' => '',
        'icone' => '',
        'category' => $category,
        'urlBack' => '',
        'children' => $children,
        'filters' => $children,
        'filterSelected' => null,
        'filterType' => null,
        'nameBack' => '',
        'categoryName' => $categoryName,
        'offers' => $offers,
        'bgcat' => '',
        'countArticles' => count($offers),
    ]
);
get_footer();
