<?php

namespace VisitMarche\ThemeWp;

use AcMarche\PivotAi\Service\PivotClient;
use VisitMarche\ThemeWp\Lib\Di;
use VisitMarche\ThemeWp\Lib\PivotRepository;

get_header();

$pivotRepository = new PivotRepository(Di::getInstance()->get(PivotClient::class));
$offers = $pivotRepository->loadEvents();
foreach ($offers as $offer) {
    dd($offer);
}


?>
    <div class="container">
        hello
    </div>
    <?php
get_footer();
