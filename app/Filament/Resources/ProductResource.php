<?php

namespace App\Filament\Resources;

use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Services\ProductService;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                                                Forms\Components\TagsInput::make('tags')
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])
                            ->columns(3),

                        // ── Price & Stock Tab (Variations) ──
                        Tabs\Tab::make('Variants')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                // This repeater manages ALL stocks (variants).
                                Forms\Components\Repeater::make('stocks')
                                    ->label('Product Variants')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('variant')
                                                    ->label('Variant Name')
                                                    ->placeholder('e.g., Default, Red XL, 128GB')
                                                    ->default('Default')
                                                    ->required()
                                                    ->distinct() // Ensure variant names are unique within the repeater
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->unique('product_stocks', 'sku', ignoreRecord: true), // Ensure SKU is unique in DB

                                                Forms\Components\TextInput::make('price')
                                                    ->label('Price')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required(),

                                                Forms\Components\TextInput::make('qty')
                                                    ->label('Quantity')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required(),

                                                Forms\Components\TextInput::make('min_qty')
                                                    ->label('Min Qty')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required(),

                                                Forms\Components\Toggle::make('cash_on_delivery')
                                                    ->label('Cash On Delivery')
                                                    ->default(true)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),

                                        Forms\Components\Section::make('Media & Files')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        SpatieMediaLibraryFileUpload::make('thumbnail')
                                                            ->collection('thumbnail')
                                                            ->label('Variant Thumbnail')
                                                            ->image()
                                                            ->imageEditor(),

                                                        SpatieMediaLibraryFileUpload::make('video_thumbnail')
                                                            ->collection('video_thumbnail')
                                                            ->label('Video Thumbnail')
                                                            ->image()
                                                            ->imageEditor(),

                                                        SpatieMediaLibraryFileUpload::make('meta_img')
                                                            ->collection('meta_img')
                                                            ->label('Meta Image')
                                                            ->image()
                                                            ->imageEditor(),
                                                    ]),

                                                SpatieMediaLibraryFileUpload::make('gallery')
                                                    ->collection('gallery')
                                                    ->label('Variant Gallery')
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->image()
                                                    ->imageEditor()
                                                    ->panelLayout('grid')
                                                    ->columnSpanFull(),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        SpatieMediaLibraryFileUpload::make('short_video')
                                                            ->collection('short_video')
                                                            ->label('Short Video')
                                                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                                            ->maxSize(50000), // 50MB limit

                                                        SpatieMediaLibraryFileUpload::make('pdf')
                                                            ->collection('pdf')
                                                            ->label('PDF Specification')
                                                            ->acceptedFileTypes(['application/pdf'])
                                                            ->maxSize(10000), // 10MB limit
                                                    ]),

                                                SpatieMediaLibraryFileUpload::make('files')
                                                    ->collection('files')
                                                    ->label('Downloadable Files')
                                                    ->multiple()
                                                    ->columnSpanFull(),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('video_link')
                                                            ->label('External Video Link')
                                                            ->placeholder('https://youtube.com/watch?v=...'),

                                                        Forms\Components\Select::make('video_provider')
                                                            ->label('Video Provider')
                                                            ->options([
                                                                'youtube' => 'Youtube',
                                                                'dailymotion' => 'Dailymotion',
                                                                'vimeo' => 'Vimeo',
                                                            ]),
                                                    ]),
                                            ])
                                            ->collapsed()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->reorderable(true)
                                    ->addable(true)
                                    ->deletable(true)
                                    ->defaultItems(1)
                                    ->minItems(1)
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
                                    ->maxLength(255)
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
                    ->state(fn (Product $record) => $record->stocks->min('price')) // Calculate min price from stocks
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->label('Max Price')
                    ->state(fn (Product $record) => $record->stocks->max('price')) // Calculate min price from stocks
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Qty')
                    ->state(fn (Product $record) => $record->stocks->sum('qty')) // Calculate total qty
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
}
