<?php

namespace VisitMarche\ThemeWp\Lib;

use AcMarche\PivotAi\Kernel;
use AcMarche\PivotAi\Service\PivotClient;

class Di
{
    private ?Kernel $kernel = null;

    public function getInstance(): Kernel
    {
        if (!$this->kernel) {
            $this->kernel = new Kernel($_ENV['APP_ENV'], WP_DEBUG);
            $this->kernel->boot();
        }

        return $this->kernel;
    }

    public function getPivotClient(): PivotClient
    {
        $this->getInstance();
        return $this->kernel->getContainer()->get(PivotClient::class);
    }
}
