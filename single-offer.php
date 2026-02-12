<?php

namespace VisitMarche\ThemeWp;

use AcMarche\PivotAi\Enums\ContentLevel;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$codeCgt = get_query_var(RouterPivot::PARAM_OFFRE);

if (!str_contains($codeCgt, "-")) {
    Twig::rend404Page();
    get_footer();

    return;
}

$pivotRepository = new PivotRepository();

try {
    $offer = $pivotRepository->loadOffer($codeCgt, ContentLevel::Full);
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

$events = $pivotRepository->loadEvents(skip: true);
$latitude = $offer->latitude();
$longitude = $offer->longitude();
if ($latitude && $longitude) {
    //AssetsLoader::enqueueLeaflet();
}

if (!$currentCategory = get_category_by_slug(get_query_var('category_name'))) {
    $currentCategory = get_category(1);
}
$returnUrl = get_category_link($currentCategory);

Twig::rendPage(
    '@Visit/offer.html.twig',
    [
        'offer' => $offer,
        'name' => $offer->name(),
        'returnName' => $currentCategory->name,
        'returnUrl' => $returnUrl,
        'categoryName' => $currentCategory->name,
        'nameBack' => $currentCategory->name,
        'image' => $offer->getDefaultImage()->url ?? get_template_directory_uri().'/assets/images/404.jpg',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'excerpt' => null,
        'tags' => [],
        'icon' => null,
        'events' => $events,
        'specs' => [],
        'gpx' => null,
        'locations' => [],
    ]
);
get_footer();
