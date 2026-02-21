<?php

namespace App\Filament\Resources;

use App\Enums\Coupons\CouponDiscountTypes;
use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = NavigationGroups::CATALOG;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('type')
                            ->options(\App\Enums\Coupons\CouponTypes::class)
                            ->required()
                            ->reactive(),
                    ])->columns(2),

                Section::make('Discount Settings')
                    ->schema([
                        TextInput::make('discount')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        Select::make('discount_type')
                            ->options(CouponDiscountTypes::class)
                            ->required(),
                        DatePicker::make('start_date')
                            ->required()
                            ->date(),
                        DatePicker::make('end_date')
                            ->required()
                            ->after('start_date')
                            ->date(),
                    ])->columns(2),

                Section::make('Restrictions')
                    ->schema([
                        TextInput::make('min_money_spent')
                            ->numeric()
                            ->label('Minimum Spend')
                            ->minValue(0),
                        TextInput::make('max_discount')
                            ->numeric()
                            ->label('Maximum Discount Amount')
                            ->minValue(0),
                        TextInput::make('usage_limit')
                            ->numeric()
                            ->label('Total Usage Limit')
                            ->minValue(1),
                    ])->columns(3),

                Section::make('Product Selection')
                    ->schema([
                        Select::make('product_ids')
                            ->label('Select Products')
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => \App\Models\Product::where('name', 'like',
                                "%{$search}%")->limit(50)->pluck('name', 'id'))
                            ->getOptionLabelsUsing(fn (array $values) => \App\Models\Product::whereIn('id',
                                $values)->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->hidden(function (\Filament\Forms\Get $get) {
                        $type = $get('type');
                        // In edit mode it might be an Enum, in create it might be the value
                        $value = $type instanceof \App\Enums\Coupons\CouponTypes ? $type->value : $type;

                        return $value !== \App\Enums\Coupons\CouponTypes::PRODUCT_BASED->value;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => \App\Enums\Coupons\CouponTypes::CART_BASED->value,
                        'success' => \App\Enums\Coupons\CouponTypes::PRODUCT_BASED->value,
                    ]),
                TextColumn::make('discount')
                    ->formatStateUsing(fn (
                        $state,
                        $record
                    ) => $state.($record->discount_type === \App\Enums\Coupons\CouponDiscountTypes::PERCENTAGE->value ? '%' : ' Flat')),
                TextColumn::make('start_date')
                    ->dateTime(),
                TextColumn::make('end_date')
                    ->dateTime(),
                TextColumn::make('used_count')
                    ->label('Usage'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
