<?php

namespace VisitMarche\ThemeWp;

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use VisitMarche\ThemeWp\Inc\AssetsLoader;

/**
 * Template sf
 */
if (WP_DEBUG === false) {
    HtmlErrorRenderer::setTemplate(get_template_directory().'/error500.php');
} else {
   //Debug::enable();
}
/**
 * Initialisation du thème
 */
//new SetupTheme();
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
//new ApiRoutes();
/**
 * Ajout de routage pour pivot
 */
//new RouterPivot();
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
//new AdminPage();
/**
 * Add buttons to admin bar
 */
//new AdminBar();
/**
 * Ajax for admin
 */
//new Ajax();
/**
 * Gpx viewer
 */
//new ShortCodes();
