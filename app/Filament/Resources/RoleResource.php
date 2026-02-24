<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    public static function getModelLabel(): string
    {
        return __('Role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Role Name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        Forms\Components\select::make('guard_name')
                            ->label('Guard Name')
                            ->default('admin')
                            ->nullable()
                            ->options([
                                'admin' => 'Admin',
                                'web' => 'Web',
                            ]),

                        Forms\Components\Select::make('permissions')
                            ->multiple()
                            ->options(function () {
                                $groups = config('permissions.permissions', []);

                                return array_map(function ($permissionNames) {
                                    return Permission::whereIn('name', $permissionNames)
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }, $groups);
                            })
                            ->preload()
                            ->searchable()
                            ->label('Permissions')
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
                    ->label('Role')
                    ->colors(['primary'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->label('Guard'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label('Permissions')
                    ->counts('permissions')
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
