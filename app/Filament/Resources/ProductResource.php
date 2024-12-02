<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Set;

use Illuminate\Support\Str;

use App\Models\Category;
use App\Models\Brand;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state)=> $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->default(null)
                    ->prefix('₹'),
                Forms\Components\Select::make('category_id')
                    ->preload()
                    ->searchable()
                    ->relationship('category', 'name'),
                Forms\Components\Select::make('brand_id')
                    ->preload()
                    ->searchable()
                    ->relationship('brand', 'name'),
                Forms\Components\FileUpload::make('image_path')
                    ->image(),
                Forms\Components\TextInput::make('qty')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('alert_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('name')
                    ->label('Name')
                    ->searchable()                    
                    ->afterStateUpdated(function ($state, $record) {
                        // Automatically update the slug
                        $record->update(['slug' => Str::slug($state)]);
                    }),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\SelectColumn::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('brand_id')
                    ->label('Brand')
                    ->options(Brand::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_path')
                    ->getStateUsing(fn($record) => $record->GetImagePath()),
                Tables\Columns\TextInputColumn::make('qty')                    
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('alert_stock')                    
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
