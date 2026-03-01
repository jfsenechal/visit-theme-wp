<?php

namespace VisitMarche\ThemeWp\Lib;

use VisitMarche\ThemeWp\Dto\CommonItem;
use VisitMarche\ThemeWp\Enums\IconeEnum;
use VisitMarche\ThemeWp\Enums\LanguageEnum;
use VisitMarche\ThemeWp\Repository\WpRepository;
use WP_Term;

class Menu
{
    /**
     * @return WP_Term[]
     */
    public function getIcons(string $locale): array
    {
        $icons = [
            'arts' => get_category_by_slug('arts'),
            'balades' => get_category_by_slug('balades'),
            'fetes' => get_category_by_slug('fetes'),
            'gourmandises' => get_category_by_slug('gourmandises'),
            'patrimoine' => get_category_by_slug('patrimoine'),
        ];

        foreach ($icons as $key => $icon) {
            if ($icon instanceof WP_Term) {
                $icon->url = get_category_link($icon);
                $icon->colorHover = $this->hoverColor($key);
                $icon->imageWhite = IconeEnum::iconeWhite($icon->slug);
            }
        }

        if ($locale !== 'fr' && ($language = LanguageEnum::tryFrom($locale))) {
            $translator = OpenAi::create();
            foreach ($icons as $data) {
                $data->name = $translator->translate($data->name, $language);
            }
        }

        return $icons;
    }

    private function hoverColor(string $key): string
    {
        return match ($key) {
            'arts' => 'hover:bg-art',
            'balades' => 'hover:bg-walk',
            'fetes' => 'hover:bg-party',
            'gourmandises' => 'hover:bg-delicacy',
            default => 'hover:bg-patrimony',
        };
    }

    public function getMenuTop(string $locale): array
    {
        $wpRepository = new WpRepository();

        $menu = [
            0 => ['parent' => get_category_by_slug('idees-sejours'), 'children' => []],
            1 => ['parent' => get_category_by_slug('inspirations'), 'children' => []],
            2 => ['parent' => get_category_by_slug('agenda'), 'children' => []],
        ];

        $organiser = get_category_by_slug('sorganiser');
        if ($organiser) {
            $menu[3] = [
                'parent' => $organiser,
                'children' => $wpRepository->getChildrenOfCategory($organiser->term_id),
            ];
        }
        $decouvrir = get_category_by_slug('decouvrir');
        //todo add produits locaux carte
        if ($decouvrir) {
            $menu[4] = [
                'parent' => $decouvrir,
                'children' => $wpRepository->getChildrenOfCategory($decouvrir->term_id),
            ];
        }

        $menu[] = [
            'parent' => get_category_by_slug('pratique'),
            'children' => [],
        ];

        $items = [];
        foreach ($menu as $data) {
            if ($data['parent'] instanceof WP_Term) {
                $row = ['parent' => CommonItem::createFromCategory($data['parent']), 'children' => []];
                foreach ($data['children'] as $child) {
                    if ($child instanceof WP_Term) {
                        $row['children'][] = CommonItem::createFromCategory($child);
                    }
                }
                $items[] = $row;
            }
        }

        if ($locale !== 'fr' && ($language = LanguageEnum::tryFrom($locale))) {
            $translator = OpenAi::create();
            foreach ($items as $data) {
                $data['parent']->name = $translator->translate($data['parent']->name, $language);
                foreach ($data['children'] as $child) {
                    $child->name = $translator->translate($child->name, $language);
                }
            }
        }

        return $items;
    }
}
