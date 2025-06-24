<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoardingHouseResource\Pages;
use App\Filament\Resources\BoardingHouseResource\RelationManagers;
use App\Models\BoardingHouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoardingHouseResource extends Resource
{
    protected static ?string $model = BoardingHouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Boarding House Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informasi Umum')
                            ->schema([
                                Forms\Components\FileUpload::make('thumbnail')
                                    ->image()
                                    ->directory('boarding_houses')
                                    ->required(),

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->debounce(10)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required(),

                                Forms\Components\Select::make('city_id')
                                    ->relationship('city', 'name')
                                    // ->content(fn ($record) => $record->city_id->name )
                                    ->required(),

                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required(),

                                Forms\Components\RichEditor::make('description')
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required(),

                                Forms\Components\Textarea::make('address')
                                    ->required()
                            ]),

                        Forms\Components\Tabs\Tab::make('Bonus Ngekos')
                            ->schema([
                                Forms\Components\Repeater::make('bonuses')
                                    ->relationship('bonuses')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->image()
                                            ->required()
                                            ->directory('boarding_houses/bonuses'),
                                        Forms\Components\TextInput::make('name')
                                            ->required(),

                                        Forms\Components\Textarea::make('description')
                                            ->required(),
                                    ])
                            ]),

                        Forms\Components\Tabs\Tab::make('Kamar')
                            ->schema([
                                Forms\Components\Repeater::make('rooms')
                                    ->relationship('rooms')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\TextInput::make('room_type')
                                            ->required(),
                                        Forms\Components\TextInput::make('square_feet')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('capacity')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('price_per_month')
                                            ->required()
                                            ->prefix('IDR')
                                            ->numeric(),
                                        Forms\Components\Toggle::make('is_available')
                                            ->required(),

                                        Forms\Components\Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->required()
                                                    ->directory('boarding_houses/rooms'),
                                            ]),
                                    ])
                            ]),
                    ])
                    ->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->toggleable(),
                Tables\Columns\TextColumn::make('city.name'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\ImageColumn::make('thumbnail')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()

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
            'index' => Pages\ListBoardingHouses::route('/'),
            'create' => Pages\CreateBoardingHouse::route('/create'),
            'edit' => Pages\EditBoardingHouse::route('/{record}/edit'),
        ];
    }
}
