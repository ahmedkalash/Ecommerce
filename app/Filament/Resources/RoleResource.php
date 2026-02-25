<?php

namespace App\Filament\Resources;

use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::USER_MANAGEMENT->getLocalizedLabel();
    }

    public static function getModelLabel(): string
    {
        return __('admin/resources.role.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin/resources.role.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/navigation.roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('admin/resources.role.name'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('guard_name')
                            ->label(__('admin/resources.role.guard_name'))
                            ->default('admin')
                            ->nullable()
                            ->options([
                                'admin' => 'Admin',
                                'web' => 'Web',
                            ]),

                        Forms\Components\Select::make('permissions')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->saveRelationshipsUsing(fn () => null)
                            ->dehydrated()
                            ->preload()
                            ->searchable()
                            ->label(__('admin/resources.role.permissions'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->label(__('admin/resources.role.col_role'))
                    ->colors(['primary'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->label(__('admin/resources.role.col_guard')),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('admin/resources.role.col_permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin/resources.general.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('admin/actions.general.edit')),
                Tables\Actions\DeleteAction::make()->label(__('admin/actions.general.delete')),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
