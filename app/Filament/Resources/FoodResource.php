<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->directory('foods')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('Rp')
                    ->reactive()
                    ->required(),
                Forms\Components\Checkbox::make('is_promo')
                    ->reactive(),
                Forms\Components\Select::make('percent')
                    ->options([
                        10 => '10%',
                        25 => '25%',
                        35 => '35%',
                        50 => '50%'
                    ])
                    ->columnSpanFull()
                    ->hidden(fn($get) => !$get('is_promo'))
                    ->afterStateUpdated(function ($set, $get, $state){
                        if($get('is_promo') && $get('price') && $get('percent')){
                            $discount = ($get('price') * (int)$get('percent')) /100;
                            $set('price_afterdiscount', $get('price') - $discount);
                        }else{
                            $set('price_afterdiscount', $get('price'));
                        }
                    })
                    ->reactive(),
                Forms\Components\TextInput::make('price_afterdiscount')
                    ->prefix('Rp')
                    ->numeric()
                    ->columnSpanFull()
                    ->reactive()
                    ->readOnly()
                    ->hidden(fn($get) => !$get('is_promo'))
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->columnSpanFull()
                    ->required()
                    ->relationship('categories','name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('images'),
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('price_afterdiscount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('percent')
                    ->label('Diskon')
                    ->sortable(),
                TextColumn::make('is_promo')
                    ->label("Promo")
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->label('Nama Kategori')
                    ->searchable(),
                TextColumn::make("created_at")
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make("updated_at")
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }
}
