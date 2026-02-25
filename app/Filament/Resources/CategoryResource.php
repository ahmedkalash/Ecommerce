<?php

namespace App\Filament\Resources;

use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::CATALOG->getLocalizedLabel();
    }

    public static function getModelLabel(): string
    {
        return __('admin/resources.category.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin/resources.category.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/navigation.categories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('CategoryTabs')
                    ->tabs([
                        // ── General Tab ──
                        Tabs\Tab::make(__('admin/resources.category.tab_general'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make(__('admin/resources.category.section_information'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('admin/resources.general.name'))
                                            ->required()
                                            ->maxLength(50)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('slug', Str::slug($state));
                                            }),
                                        Forms\Components\TextInput::make('slug')
                                            ->label(__('admin/resources.general.slug'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Category::class, 'slug', ignoreRecord: true),
                                    ])
                                    ->columns(2),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make(__('admin/resources.category.section_organization'))
                                            ->schema([
                                                SelectTree::make('parent_id')
                                                    ->relationship('parentCategory', 'name', 'parent_id')
                                                    ->label(__('admin/resources.category.parent'))
                                                    ->placeholder(__('admin/resources.category.none_root'))
                                                    ->enableBranchNode()
                                                    ->expandSelected()
                                                    ->withCount()
                                                    ->searchable(),

                                            ])
                                            ->columnSpan(1),

                                        Forms\Components\Section::make(__('admin/resources.category.section_flags'))
                                            ->schema([
                                                Forms\Components\Toggle::make('featured')
                                                    ->label(__('admin/resources.category.featured'))
                                                    ->default(false),
                                                Forms\Components\Toggle::make('top')
                                                    ->label(__('admin/resources.category.top'))
                                                    ->default(false),
                                                Forms\Components\Toggle::make('digital')
                                                    ->label(__('admin/resources.category.digital'))
                                                    ->default(false),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        // ── Media Tab ──
                        Tabs\Tab::make(__('admin/resources.category.tab_media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('banner')
                                    ->collection('banner')
                                    ->label(__('admin/resources.category.banner'))
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Large banner image displayed on category page'),
                                SpatieMediaLibraryFileUpload::make('icon')
                                    ->collection('icon')
                                    ->label(__('admin/resources.category.icon'))
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Small icon displayed in navigation/menus'),
                                SpatieMediaLibraryFileUpload::make('cover_image')
                                    ->collection('cover_image')
                                    ->label(__('admin/resources.category.cover_image'))
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Cover image for category cards'),
                            ])->columns(3),

                        // ── SEO Tab ──
                        Tabs\Tab::make(__('admin/resources.category.tab_seo'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('admin/resources.general.meta_title'))
                                    ->maxLength(255)
                                    ->helperText('Leave empty to use category name'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('admin/resources.general.meta_description'))
                                    ->maxLength(255)
                                    ->rows(3)
                                    ->helperText('Leave empty to auto-generate'),
                            ]),

                        // ── Advanced Tab ──
                        Tabs\Tab::make(__('admin/resources.category.tab_advanced'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make(__('admin/resources.category.section_commission'))
                                    ->compact()
                                    ->schema([
                                        Forms\Components\TextInput::make('commision_rate')
                                            ->label(__('admin/resources.category.commission_rate'))
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100),
                                    ]),

                                Forms\Components\Section::make(__('admin/resources.category.section_refund'))
                                    ->compact()
                                    ->schema([
                                        Forms\Components\TextInput::make('refund_request_time')
                                            ->label(__('admin/resources.category.refund_days'))
                                            ->numeric()
                                            ->helperText('Number of days customers can request a refund')
                                            ->suffix('days'),
                                    ]),
                            ]),
                    ])
                    ->persistTab()
                    ->id('category-tabs')
                    ->contained(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin/resources.general.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string => $record->slug ?? ''),
                Tables\Columns\TextColumn::make('parentCategory.name')
                    ->label(__('admin/resources.category.col_parent'))
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('admin/resources.category.root_category')),
                Tables\Columns\TextColumn::make('products_count')
                    ->label(__('admin/resources.category.col_products'))
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\IconColumn::make('featured')
                    ->label(__('admin/resources.category.featured'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('top')
                    ->label(__('admin/resources.category.top'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin/resources.general.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin/resources.general.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('featured')
                    ->label(__('admin/resources.category.featured')),
                Tables\Filters\TernaryFilter::make('top')
                    ->label(__('admin/resources.category.top')),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('admin/resources.category.parent'))
                    ->relationship('parentCategory', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('admin/actions.general.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('admin/actions.general.delete'))
                    ->using(fn (Category $record) => app(\App\Services\CategoryService::class)->delete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn (
                            \Illuminate\Support\Collection $records
                        ) => app(\App\Services\CategoryService::class)->bulkDelete($records)),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
