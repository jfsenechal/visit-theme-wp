<?php

namespace VisitMarche\ThemeWp;

use AcMarche\PivotAi\Enums\ContentLevel;
use VisitMarche\ThemeWp\Enums\LanguageEnum;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Lib\LocaleHelper;
use VisitMarche\ThemeWp\Lib\OpenAi;
use VisitMarche\ThemeWp\Lib\Twig;
use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$codeCgt = get_query_var(RouterPivot::PARAM_OFFRE);

if (!str_contains($codeCgt, "-")) {
    Twig::renderNotFoundPage('Offre introuvable.');
    get_footer();

    return;
}

$pivotRepository = new PivotRepository();

try {
    $offer = $pivotRepository->loadOffer($codeCgt, ContentLevel::Full);
} catch (\Exception $e) {
    Twig::renderErrorPage($e);
    get_footer();

    return;
}

if (!$offer) {
    Twig::renderNotFoundPage('Offre introuvable.');
    get_footer();

    return;
}

$locale = LocaleHelper::getSelectedLanguage();
$name = $offer->name();
$description = $offer->getDescription();

if ($locale !== 'fr' && ($language = LanguageEnum::tryFrom($locale))) {
    $translator = OpenAi::create();
    $name = $translator->translate($name, $language);
    if ($description) {
        $description = $translator->translate($description, $language);
    }
}

$events = $pivotRepository->loadEvents(skip: true);
$events = array_slice($events, 0, 3);
$latitude = $offer->latitude();
$longitude = $offer->longitude();
if ($latitude && $longitude) {
    //AssetsLoader::enqueueLeaflet();
}

if (!$currentCategory = get_category_by_slug(get_query_var('category_name'))) {
    $currentCategory = get_category(1);
}
$returnUrl = get_category_link($currentCategory);

Twig::renderPage(
    '@Visit/offer.html.twig',
    [
        'offer' => $offer,
        'name' => $name,
        'description' => $description,
        'returnName' => $currentCategory->name,
        'returnUrl' => $returnUrl,
        'categoryName' => $currentCategory->name,
        'nameBack' => $currentCategory->name,
        'image' => $offer->getDefaultImage()->url ?? get_template_directory_uri().'/assets/images/404.jpg',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'excerpt' => null,
        'tags' => $offer->getClassificationLabels(),
        'icon' => null,
        'events' => $events,
        'specs' => [],
        'locations' => [],
    ]
);
get_footer();
