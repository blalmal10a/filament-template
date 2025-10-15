<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\CustomFileUpload;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    // ->required()
                    ->maxLength(255),
                // Section::make([
                //     CustomFileUpload::make('path')
                //         ->label('Profile picture')
                // ])->relationship('image'),
                FileUpload::make('image')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->imageEditor()
                    ->imagePreviewHeight(100)
                    // ->multiple()
                    ->dehydrated(false)
                    // ->minFiles(1)
                    ->saveRelationshipsUsing(function (FileUpload $component, $state) {
                        $record = $component->getRecord();
                        $record?->image()->delete();
                        foreach ($state ?? [] as $file) {
                            $record?->image()->create([
                                'path' => $file,
                            ]);
                        }
                    })
                    ->afterStateHydrated(function ($state, $record, $set) {
                        $data = $record?->image?->pluck('path', 'id')->toArray();
                        $set('image', $data);
                    }),
                Toggle::make('update_password')
                    ->hiddenOn('create')
                    ->reactive()
                //
                ,
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->hidden(function ($get, $record) {
                        if (!$record) return false;
                        return $get('update_password') === false;
                    })
                    ->maxLength(255),
                Section::make([
                    CheckboxList::make('roles')
                        ->relationship(
                            'roles',
                            'name',
                            fn($query)
                            => $query
                                ->whereNotIn('name', ['super_admin'])
                        )

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'super_admin');
                });
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
