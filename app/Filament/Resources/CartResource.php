<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\User;
use App\Models\Product;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('customer', 'email'),
                Forms\Components\Select::make('product_id')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->relationship('product', 'name'),
                Forms\Components\TextInput::make('qty')
                    ->required()
                    ->maxLength(255)
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SelectColumn::make('customer_id')
                    ->label('Customer')
                    ->options(User::pluck('email', 'id')->toArray())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('qty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->getStateUsing(function ($record) {

                        return $record->product->price;
                        
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price')
                    ->getStateUsing(function ($record) {
                        
                        return $record->qty * $record->product->price; // Calculate total price

                    })
                    ->searchable(),
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
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }
}
