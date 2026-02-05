<?php

namespace VisitMarche\ThemeWp;

use VisitMarche\ThemeWp\Lib\Menu;
use VisitMarche\ThemeWp\Lib\Twig;

$menu = new Menu();
$items = $menu->getMenuTop();
$icones = $menu->getIcones();
Twig::rendPage(
    '@Visit/_footer.html.twig',
    [
        'items' => $items,
        'icons' => $icones,
    ]
);
echo '
</body>
</html>';