<?php

namespace App\Filament\Resources;

use App\Enums\SpecialPriceType;
use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Services\PricingResolverService;
use App\Services\ProductService;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::CATALOG->getLocalizedLabel();
    }

    public static function getModelLabel(): string
    {
        return __('admin/resources.product.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin/resources.product.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/navigation.products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('ProductTabs')
                    ->tabs([
                        // ── General Tab ──
                        Tabs\Tab::make(__('admin/resources.product.tab_general'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make(__('admin/resources.product.section_information'))
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('admin/resources.general.name'))
                                                    ->required()
                                                    ->maxLength(200)
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
                                                    ->unique(Product::class, 'slug', ignoreRecord: true),
                                                Forms\Components\RichEditor::make('description')
                                                    ->label(__('admin/resources.general.description'))
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                    ])
                                    ->columnSpan(['lg' => 2]),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make(__('admin/resources.product.section_visibility'))
                                            ->schema([
                                                Forms\Components\Toggle::make('published')
                                                    ->required()
                                                    ->label(__('admin/resources.product.published'))
                                                    ->default(true),
                                                Forms\Components\Toggle::make('approved')
                                                    ->label(__('admin/resources.product.approved'))
                                                    ->default(true)
                                                    ->visible(fn () => auth()->user()->can('approve_products')),
                                            ]),

                                        Forms\Components\Section::make(__('admin/resources.product.section_image'))
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                                    ->collection('thumbnail')
                                                    ->label(__('admin/resources.product.thumbnail'))
                                                    ->image()
                                                    ->imageEditor()
                                                    ->columnSpanFull(),
                                            ]),

                                        Forms\Components\Section::make(__('admin/resources.product.section_organization'))
                                            ->schema([
                                                SelectTree::make('categories')
                                                    ->relationship('categories', 'name', 'parent_id')
                                                    ->saveRelationshipsUsing(fn () => null)
                                                    ->dehydrated()
                                                    ->label(__('admin/resources.product.categories'))
                                                    ->enableBranchNode()
                                                    ->expandSelected()
                                                    ->withCount()
                                                    ->searchable()
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Forms\Components\Select::make('brand_id')
                                                    ->required()
                                                    ->label(__('admin/resources.product.brand'))
                                                    ->relationship('brand', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                SpatieTagsInput::make('tags')
                                                    ->label(__('admin/resources.product.tags'))
                                                    ->dehydrated()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])
                            ->columns(3),

                        // ── Price, Stock & Variants Tab ──
                        Tabs\Tab::make(__('admin/resources.product.tab_price_stock'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                // This repeater manages ALL stocks (variants).
                                Forms\Components\Repeater::make('stocks')
                                    ->label(__('admin/resources.product.variants_label'))
                                    ->itemLabel(fn (array $state): ?string => $state['variant'] ?? 'New Variant')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->schema([
                                        Forms\Components\Section::make(__('admin/resources.product.section_variant_details'))
                                            ->schema([
                                                Forms\Components\TextInput::make('variant')
                                                    ->label(__('admin/resources.product.variant_name'))
                                                    ->placeholder(__('admin/resources.product.variant_placeholder'))
                                                    ->default('Default')
                                                    ->required()
                                                    ->distinct()
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('sku')
                                                    ->label(__('admin/resources.product.sku'))
                                                    ->placeholder(fn (
                                                        ?Product $record
                                                    ): string => $record?->sku ?: __('admin/resources.product.sku_placeholder'))->unique(
                                                        table: 'product_stocks',
                                                        column: 'sku',
                                                        modifyRuleUsing: function (Unique $rule, ?Product $record) {
                                                            // If we are editing an existing product, ignore its associated stock row.
                                                            if ($record) {
                                                                return $rule->whereNot('product_id', $record->id);
                                                            }

                                                            return $rule;
                                                        }
                                                    )
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('price')
                                                    ->label(__('admin/resources.product.price'))
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required(),

                                                Forms\Components\TextInput::make('qty')
                                                    ->label(__('admin/resources.product.qty'))
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required(),

                                                Forms\Components\TextInput::make('min_qty')
                                                    ->label(__('admin/resources.product.min_qty'))
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\Toggle::make('cash_on_delivery')
                                                    ->label(__('admin/resources.product.cod'))
                                                    ->default(true)
                                                    ->inline(false),

                                                Forms\Components\Toggle::make('todays_deal')
                                                    ->label(__('admin/resources.product.todays_deal'))
                                                    ->default(true)
                                                    ->inline(false),

                                                Forms\Components\Repeater::make('extra_attributes.specifications')
                                                    ->label(__('admin/resources.product.variant_attributes'))
                                                    ->helperText(__('admin/resources.product.variant_attributes_help'))
                                                    ->defaultItems(0)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('key')
                                                            ->label(__('admin/resources.product.attr_key'))
                                                            ->placeholder(__('admin/resources.product.attr_key_placeholder'))
                                                            ->required()
                                                            ->columnSpan(1),
                                                        Forms\Components\RichEditor::make('value')
                                                            ->label(__('admin/resources.product.attr_value'))
                                                            ->required()
                                                            ->toolbarButtons([
                                                                'bold',
                                                                'italic',
                                                                'link',
                                                                'bulletList',
                                                                'orderedList',
                                                            ])
                                                            ->extraInputAttributes(['style' => 'min-height: 100px;'])
                                                            ->columnSpan(2),
                                                    ])
                                                    ->addActionLabel(__('admin/resources.product.add_attribute'))
                                                    ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                    ->collapsible()
                                                    ->columns(3)
                                                    ->columnSpanFull(),

                                                Forms\Components\Section::make(__('admin/resources.product.section_special_price'))
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\Select::make('special_price_type')
                                                            ->label(__('admin/resources.product.discount_type'))
                                                            ->options(SpecialPriceType::class)
                                                            ->nullable()
                                                            ->live(),
                                                        Forms\Components\TextInput::make('special_price')
                                                            ->label(__('admin/resources.product.discount_value'))
                                                            ->numeric()
                                                            ->requiredWith('special_price_type')
                                                            ->rules(['nullable', 'numeric', 'min:0'])
                                                            ->helperText(fn (
                                                                Forms\Get $get
                                                            ) => match ($get('special_price_type')) {
                                                                'discount_percent' => __('admin/resources.product.discount_percent_help'),
                                                                'fixed_price' => __('admin/resources.product.fixed_price_help'),
                                                                default => __('admin/resources.product.discount_type_hint'),
                                                            }),
                                                        Forms\Components\DateTimePicker::make('special_price_start')
                                                            ->label(__('admin/resources.product.special_price_start'))
                                                            ->requiredWith('special_price_type'),
                                                        Forms\Components\DateTimePicker::make('special_price_end')
                                                            ->label(__('admin/resources.product.special_price_end'))
                                                            ->requiredWith('special_price_type')
                                                            ->afterOrEqual('special_price_start'),
                                                    ])->columns(2),

                                                Forms\Components\Section::make(__('admin/resources.product.section_media'))
                                                    ->collapsed()
                                                    ->schema([
                                                        // Gallery (First, Full Width)
                                                        SpatieMediaLibraryFileUpload::make('gallery')
                                                            ->collection('gallery')
                                                            ->label(__('admin/resources.product.variant_gallery'))
                                                            ->multiple()
                                                            ->reorderable()
                                                            ->image()
                                                            ->imageEditor()
                                                            ->columnSpanFull()
                                                            ->panelLayout('grid') // Attempt to force grid layout if supported by theme, otherwise full width usually does it
                                                            ->extraAttributes(['class' => 'gallery-grid']),
                                                        // Hooks for custom CSS if needed

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                                                    ->collection('thumbnail')
                                                                    ->label(__('admin/resources.product.variant_thumbnail'))
                                                                    ->image()
                                                                    ->imageEditor(),
                                                            ]),

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                SpatieMediaLibraryFileUpload::make('pdf')
                                                                    ->collection('pdf')
                                                                    ->label(__('admin/resources.product.pdf_spec'))
                                                                    ->acceptedFileTypes(['application/pdf'])
                                                                    ->maxSize(51200), // 50MB

                                                                SpatieMediaLibraryFileUpload::make('files')
                                                                    ->collection('files')
                                                                    ->collection('files')
                                                                    ->label(__('admin/resources.product.downloadable_files'))
                                                                    ->multiple()
                                                                    ->maxSize(51200), // 50MB
                                                            ]),

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Select::make('video_provider')
                                                                    ->options([
                                                                        'youtube' => 'Youtube',
                                                                        'dailymotion' => 'Dailymotion',
                                                                        'vimeo' => 'Vimeo',
                                                                    ])
                                                                    ->label(__('admin/resources.product.video_provider')),
                                                                Forms\Components\TextInput::make('video_link')
                                                                    ->label(__('admin/resources.product.video_link')),
                                                            ]),

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                SpatieMediaLibraryFileUpload::make('short_video')
                                                                    ->collection('short_video')
                                                                    ->label(__('admin/resources.product.short_video'))
                                                                    ->acceptedFileTypes([
                                                                        'video/mp4',
                                                                        'video/webm',
                                                                        'video/ogg',
                                                                    ])
                                                                    ->maxSize(51200), // 50MB

                                                                SpatieMediaLibraryFileUpload::make('short_video_thumbnail')
                                                                    ->collection('short_video_thumbnail')
                                                                    ->label(__('admin/resources.product.short_video_thumbnail'))
                                                                    ->image(),
                                                            ]),
                                                    ]),

                                            ])->columns(4),
                                    ])
                                    ->reorderable()
                                    ->addActionLabel(__('admin/resources.product.add_variant'))
                                    ->columnSpanFull(),
                            ]),

                        // ── SEO Tab ──
                        Tabs\Tab::make(__('admin/resources.product.tab_seo'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('admin/resources.general.meta_title'))
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('admin/resources.general.meta_description'))
                                    ->maxLength(65000)
                                    ->rows(3),
                                SpatieMediaLibraryFileUpload::make('meta_img')
                                    ->collection('meta')
                                    ->label(__('admin/resources.product.meta_image'))
                                    ->image()
                                    ->columnSpanFull(),
                            ]),

                        // ── Shipping Tab ──
                        Tabs\Tab::make(__('admin/resources.product.tab_shipping'))
                            ->icon('heroicon-o-truck')
                            ->schema([
                                // Cash on delivery moved to stocks
                                Forms\Components\Select::make('shipping_type')
                                    ->label(__('admin/resources.product.shipping_type'))
                                    ->options([
                                        'free' => __('admin/resources.product.shipping_free'),
                                        'flat_rate' => __('admin/resources.product.shipping_flat_rate'),
                                    ])
                                    ->default('flat_rate'),
                                Forms\Components\TextInput::make('shipping_cost')
                                    ->label(__('admin/resources.product.shipping_cost'))
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('est_shipping_days')
                                    ->label(__('admin/resources.product.est_shipping_days'))
                                    ->numeric(),
                            ])->columns(2),

                        // Status tab content moved to General tab

                        // ── Warranty Tab ──
                        Tabs\Tab::make(__('admin/resources.product.tab_warranty'))
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Toggle::make('has_warranty')
                                    ->label(__('admin/resources.product.has_warranty')),
                            ]),
                    ])
                    ->persistTab()
                    ->id('product-tabs')
                    ->contained(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->collection('thumbnail')
                    ->label(__('admin/resources.product.thumbnail')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin/resources.general.name'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('admin/resources.product.categories'))
                    ->badge()
                    ->separator(',')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_price')
                    ->label(__('admin/resources.product.col_min_price'))
                    ->state(fn (Product $record) => $record->stocks_min_price ?? $record->stocks->min('price'))
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->label(__('admin/resources.product.col_max_price'))
                    ->state(fn (Product $record) => $record->stocks_max_price ?? $record->stocks->max('price'))
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label(__('admin/resources.product.col_qty'))
                    ->state(fn (Product $record) => $record->stocks_sum_qty ?? $record->stocks->sum('qty'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_min_price')
                    ->label(__('admin/resources.product.col_effective_min'))
                    ->state(function (Product $record) {
                        $resolver = app(PricingResolverService::class);

                        return $record->stocks
                            ->map(fn ($s) => $resolver->resolve($s)->finalPrice)
                            ->min();
                    })
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('published')
                    ->label(__('admin/resources.product.published'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin/resources.general.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Product $record) {
                        return DB::transaction(function () use ($record) {
                            try {
                                app(ProductService::class)->destroy($record->id);
                            } catch (Exception $e) {
                                Log::error('Product deletion failed (Filament Action): '.$e->getMessage(), [
                                    'product_id' => $record->id,
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                throw $e;
                            }
                        });
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            return DB::transaction(function () use ($records) {
                                try {
                                    $service = app(ProductService::class);
                                    foreach ($records as $record) {
                                        $service->destroy($record->id);
                                    }
                                } catch (Exception $e) {
                                    Log::error('Bulk product deletion failed (Filament Action): '.$e->getMessage(), [
                                        'ids' => $records->pluck('id')->toArray(),
                                        'trace' => $e->getTraceAsString(),
                                    ]);

                                    throw $e;
                                }
                            });
                        }),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withMin('stocks', 'price')
            ->withMax('stocks', 'price')
            ->withSum('stocks', 'qty');
    }
}
