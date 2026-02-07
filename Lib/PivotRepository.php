<?php

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Enums\TypeOffreEnum;

class PivotRepository
{
    /**
     * @return array<int, Offer>
     */
    public function loadEvents(): array
    {
        $di = new Di();
        $pivotClient = $di->getPivotClient();
        $response = $pivotClient->fetchOffersByCriteria();

        $data = [];
        foreach ($response->getOffers() as $offer) {
            if ($offer->typeOffre->idTypeOffre == TypeOffreEnum::EVENT->value) {
                $data[] = $offer;
            }
        }

        return $data;
    }
}
