<?php

namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Lib\LocaleHelper;
use VisitMarche\ThemeWp\Lib\Menu;
use VisitMarche\ThemeWp\Lib\Twig;

$menu = new Menu();
$locale = LocaleHelper::getSelectedLanguage();
$items = $menu->getMenuTop($locale);
$icones = $menu->getIcons($locale);
Twig::renderPage(
    '@Visit/_footer.html.twig',
    [
        'items' => $items,
        'icons' => $icones,
    ]
);
echo '
</body>
</html>';
