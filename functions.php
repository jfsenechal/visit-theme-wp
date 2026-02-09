<?php

namespace VisitMarche\ThemeWp;

use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use VisitMarche\ThemeWp\Inc\AdminBar;
use VisitMarche\ThemeWp\Inc\AdminPages;
use VisitMarche\ThemeWp\Inc\Ajax;
use VisitMarche\ThemeWp\Inc\ApiRoutes;
use VisitMarche\ThemeWp\Inc\AssetsLoader;
use VisitMarche\ThemeWp\Inc\RouterPivot;
use VisitMarche\ThemeWp\Inc\SetupTheme;
use VisitMarche\ThemeWp\Lib\Frankenphp;

/**
 * Template sf
 */
if (WP_DEBUG === false) {
    HtmlErrorRenderer::setTemplate(get_template_directory().'/error500.php');
} else {
    //Debug::enable();
}
if (WP_DEBUG) {
    new Frankenphp();
}
/**
 * Initialisation du thème
 */
new SetupTheme();
/**
 * Chargement css, js
 */
new AssetsLoader();
/**
 * Un peu de sécurité
 */
//new SecurityConfig();
/**
 * Enregistrement des routes api
 */
new ApiRoutes();
/**
 * Ajout de routage pour pivot
 */
new RouterPivot();
/**
 * Balises pour le référencement
 */
//new Seo();
/**
 * Balises pour le social
 */
//new OpenGraph();
/**
 * Gpx viewer
 */
//new ShortCodes();
/**
 * Admin pages
 */
new AdminPages();
/**
 * Add buttons to admin bar
 */
new AdminBar();
/**
 * Ajax for admin
 */
new Ajax();
/**
 * Gpx viewer
 */
//new ShortCodes();
