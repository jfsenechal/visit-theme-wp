<?php

namespace VisitMarche\ThemeWp\Repository;

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Enums\ContentLevel;
use AcMarche\PivotAi\Enums\TypeOffreEnum;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Inc\Theme;
use VisitMarche\ThemeWp\Lib\Di;

readonly class PivotRepository
{
    private PivotClient $pivotClient;

    public function __construct()
    {
        $this->setClient();
    }

    public function setClient(): void
    {
        $this->pivotClient = Di::getInstance()->get(PivotClient::class);
    }

    /**
     * @return array<int, Offer>
     */
    public function loadEvents(
        bool $skip = false
    ): array {
        $response = $this->pivotClient->fetchOffersByCriteria();

        $data = [];
        foreach ($response->getOffers() as $offer) {
            if ($offer->typeOffre->idTypeOffre == TypeOffreEnum::EVENT->value) {
                $data[] = $offer;
            }
        }
        array_map(
            fn(Offer $offer) => $offer->url = RouterPivot::getOfferUrl(Theme::CATEGORY_PATRIMOINES, $offer->codeCgt),
            $data
        );

        return $data;
    }

    public function loadOffer(string $codeCgt, ?ContentLevel $contentLevel = null): ?Offer
    {
        return $this->pivotClient->loadOffer($codeCgt, $contentLevel);
    }

    /**
     * @return \stdClass[]
     */
    public function getAllOffersShorts(): array
    {
        $offerResponse = $this->pivotClient->fetchOffersByCriteria(ContentLevel::Summary);

        $offers = [];
        foreach ($offerResponse->getOffers() as $offer) {
            $std = new \stdClass();
            $std->codeCgt = $offer->codeCgt;
            $std->name = $offer->nom;
            $std->type = ($offer->typeOffre && $offer->typeOffre->label) ? ($offer->typeOffre->label[0]->value ?? '') : '';
            $offers[] = $std;
        }

        usort($offers, fn(\stdClass $a, \stdClass $b) => strcasecmp($a->name ?? '', $b->name ?? ''));

        return $offers;
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

    /**
     * @return \stdClass[]
     */
    public function findShortsByNameOrCode(string $search): array
    {
        $offers = array_filter($this->getAllOffersShorts(), function (\stdClass $offre) use ($search) {
            return preg_match('#'.preg_quote($search, '#').'#i', $offre->name ?? '')
                || preg_match('#'.preg_quote($search, '#').'#i', $offre->codeCgt ?? '');
        });

        return array_values($offers);
    }
}
