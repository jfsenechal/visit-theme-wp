<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Dto;

use AcMarche\PivotAi\Entity\Pivot\Offer;
use VisitMarche\ThemeWp\Inc\CategoryMetaData;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Inc\Theme;

class CommonItem
{
    public ?string $url = null;

    /** @var array<int, object{name: string}> */
    public array $tags = [];

    public ?string $content = null;

    public function __construct(
        public string $id,
        public string $type,
        public string $name,
        public string $image,
        public ?string $excerpt = null,
    ) {
    }

    public static function createFromPost(\WP_Post $post): CommonItem
    {
        $item = new CommonItem(
            id: (string)$post->ID,
            type: 'post',
            name: $post->post_title,
            image: self::getPostThumbnail($post->ID),
            excerpt: $post->post_excerpt
        );

        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            $item->tags[] = (object)['name' => $category->name];
        }

        $item->content = apply_filters('the_content', get_the_content(null, null, $post));
        $item->url = get_permalink($post);

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
            excerpt: $offer->getShortDescription() ?? '',
        );

        if ($offer->typeOffre) {
            $label = $offer->typeOffre->getLabelByLang('fr');
            if ($label) {
                $item->tags[] = (object)['name' => $label];
            }
        }

        $item->url = RouterPivot::getOfferUrl(Theme::CATEGORY_NOT_CATEGORIZED, $offer->codeCgt);
        $item->content = $offer->getDescription();

        return $item;
    }

    public static function createFromCategory(\WP_Term $category, string $content, array $tags = []): CommonItem
    {
        $item = new CommonItem(
            id: (string)$category->ID,
            type: 'category',
            name: $category->name,
            image: CategoryMetaData::getImage($category),
            excerpt: $category->description
        );

        $item->url = get_category_link($category);
        $item->tags = $tags;
        $item->content = $content;

        return $item;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'image' => $this->image,
            'excerpt' => $this->excerpt !== null ? strip_tags($this->excerpt) : null,
            'content' => $this->content !== null ? strip_tags($this->content) : null,
            'url' => $this->url,
            'tags' => array_map(fn($tag) => ['name' => $tag->name], $this->tags),
        ];
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
