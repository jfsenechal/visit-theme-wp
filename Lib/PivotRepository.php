<?php

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Enums\TypeOffreEnum;
use AcMarche\PivotAi\Service\PivotClient;

readonly class PivotRepository
{
    public function __construct(
        private PivotClient $pivotClient,
    ) {}

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

        return $data;
    }
}
