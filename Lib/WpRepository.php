<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Api\PivotClient;

class WpRepository
{
    public const PIVOT_REFOFFERS = 'pivot_ref_offers';

    /**
     * @return string[]
     */
    public static function getMetaPivotCodesCgtOffres(int $categoryId): array
    {
        $offers = get_term_meta($categoryId, self::PIVOT_REFOFFERS, true);
        if (!is_array($offers)) {
            return [];
        }

        return $offers;
    }

    /**
     * @return \stdClass[]
     */
    public function getAllOffersShorts(): array
    {
        $pivotClient = Di::getInstance()->get(PivotClient::class);
        $offerResponse = $pivotClient->fetchOffersByCriteria(PivotClient::CONTENT_LEVEL_MINIMAL);

        $offres = [];
        foreach ($offerResponse->getOffers() as $offer) {
            $std = new \stdClass();
            $std->codeCgt = $offer->codeCgt;
            $std->name = $offer->nom;
            $std->type = ($offer->typeOffre && $offer->typeOffre->label) ? ($offer->typeOffre->label[0]->value ?? '') : '';
            $offres[] = $std;
        }

        usort($offres, fn(\stdClass $a, \stdClass $b) => strcasecmp($a->name ?? '', $b->name ?? ''));

        return $offres;
    }

    /**
     * @return \stdClass[]
     */
    public function findShortsByNameOrCode(string $search): array
    {
        $offres = array_filter($this->getAllOffersShorts(), function (\stdClass $offre) use ($search) {
            return preg_match('#' . preg_quote($search, '#') . '#i', $offre->name ?? '')
                || preg_match('#' . preg_quote($search, '#') . '#i', $offre->codeCgt ?? '');
        });

        return array_values($offres);
    }

    /**
     * @param string[] $codesCgt
     * @return \stdClass[]
     */
    public function findOffersShortByCodesCgt(array $codesCgt): array
    {
        $allOffers = $this->getAllOffersShorts();
        $offers = [];
        foreach ($codesCgt as $codeCgt) {
            foreach ($allOffers as $offerShort) {
                if ($offerShort->codeCgt === $codeCgt) {
                    $offers[] = $offerShort;
                    break;
                }
            }
        }

        return $offers;
    }
}
