<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')->relationship('category', 'name')->searchable()->preload()->required()->label('Kategori'),
                TextInput::make('name')->required()->maxLength(255)->label('Nama Produk'),
                TextInput::make('price')->required()->numeric()->prefix('Rp')->label('Harga'),
                TextInput::make('stock')->required()->numeric()->default(0)->label('Stok'),
                FileUpload::make('image_path')->image()->directory('products')->label('Foto Produk'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->label('Foto'),
                TextColumn::make('name')->label('Nama Produk')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori')->searchable()->sortable()->badge()->color('info'),
                TextColumn::make('price')->label('Harga')->money('IDR')->sortable(),
                TextColumn::make('stock')->label('Stok')->sortable()->badge()
                    ->color(fn ($state): string => match (true) {
                        $state == 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name')->label('Filter Kategori'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}