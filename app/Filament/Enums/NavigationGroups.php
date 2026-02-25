<?php

namespace App\Filament\Enums;

/**
 * Enum representing Filament navigation groups with localised labels.
 *
 * Use `NavigationGroups::CATALOG->getLocalizedLabel()` wherever you need
 * a translated group name (e.g. in AdminPanelProvider::navigationGroups()).
 */
enum NavigationGroups: string
{
    case CATALOG = 'Catalog';
    case SHOP_MANAGEMENT = 'Shop Management';
    case USER_MANAGEMENT = 'User Management';
    case SETTINGS = 'Settings';

    /**
     * Return the translated navigation group label.
     *
     * Falls back to the enum value (English) when the translation key is missing.
     */
    public function getLocalizedLabel(): string
    {
        return match ($this) {
            self::CATALOG => __('admin/navigation.catalog'),
            self::SHOP_MANAGEMENT => __('admin/navigation.shop_management'),
            self::USER_MANAGEMENT => __('admin/navigation.user_management'),
            self::SETTINGS => __('admin/navigation.settings'),
        };
    }
}
