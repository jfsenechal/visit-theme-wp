<?php

namespace VisitMarche\ThemeWp\Repository;

use AcMarche\PivotAi\Api\PivotClient;
use VisitMarche\ThemeWp\Dto\CommonItem;
use VisitMarche\ThemeWp\Lib\Di;
use WP_Query;
use WP_Term;

class WpRepository
{
    public const PIVOT_REFOFFERS = 'pivot_ref_offers';

    /**
     * @return string[]
     */
    public static function getMetaPivotCodesCgtOffers(int $categoryId): array
    {
        $offers = get_term_meta($categoryId, self::PIVOT_REFOFFERS, true);
        if (!is_array($offers)) {
            return [];
        }

        return $offers;
    }

    /**
     * @param int $cat_ID
     * @return WP_Term[]
     */
    public function getChildrenOfCategory(int $cat_ID): array
    {
        $args = [
            'parent' => $cat_ID,//Get direct children only
            'hide_empty' => false,
        ];
        $children = get_categories($args);
        array_map(
            function ($category) {
                $category->url = get_category_link($category->term_id);
                $category->id = $category->term_id;
            },
            $children,
        );

        return $children;
    }

    /**
     * @return CommonItem[]
     */
    public function findArticlesAndOffersByWpCategory(int $cat_ID): array
    {
        $items = [];
        foreach ($this->findArticlesByCategory($cat_ID) as $post) {
            $items[] = CommonItem::createFromPost($post);
        }

        $codesCgt = WpRepository::getMetaPivotCodesCgtOffers($cat_ID);
        if ($codesCgt !== []) {
            $pivotClient = Di::getInstance()->get(PivotClient::class);
            $offerResponse = $pivotClient->fetchOffersByCriteria();

            foreach ($offerResponse->getOffers() as $offer) {
                if (in_array($offer->codeCgt, $codesCgt, true)) {
                    $items[] = CommonItem::createFromOffer($offer);
                }
            }
        }

        usort($items, fn(CommonItem $a, CommonItem $b) => strcasecmp($a->name, $b->name));

        return $items;
    }


    public function findArticlesByCategory(int $catId): array
    {
        $args = [
            'cat' => $catId,
            'numberposts' => 5000,
            'orderby' => 'post_title',
            'order' => 'ASC',
            'post_status' => 'publish',
        ];

        $querynews = new WP_Query($args);
        $posts = [];
        while ($querynews->have_posts()) {
            $post = $querynews->next_post();
            $post->excerpt = $post->post_excerpt;
            $post->permalink = get_permalink($post->ID);
            $post->thumbnail_url = CommonItem::getPostThumbnail($post->ID);
            $posts[] = $post;
        }

        return $posts;
    }

}
