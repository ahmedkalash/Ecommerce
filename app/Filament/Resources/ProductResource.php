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

    protected static ?string $navigationGroup = NavigationGroups::CATALOG;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('ProductTabs')
                    ->tabs([
                        // ── General Tab ──
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make('Product Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
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
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(Product::class, 'slug', ignoreRecord: true),
                                                Forms\Components\RichEditor::make('description')
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                    ])
                                    ->columnSpan(['lg' => 2]),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Section::make('Visibility & Status')
                                            ->schema([
                                                Forms\Components\Toggle::make('published')
                                                    ->required()
                                                    ->label('Published')
                                                    ->default(true),
                                                Forms\Components\Toggle::make('approved')
                                                    ->label('Approved')
                                                    ->default(true)
                                                    ->visible(fn () => auth()->user()->can('approve_products')),
                                            ]),

                                        Forms\Components\Section::make('Product Image')
                                            ->schema([
                                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                                    ->collection('thumbnail')
                                                    ->label('Thumbnail Image')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->columnSpanFull(),
                                            ]),

                                        Forms\Components\Section::make('Organization')
                                            ->schema([
                                                SelectTree::make('categories')
                                                    ->relationship('categories', 'name', 'parent_id')
                                                    ->saveRelationshipsUsing(fn () => null)
                                                    ->dehydrated()
                                                    ->label('Categories')
                                                    ->enableBranchNode()
                                                    ->expandSelected()
                                                    ->withCount()
                                                    ->searchable()
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Forms\Components\Select::make('brand_id')
                                                    ->required()
                                                    ->label('Brand')
                                                    ->relationship('brand', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                SpatieTagsInput::make('tags')
                                                    ->dehydrated()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])
                            ->columns(3),

                        // ── Price, Stock & Variants Tab ──
                        Tabs\Tab::make('Price & Stock')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                // This repeater manages ALL stocks (variants).
                                Forms\Components\Repeater::make('stocks')
                                    ->label('Product Variants / Inventory')
                                    ->itemLabel(fn (array $state): ?string => $state['variant'] ?? 'New Variant')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->schema([
                                        Forms\Components\Section::make('Variant Details')
                                            ->schema([
                                                Forms\Components\TextInput::make('variant')
                                                    ->label('Variant Name')
                                                    ->placeholder('e.g., Default, Large-Blue, Extra-Cotton')
                                                    ->default('Default')
                                                    ->required()
                                                    ->distinct()
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->placeholder(fn (
                                                        ?Product $record
                                                    ): string => $record?->sku ?: 'Auto-generated if empty')->unique(
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
                                                    ->label('Base Price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required(),

                                                Forms\Components\TextInput::make('qty')
                                                    ->label('Qty In Stock')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required(),

                                                Forms\Components\TextInput::make('min_qty')
                                                    ->label('Min Purchase Qty')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\Toggle::make('cash_on_delivery')
                                                    ->label('COD Available')
                                                    ->default(true)
                                                    ->inline(false),

                                                Forms\Components\Toggle::make('todays_deal')
                                                    ->label('Today\'s Deal')
                                                    ->default(true)
                                                    ->inline(false),

                                                Forms\Components\Repeater::make('extra_attributes.specifications')
                                                    ->label('Variant Specific Attributes')
                                                    ->helperText('Define technical specs or attributes for this specific version.')
                                                    ->defaultItems(0)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('key')
                                                            ->label('Key')
                                                            ->placeholder('e.g., Material, Warranty')
                                                            ->required()
                                                            ->columnSpan(1),
                                                        Forms\Components\RichEditor::make('value')
                                                            ->label('Value')
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
                                                    ->addActionLabel('Add another Attribute')
                                                    ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                                    ->collapsible()
                                                    ->columns(3)
                                                    ->columnSpanFull(),

                                                Forms\Components\Section::make('Special Price')
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\Select::make('special_price_type')
                                                            ->label('Discount Type')
                                                            ->options(SpecialPriceType::class)
                                                            ->nullable()
                                                            ->live(),
                                                        Forms\Components\TextInput::make('special_price')
                                                            ->label('Discount Value')
                                                            ->numeric()
                                                            ->requiredWith('special_price_type')
                                                            ->rules(['nullable', 'numeric', 'min:0'])
                                                            ->helperText(fn (
                                                                Forms\Get $get
                                                            ) => match ($get('special_price_type')) {
                                                                'discount_percent' => 'Percentage off (0–100)',
                                                                'fixed_price' => 'Exact final price the customer pays',
                                                                default => 'Select a discount type first',
                                                            }),
                                                        Forms\Components\DateTimePicker::make('special_price_start')
                                                            ->label('Start Date')
                                                            ->requiredWith('special_price_type'),
                                                        Forms\Components\DateTimePicker::make('special_price_end')
                                                            ->label('End Date')
                                                            ->requiredWith('special_price_type')
                                                            ->afterOrEqual('special_price_start'),
                                                    ])->columns(2),

                                                Forms\Components\Section::make('Media & Files')
                                                    ->collapsed()
                                                    ->schema([
                                                        // Gallery (First, Full Width)
                                                        SpatieMediaLibraryFileUpload::make('gallery')
                                                            ->collection('gallery')
                                                            ->label('Variant Gallery')
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
                                                                    ->label('Variant Thumbnail')
                                                                    ->image()
                                                                    ->imageEditor(),
                                                            ]),

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                SpatieMediaLibraryFileUpload::make('pdf')
                                                                    ->collection('pdf')
                                                                    ->label('PDF Specification')
                                                                    ->acceptedFileTypes(['application/pdf'])
                                                                    ->maxSize(51200), // 50MB

                                                                SpatieMediaLibraryFileUpload::make('files')
                                                                    ->collection('files')
                                                                    ->collection('files')
                                                                    ->label('Downloadable Files')
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
                                                                    ->label('Video Provider'),
                                                                Forms\Components\TextInput::make('video_link')
                                                                    ->label('Video Link'),
                                                            ]),

                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                SpatieMediaLibraryFileUpload::make('short_video')
                                                                    ->collection('short_video')
                                                                    ->label('Short Video')
                                                                    ->acceptedFileTypes([
                                                                        'video/mp4',
                                                                        'video/webm',
                                                                        'video/ogg',
                                                                    ])
                                                                    ->maxSize(51200), // 50MB

                                                                SpatieMediaLibraryFileUpload::make('short_video_thumbnail')
                                                                    ->collection('short_video_thumbnail')
                                                                    ->label('Short Video Thumbnail')
                                                                    ->image(),
                                                            ]),
                                                    ]),

                                            ])->columns(4),
                                    ])
                                    ->reorderable()
                                    ->addActionLabel('Add Another Variant')
                                    ->columnSpanFull(),
                            ]),

                        // ── SEO Tab ──
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(65000)
                                    ->rows(3),
                                SpatieMediaLibraryFileUpload::make('meta_img')
                                    ->collection('meta')
                                    ->label('Meta Image (SEO)')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),

                        // ── Shipping Tab ──
                        Tabs\Tab::make('Shipping')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                // Cash on delivery moved to stocks
                                Forms\Components\Select::make('shipping_type')
                                    ->options([
                                        'free' => 'Free Shipping',
                                        'flat_rate' => 'Flat Rate',
                                    ])
                                    ->default('flat_rate'),
                                Forms\Components\TextInput::make('shipping_cost')
                                    ->label('Shipping Cost')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('est_shipping_days')
                                    ->label('Estimate Shipping Days')
                                    ->numeric(),
                            ])->columns(2),

                        // Status tab content moved to General tab

                        // ── Warranty Tab ──
                        Tabs\Tab::make('Warranty')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Toggle::make('has_warranty')
                                    ->label('Has Warranty'),
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
                    ->collection('thumbnail'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->name),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->separator(',')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_price')
                    ->label('Min Price')
                    ->state(fn (Product $record) => $record->stocks_min_price ?? $record->stocks->min('price'))
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->label('Max Price')
                    ->state(fn (Product $record) => $record->stocks_max_price ?? $record->stocks->max('price'))
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Qty')
                    ->state(fn (Product $record) => $record->stocks_sum_qty ?? $record->stocks->sum('qty'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_min_price')
                    ->label('Effective Min')
                    ->state(function (Product $record) {
                        $resolver = app(PricingResolverService::class);

                        return $record->stocks
                            ->map(fn ($s) => $resolver->resolve($s)->finalPrice)
                            ->min();
                    })
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('published')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
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
