<?php

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Enums\TypeOffreEnum;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Inc\Theme;

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
    public function loadEvents(): array
    {
        $response = $this->pivotClient->fetchOffersByCriteria();

        $data = [];
        foreach ($response->getOffers() as $offer) {
            if ($offer->typeOffre->idTypeOffre == TypeOffreEnum::EVENT->value) {
                $data[] = $offer;
            }
        }
        array_map(fn(Offer $offer) => $offer->url = RouterPivot::getOfferUrl(Theme::CATEGORY_PATRIMOINES,$offer->codeCgt), $data);

        return $data;
    }

    public function loadOffer(string $codeCgt): ?Offer
    {
        return $this->pivotClient->loadOffer($codeCgt);
    }
}
