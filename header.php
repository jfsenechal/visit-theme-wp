<?php

namespace VisitMarche\ThemeWp;
use VisitMarche\ThemeWp\Lib\Menu;
use VisitMarche\ThemeWp\Lib\Twig;

$locale='fr';
?>
    <!doctype html>
<html lang="<?php echo $locale; ?>">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="https://gmpg.org/xfn/11">
        <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri() ?>/assets/images/favicon.png"/>
        <?php wp_head();

        ?>
    </head>

<body <?php body_class(); ?> id="app" data-langwp="<?= $locale ?>" data-langsf="<?= $locale ?>">
    <?php
wp_body_open();

$menu = new Menu();
$items = $menu->getMenuTop();
$icons = $menu->getIcones();
Twig::rendPage(
    '@Visit/header/_header.html.twig',
    [
        'locale' => $locale,
        'items' => $items,
        'icons' => $icons,
    ]
);

