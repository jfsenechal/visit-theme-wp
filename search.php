<?php

namespace VisitMarche\ThemeWp;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use VisitMarche\ThemeWp\Lib\Search\MeiliSearch;
use VisitMarche\ThemeWp\Lib\Twig;

get_header();

$searcher = new MeiliSearch();
$searcher->initClientAndIndex();

$keyword = get_search_query();

try {
    $searching = $searcher->doSearch($keyword);
    $hits = $searching->getHits();
    $count = $searching->count();
} catch (\Exception $e) {
    Twig::renderErrorPage($e);

    get_footer();

    return;
}

$twig = Twig::loadTwig();
$thumbnail = get_template_directory_uri().'/assets/images/bg-search.jpeg';
$paths = [];

try {
    echo $twig->render('@Visit/search.html.twig', [
        'hits' => $hits,
        'name' => 'Rechercher',
        'count' => $count,
        'keyword' => $keyword,
        'returnUrl' => null,
        'returnName' => null,
        'image' => $thumbnail,
        'thumbnail' => $thumbnail,
        'thumbnail_srcset' => null,
        'thumbnail_sizes' => null,
        'paths' => $paths,
        'title' => 'Rechercher',
    ]);
} catch (LoaderError|RuntimeError|SyntaxError $e) {
    Twig::renderErrorPage($e);
}

get_footer();
