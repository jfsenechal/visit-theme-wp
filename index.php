<?php

namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Repository\PivotRepository;

get_header();

$pivotRepository = new PivotRepository();
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
