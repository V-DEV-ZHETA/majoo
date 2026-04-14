<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\Weight\Icons;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'polaris-product-cost-icon';

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}

    protected static ?string $navigationLabel = 'Kelola Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 2,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->suffixIcon('heroicon-o-tag')
                                ->label('Nama Produk')
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('sku')
                                ->label('Barcode/SKU')
                                ->suffixIcon('fas-barcode')
                                ->unique(ignoreRecord: true),
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->label('Kategori')
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),
                        
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->label('Harga')
                            ->prefix('Rp ')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->label('Stok')
                            ->suffixIcon('heroicon-o-cube')
                            ->default(0)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Gambar & Status')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products/images')
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_available')
                            ->label('Status: Tersedia')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->size(48),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Produk')
                    ->sortable()
                    ->description(fn (Product $record) => $record->description ? substr($record->description, 0, 50) . '...' : ''),
                TextColumn::make('sku')
                    ->copyable()
                    ->label('SKU')
                    ->copyMessage('SKU copied'),
                TextColumn::make('category.name')
                    ->badge()
                    ->label('Kategori')
                    ->color('info'),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn (string $state): string => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                BadgeColumn::make('stock')
                    ->colors([
                        'danger' => 0,
                        'warning' => fn ($state) => $state <= 5,
                        'success' => fn ($state) => $state > 5,
                    ]),
                IconColumn::make('is_available')
                    ->boolean()
                    ->label('Status Produk')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload()
                    ->label('Kategori'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
