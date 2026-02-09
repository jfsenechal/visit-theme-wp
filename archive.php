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
    $offres = $wpRepository->findArticlesAndOffersByWpCategory($category->cat_ID, $filterSelected, $filterSelectedType);
} catch (\Exception $e) {
    $offres = [];
}

Twig::rendPage(
    '@Visit/category.html.twig',
    [
        'name' => $categoryName,
        'excerpt' => $category->description,
        'image' => $image,
        'video' => $video,
        'bgCat' => $bgcat,
        'icone' => $icone,
        'category' => $category,
        'urlBack' => $urlBack,
        'children' => $children,
        'filters' => $filtres,
        'filterSelected' => $filterSelected,
        'filterType' => $filterSelectedType,
        'nameBack' => $nameBack,
        'categoryName' => $categoryName,
        'offres' => $offres,
        'bgcat' => $bgcat,
        'countArticles' => count($offres),
    ]
);
get_footer();
