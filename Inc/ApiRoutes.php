<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Inc;

use VisitMarche\ThemeWp\Lib\WpRepository;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;

class ApiRoutes
{
    public function __construct()
    {
        add_action('rest_api_init', fn() => $this->registerRoutes());
    }

    private function registerRoutes(): void
    {
        register_rest_route(
            'pivot',
            'find-offers-by-name/(?P<name>.+)',
            [
                'methods' => 'GET',
                'callback' => fn(WP_REST_Request $request) => $this->findOffersByName($request),
                'permission_callback' => fn() => is_user_logged_in(),
            ],
        );

        register_rest_route(
            'pivot',
            'category_offers/(?P<categoryId>\d+)',
            [
                'methods' => 'GET',
                'callback' => fn(WP_REST_Request $request) => $this->getCategoryOffers($request),
                'permission_callback' => fn() => is_user_logged_in(),
            ],
        );
    }

    private function findOffersByName(WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response
    {
        $name = urldecode((string) $request->get_param('name'));
        $wpRepository = new WpRepository();

        try {
            $offres = $wpRepository->findShortsByNameOrCode($name);
        } catch (\Exception $e) {
            return rest_ensure_response(['error' => $e->getMessage()]);
        }

        return rest_ensure_response($offres);
    }

    private function getCategoryOffers(WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response
    {
        $categoryId = (int) $request->get_param('categoryId');
        if ($categoryId < 1) {
            return new WP_Error(400, 'Missing param categoryId');
        }

        $codesCgt = WpRepository::getMetaPivotCodesCgtOffres($categoryId);
        $wpRepository = new WpRepository();

        try {
            $offers = $wpRepository->findOffersShortByCodesCgt($codesCgt);
        } catch (\Exception $e) {
            $offers = [];
        }

        return rest_ensure_response($offers);
    }
}
