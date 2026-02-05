<?php

namespace VisitMarche\ThemeWp;

use AcMarche\PivotAi\Kernel;
use AcMarche\PivotAi\Service\PivotClient;

get_header();
$kernel = new Kernel($_ENV['APP_ENV'], WP_DEBUG);
$kernel->boot();

$pivotClient = $kernel->getContainer()->get(PivotClient::class);
$response = $pivotClient->fetchOffersByCriteria();

dd($response);


?>
    <div class="container">
        hello
    </div>
    <?php
get_footer();
