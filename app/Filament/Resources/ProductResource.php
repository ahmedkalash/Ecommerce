<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Products';

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
                                SelectTree::make('categories')
                                    ->relationship('categories', 'name', 'parent_id')
                                    ->label('Product Category')
                                    ->enableBranchNode()
                                    ->expandSelected()
                                    ->withCount()
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('brand_id')
                                    ->label('Brand')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('min_qty')
                                    ->label('Min Purchase Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\TagsInput::make('tags')
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // ── Files & Media Tab ──
                        Tabs\Tab::make('Files & Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->collection('thumbnail')
                                    ->label('Thumbnail Image')
                                    ->image()
                                    ->imageEditor(),
                                SpatieMediaLibraryFileUpload::make('gallery')
                                    ->collection('gallery')
                                    ->multiple()
                                    ->reorderable()
                                    ->label('Gallery Images')
                                    ->image(),
                                SpatieMediaLibraryFileUpload::make('meta_img')
                                    ->collection('meta')
                                    ->label('Meta Image (SEO)')
                                    ->image(),
                            ]),

                        // ── Price & Stock Tab ──
                        Tabs\Tab::make('Price & Stock')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                                Forms\Components\TextInput::make('purchase_price')
                                    ->label('Purchase Price')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('tax')
                                    ->label('Tax')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Select::make('tax_type')
                                    ->options([
                                        'amount' => 'Flat',
                                        'percent' => 'Percent',
                                    ])
                                    ->default('amount'),
                                Forms\Components\TextInput::make('discount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Select::make('discount_type')
                                    ->options([
                                        'amount' => 'Flat',
                                        'percent' => 'Percent',
                                    ])
                                    ->default('amount'),
                                Forms\Components\TextInput::make('current_stock')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('external_link')
                                    ->placeholder('http://...')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('external_link_btn')
                                    ->label('External Link Button Text')
                                    ->default('Buy Now'),
                            ])->columns(2),

                        // ── Variations Tab ──
                        Tabs\Tab::make('Variations')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Forms\Components\Toggle::make('variant_product')
                                    ->label('Enable Variations')
                                    ->live(),

                                Forms\Components\Section::make('Variation Configuration')
                                    ->visible(fn (Forms\Get $get) => $get('variant_product'))
                                    ->schema([
                                        Forms\Components\Select::make('colors')
                                            ->label('Colors')
                                            ->multiple()
                                            ->options(\App\Models\Color::all()->pluck('name', 'code'))
                                            ->searchable(),

                                        Forms\Components\Repeater::make('choice_options')
                                            ->label('Attributes')
                                            ->schema([
                                                Forms\Components\Select::make('attribute_id')
                                                    ->label('Attribute')
                                                    ->options(\App\Models\Attribute::all()->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('values', [])),

                                                Forms\Components\TagsInput::make('values')
                                                    ->label('Values')
                                                    ->placeholder('Press Enter to add values')
                                                    ->suggestions(function (Forms\Get $get) {
                                                        $attributeId = $get('attribute_id');
                                                        if (! $attributeId) {
                                                            return [];
                                                        }

                                                        return \App\Models\AttributeValue::where('attribute_id',
                                                            $attributeId)
                                                            ->pluck('value')
                                                            ->toArray();
                                                    }),
                                            ])
                                            ->itemLabel(fn (array $state
                                            ): ?string => \App\Models\Attribute::find($state['attribute_id'] ?? null)?->name ?? null),

                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('generate_variants')
                                                ->label('Generate Variants')
                                                ->icon('heroicon-o-arrow-path')
                                                ->action(function (Forms\Get $get, Forms\Set $set) {
                                                    $colors = $get('colors') ?? [];
                                                    $choiceOptions = $get('choice_options') ?? [];
                                                    $colorsActive = $get('variant_product') && ! empty($colors);

                                                    $options = [];
                                                    // 1. Add colors if active
                                                    if ($colorsActive) {
                                                        $options[] = $colors;
                                                    }

                                                    // 2. Add choice options (values)
                                                    foreach ($choiceOptions as $option) {
                                                        if (! empty($option['values'])) {
                                                            $options[] = $option['values'];
                                                        }
                                                    }

                                                    // 3. Generate combinations
                                                    $combinations = (new \AizPackages\CombinationGenerate\Services\CombinationService)->generate_combination($options);

                                                    // 4. Prepare new stock items, preserving existing values
                                                    $oldStocks = $get('stocks') ?? [];
                                                    $oldStocksByVariant = collect($oldStocks)->keyBy('variant')->toArray();

                                                    $newStocks = [];
                                                    foreach ($combinations as $combination) {
                                                        // Generate variant string
                                                        $str = '';
                                                        foreach ($combination as $key => $item) {
                                                            if ($key > 0) {
                                                                $str .= '-'.str_replace(' ', '', $item);
                                                            } else {
                                                                if ($colorsActive) {
                                                                    $colorName = \App\Models\Color::where('code',
                                                                        $item)->first()?->name;
                                                                    $str .= $colorName;
                                                                } else {
                                                                    $str .= str_replace(' ', '', $item);
                                                                }
                                                            }
                                                        }

                                                        // Check if this variant already exists
                                                        if (isset($oldStocksByVariant[$str])) {
                                                            $newStocks[] = $oldStocksByVariant[$str];
                                                        } else {
                                                            $newStocks[] = [
                                                                'variant' => $str,
                                                                'price' => $get('unit_price') ?? 0,
                                                                'sku' => ($get('sku') ? $get('sku').'-' : '').$str,
                                                                'qty' => 10,
                                                            ];
                                                        }
                                                    }

                                                    $set('stocks', $newStocks);
                                                }),
                                        ]),

                                        Forms\Components\Repeater::make('stocks')
                                            ->label('Variants')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\TextInput::make('variant')
                                                    ->disabled()
                                                    ->required()
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required(),
                                                Forms\Components\TextInput::make('sku')
                                                    ->label('SKU'),
                                                Forms\Components\TextInput::make('qty')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required(),
                                                Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->directory('products/variants'),
                                            ])
                                            ->columns(6)
                                            ->reorderable(false)
                                            ->addable(false)
                                            ->deletable(false),
                                    ]),
                            ])->id('variations-tab'),

                        // ── SEO Tab ──
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(255)
                                    ->rows(3),
                            ]),

                        // ── Shipping Tab ──
                        Tabs\Tab::make('Shipping')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\Toggle::make('cash_on_delivery')
                                    ->label('Cash On Delivery Status'),
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

                        // ── Status Tab ──
                        Tabs\Tab::make('Status')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Forms\Components\Toggle::make('published')
                                    ->label('Published')
                                    ->default(true),
                                Forms\Components\Toggle::make('featured')
                                    ->label('Featured'),
                                Forms\Components\Toggle::make('todays_deal')
                                    ->label('Today\'s Deal'),
                                Forms\Components\Toggle::make('approved')
                                    ->label('Approved')
                                    ->default(true)
                                    ->visible(fn () => auth()->user()->can('approve_products')),
                            ]),

                        // ── Videos Tab ──
                        Tabs\Tab::make('Videos')
                            ->icon('heroicon-o-video-camera')
                            ->schema([
                                Forms\Components\Select::make('video_provider')
                                    ->options([
                                        'youtube' => 'Youtube',
                                        'dailymotion' => 'Dailymotion',
                                        'vimeo' => 'Vimeo',
                                    ]),
                                Forms\Components\TextInput::make('video_link')
                                    ->placeholder('Video Link'),
                            ]),

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
                Tables\Columns\TextColumn::make('main_category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Qty')
                    ->sortable(),
                Tables\Columns\IconColumn::make('published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('featured')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
}
