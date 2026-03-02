<?php

declare(strict_types=1);

namespace VisitMarche\ThemeWp\Enums;

enum CommonItemTypeEnum: string
{
    case POST = 'post';
    case OFFER = 'offer';
    case CATEGORY = 'category';
}
