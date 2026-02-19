<?php

namespace VisitMarche\ThemeWp\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VisitMarche\ThemeWp\Inc\Theme;
use VisitMarche\ThemeWp\Repository\PivotRepository;
use VisitMarche\ThemeWp\Repository\WpRepository;

// Set up server variables for WordPress multisite CLI context
$_SERVER['HTTP_HOST'] = $_ENV['WP_URL_HOME'];

// Bootstrap WordPress to make WordPress functions available
define('WP_USE_THEMES', false);
require __DIR__.'/../../../../wp-load.php';

#[AsCommand(
    name: 'visit:do',
    description: ' ',
)]
class VisitCommand extends Command
{
    private PivotRepository $pivotRepository;
    private SymfonyStyle $io;

    protected function configure(): void
    {
        $this->setDescription('Do something');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->restaurants();

        return Command::SUCCESS;
    }

    private function restaurants(): void
    {
        $this->pivotRepository = new PivotRepository();
        $restaurants = $this->pivotRepository->loadRestaurants();
        $parentCategoryId = Theme::CATEGORIES_RESTAURATION;

        $urns = [
            'urn:fld:cat:resto',
            'urn:fld:cat:restorap',
            'urn:fld:cat:brass',
            'urn:fld:cat:bar:vin',
            'urn:fld:cat:saldegust',
            'urn:fld:cat:trait',
            'urn:fld:cat:foodtruck',
            'urn:fld:cat:bar',
        ];

        // Step 1: Find the label for each URN from the offers data
        $urnLabels = [];
        foreach ($restaurants as $offer) {
            foreach ($offer->classificationLabels as $label) {
                if (in_array($label->urn, $urns, true) && !isset($urnLabels[$label->urn])) {
                    $urnLabels[$label->urn] = $label->label;
                }
            }
        }

        // Step 2: Create WP categories and collect URN → category ID mapping
        $urnToCategoryId = [];
        foreach ($urns as $urn) {
            if (!isset($urnLabels[$urn])) {
                $this->io->warning(sprintf('No label found for URN: %s', $urn));
                continue;
            }

            $categoryName = $urnLabels[$urn];
            $existingTerm = term_exists($categoryName, 'category');

            if ($existingTerm) {
                $categoryId = (int) $existingTerm['term_id'];
                $this->io->text(sprintf('Category "%s" already exists (ID %d)', $categoryName, $categoryId));
            } else {
                $result = wp_insert_term($categoryName, 'category', [
                    'parent' => $parentCategoryId,
                ]);

                if (is_wp_error($result)) {
                    $this->io->error(sprintf('Failed to create category "%s": %s', $categoryName, $result->get_error_message()));
                    continue;
                }

                $categoryId = (int) $result['term_id'];
                $this->io->success(sprintf('Created category "%s" (ID %d)', $categoryName, $categoryId));
            }

            $urnToCategoryId[$urn] = $categoryId;
        }

        // Step 3: For each category URN, find matching offers and store their codeCgt
        foreach ($urnToCategoryId as $urn => $categoryId) {
            $codesCgt = [];
            foreach ($restaurants as $offer) {
                foreach ($offer->classificationLabels as $label) {
                    if ($label->urn === $urn) {
                        $codesCgt[] = $offer->codeCgt;
                        break;
                    }
                }
            }

            update_term_meta($categoryId, WpRepository::PIVOT_REFOFFERS, $codesCgt);
            $this->io->text(sprintf('  URN %s → %d offers linked to category %d', $urn, count($codesCgt), $categoryId));
        }
    }

    private function specculi($label): array
    {
        if (strpos($label->urn, 'specculi')) {
            $labels[$label->urn] = [$label->label, $label->urn];
        }

        return [];
    }

    private function index(): void
    {
        $pivotRepository = new PivotRepository();
        $events = $pivotRepository->loadRestaurants();
        $labels = [];
        foreach ($events as $event) {
            $this->io->title($event->name());
            foreach ($event->classificationLabels as $label) {
                $this->io->text($label);
                if (!strpos($label->urn, 'specculi')) {
                    $labels[$label->urn] = [$label->label, $label->urn];
                }
            }
            $this->io->newLine(2);
        }

        $this->io->table(
            ['Label', 'Urn'],
            $labels,
        );
    }
}
