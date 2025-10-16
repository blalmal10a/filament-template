<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),

                FileUpload::make('image')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->imageEditor()
                    ->imagePreviewHeight(100)
                    ->dehydrated(false)
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
                    ->reactive(),
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
}
