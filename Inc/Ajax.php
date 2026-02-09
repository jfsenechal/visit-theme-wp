<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Inc;

use VisitMarche\ThemeWp\Lib\WpRepository;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_action_add_offer', fn() => $this->actionAddOffer());
        add_action('wp_ajax_action_delete_offer', fn() => $this->actionDeleteOffer());
    }

    private function actionAddOffer(): void
    {
        if (!check_ajax_referer('pivot_offers_nonce', '_ajax_nonce', false)) {
            wp_send_json_error('Invalid nonce', 403);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions', 403);
        }

        $categoryId = (int) ($_POST['categoryId'] ?? 0);
        $codeCgt = sanitize_text_field($_POST['codeCgt'] ?? '');
        $codesCgt = [];

        if ($categoryId > 0 && $codeCgt !== '') {
            $codesCgt = WpRepository::getMetaPivotCodesCgtOffres($categoryId);
            if (!in_array($codeCgt, $codesCgt, true)) {
                $codesCgt[] = $codeCgt;
                update_term_meta($categoryId, WpRepository::PIVOT_REFOFFERS, $codesCgt);
            }
        }

        wp_send_json($codesCgt);
    }

    private function actionDeleteOffer(): void
    {
        if (!check_ajax_referer('pivot_offers_nonce', '_ajax_nonce', false)) {
            wp_send_json_error('Invalid nonce', 403);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions', 403);
        }

        $categoryId = (int) ($_POST['categoryId'] ?? 0);
        $codeCgt = sanitize_text_field($_POST['codeCgt'] ?? '');
        $codesCgt = [];

        if ($categoryId > 0 && $codeCgt !== '') {
            $codesCgt = WpRepository::getMetaPivotCodesCgtOffres($categoryId);
            $key = array_search($codeCgt, $codesCgt, true);
            if ($key !== false) {
                unset($codesCgt[$key]);
                $codesCgt = array_values($codesCgt);
                update_term_meta($categoryId, WpRepository::PIVOT_REFOFFERS, $codesCgt);
            }
        }

        wp_send_json($codesCgt);
    }
}
