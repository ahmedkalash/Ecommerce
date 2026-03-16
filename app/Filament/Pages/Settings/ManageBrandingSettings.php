<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Settings\BrandingSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Storage;

class ManageBrandingSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.branding.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.branding.title');
    }

    protected static string $settings = BrandingSettings::class;

    protected static ?int $navigationSort = 2;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.branding.sections.logos_icons'))
                    ->schema([
                        FileUpload::make('header_logo')
                            ->label(__('admin/settings.branding.fields.header_logo'))
                            ->image()
                            ->directory('settings'),
                        FileUpload::make('footer_logo')
                            ->label(__('admin/settings.branding.fields.footer_logo'))
                            ->image()
                            ->directory('settings'),
                        FileUpload::make('site_icon')
                            ->label(__('admin/settings.branding.fields.site_icon'))
                            ->image()
                            ->directory('settings'),
                    ])->columns(2),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $settings = app(static::$settings);
        $fileFields = ['header_logo', 'footer_logo', 'site_icon'];

        foreach ($fileFields as $field) {
            $oldFile = $settings->{$field} ?? null;
            $newFile = $data[$field] ?? null;

            if ($oldFile && $oldFile !== $newFile) {
                Storage::disk('public')->delete($oldFile);
            }
        }

        return $data;
    }
}
