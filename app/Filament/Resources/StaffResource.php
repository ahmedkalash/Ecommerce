<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\StaffResource\Pages;
use App\Models\Admin;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::USER_MANAGEMENT->getLocalizedLabel();
    }

    public static function getModelLabel(): string
    {
        return __('admin/resources.staff.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin/resources.staff.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/navigation.staff');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('admin/resources.general.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('admin/resources.general.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('phone')
                    ->label(__('admin/resources.general.phone'))
                    ->tel()
                    ->maxLength(255),

                Select::make('user_type')
                    ->label(__('admin/resources.staff.user_type'))
                    ->options([
                        UserType::ADMIN->value => 'admin',
                        UserType::STAFF->value => 'staff',
                    ])
                    ->required()
                    ->default(UserType::STAFF->value),

                TextInput::make('password')
                    ->label(__('admin/resources.general.password'))
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),

                Select::make('roles')
                    ->label(__('admin/resources.staff.roles'))
                    ->multiple()
                    ->preload()
                    ->options(fn () => Role::all()->pluck('name', 'id'))
                    ->searchable(),
                // DONT use relationship() per project rules. Let DTO and Service handle it.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin/resources.general.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('admin/resources.general.email'))
                    ->searchable(),
                TextColumn::make('user_type')
                    ->label(__('admin/resources.staff.user_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        UserType::ADMIN->value => 'danger',
                        UserType::STAFF->value => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('roles.name')
                    ->label(__('admin/resources.staff.roles'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('admin/resources.general.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('admin/actions.general.edit')),
                Tables\Actions\DeleteAction::make()->label(__('admin/actions.general.delete')),
                Tables\Actions\ViewAction::make()->label(__('admin/actions.general.view')),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
