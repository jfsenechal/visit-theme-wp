<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Dto;

use AcMarche\PivotAi\Entity\Pivot\Offer;

class CommonItem
{
    public ?string $url = null;

    /** @var array<int, object{name: string}> */
    public array $tags = [];

    public function __construct(
        public string $id,
        public string $type,
        public string $name,
        public string $image,
        public string $description = '',
    ) {
    }

    public static function createFromPost(\WP_Post $post): CommonItem
    {
        $item = new CommonItem(
            id: (string)$post->ID,
            type: 'post',
            name: $post->post_title,
            image: self::getPostThumbnail($post->ID),
            description: $post->post_excerpt,
        );
        $item->url = get_permalink($post->ID);

        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            $item->tags[] = (object)['name' => $category->name];
        }

        return $item;
    }

    public static function createFromOffer(Offer $offer): CommonItem
    {
        $image = $offer->getDefaultImage();

        $item = new CommonItem(
            id: $offer->codeCgt ?? '',
            type: 'offer',
            name: $offer->nom ?? '',
            image: $image?->url ?? get_template_directory_uri().'/assets/images/404.jpg',
            description: $offer->getShortDescription() ?? '',
        );

        if ($offer->typeOffre) {
            $label = $offer->typeOffre->getLabelByLang('fr');
            if ($label) {
                $item->tags[] = (object)['name' => $label];
            }
        }

        return $item;
    }

    public static function getPostThumbnail(int $id): string
    {
        if (has_post_thumbnail($id)) {
            $attachment_id = get_post_thumbnail_id($id);
            $images = wp_get_attachment_image_src($attachment_id, 'original');
            $post_thumbnail_url = $images[0];
        } else {
            $post_thumbnail_url = get_template_directory_uri().'/assets/images/404.jpg';
        }

        return $post_thumbnail_url;
    }
}
