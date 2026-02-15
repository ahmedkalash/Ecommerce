<?php

namespace App\Filament\Enums;

enum NavigationGroups: string
{
    case CATALOG = 'Catalog';
    case SHOP_MANAGEMENT = 'Shop Management';
    case USER_MANAGEMENT = 'User Management';
    case SETTINGS = 'Settings';
}
