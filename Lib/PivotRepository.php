<?php

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Enums\TypeOffreEnum;

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

        return $data;
    }
}
