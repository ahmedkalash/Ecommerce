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

    protected static ?string $navigationGroup = NavigationGroups::CATALOG;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('CategoryTabs')
                    ->tabs([
                        // ── General Tab ──
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Category Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
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
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Category::class, 'slug', ignoreRecord: true),
                                    ])
                                    ->columns(2),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Organization')
                                            ->schema([
                                                SelectTree::make('parent_id')
                                                    ->relationship('parentCategory', 'name', 'parent_id')
                                                    ->label('Parent Category')
                                                    ->placeholder('None (Root Category)')
                                                    ->enableBranchNode()
                                                    ->expandSelected()
                                                    ->withCount()
                                                    ->searchable(),

                                            ])
                                            ->columnSpan(1),

                                        Forms\Components\Section::make('Flags')
                                            ->schema([
                                                Forms\Components\Toggle::make('featured')
                                                    ->label('Featured')
                                                    ->default(false),
                                                Forms\Components\Toggle::make('top')
                                                    ->label('Top Category')
                                                    ->default(false),
                                                Forms\Components\Toggle::make('digital')
                                                    ->label('Digital Products')
                                                    ->default(false),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        // ── Media Tab ──
                        Tabs\Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('banner')
                                    ->collection('banner')
                                    ->label('Banner Image')
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Large banner image displayed on category page'),
                                SpatieMediaLibraryFileUpload::make('icon')
                                    ->collection('icon')
                                    ->label('Icon Image')
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Small icon displayed in navigation/menus'),
                                SpatieMediaLibraryFileUpload::make('cover_image')
                                    ->collection('cover_image')
                                    ->label('Cover Image')
                                    ->image()
                                    ->imageEditor()
                                    ->helperText('Cover image for category cards'),
                            ])->columns(3),

                        // ── Discount & Commission Tab ──
                        // ── SEO Tab ──
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255)
                                    ->helperText('Leave empty to use category name'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(255)
                                    ->rows(3)
                                    ->helperText('Leave empty to auto-generate'),
                            ]),

                        // ── Advanced Tab ──
                        Tabs\Tab::make('Advanced')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Commission')
                                    ->compact()
                                    ->schema([
                                        Forms\Components\TextInput::make('commision_rate')
                                            ->label('Commission Rate (%)')
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100),
                                    ]),

                                Forms\Components\Section::make('Refund Settings')
                                    ->compact()
                                    ->schema([
                                        Forms\Components\TextInput::make('refund_request_time')
                                            ->label('Refund Request Time (days)')
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
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string => $record->slug ?? ''),
                Tables\Columns\TextColumn::make('parentCategory.name')
                    ->label('Parent')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Root Category'),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\IconColumn::make('featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('top')
                    ->label('Top')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Featured Categories'),
                Tables\Filters\TernaryFilter::make('top')
                    ->label('Top Categories'),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parentCategory', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(fn (Category $record) => app(\App\Services\CategoryService::class)->delete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn (\Illuminate\Support\Collection $records
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
