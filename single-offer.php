<?php

namespace VisitMarche\ThemeWp;

use AcMarche\PivotAi\Enums\ContentLevel;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$codeCgt = get_query_var(RouterPivot::PARAM_OFFRE);

$wpRepository = new PivotRepository();

if (!str_contains($codeCgt, "-")) {
    Twig::rend404Page();
    get_footer();

    return;
}

try {
    $offer = $wpRepository->loadOffer($codeCgt, ContentLevel::Full);
} catch (\Exception $e) {
    Twig::rend500Page($e->getMessage());
    get_footer();

    return;
}

if (!$offer) {
    Twig::rend404Page();
    get_footer();

    return;
}

//$translator = OpenAi::create();
//$result = $translator->translate($offer->nom, LanguageEnum::ENGLISH);

$latitude = $offer->latitude();
$longitude = $offer->longitude();
if ($latitude && $longitude) {
    //AssetsLoader::enqueueLeaflet();
}

if (!$currentCategory = get_category_by_slug(get_query_var('category_name'))) {
    $currentCategory = get_category_by_slug('non-classifiee');
}
$urlcurrentCategory = get_category_link($currentCategory);

Twig::rendPage(
    '@Visit/offer.html.twig',
    [
        'offer' => $offer,
        'name' => $offer->name(),
        'latitude' => $latitude,
        'longitude' => $longitude,
        'excerpt' => null,
        'tags' => [],
        'image' => $offer->getDefaultImage(),
        'icone' => null,
        'recommandations' => [],
        'urlBack' => $urlcurrentCategory,
        'categoryName' => $currentCategory->name,
        'nameBack' => $currentCategory->name,
        'specs' => [],
        'gpx' => null,
        'locations' => [],
    ]
);
get_footer();
