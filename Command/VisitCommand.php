<?php

namespace VisitMarche\ThemeWp\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VisitMarche\ThemeWp\Lib\Search\DataForSearch;
use VisitMarche\ThemeWp\Lib\Search\MeiliServer;

#[AsCommand(
    name: 'visit:do',
    description: ' ',
)]
class VisitCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Do something');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        return Command::SUCCESS;
    }
}
