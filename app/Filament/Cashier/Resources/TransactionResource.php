<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Exports\TransactionExporter;
use App\Filament\Cashier\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('outlet_id')
                    ->label("Outlet")
                    ->options(auth()->user()->cashier_outlet->pluck("name","id"))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('package_id', null);
                        $set('cashier_id', null);
                    }),
                Forms\Components\Select::make('cashier_id')
                    ->options(fn(Get $get) => User::where("id",auth()->user()->id)->pluck("name", "id"))
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'name')
                    ->required(),
                Forms\Components\Select::make('package_id')
                    ->required()
                    ->relationship('package', 'name', function ($query, Get $get) {
                        $outletID = $get("outlet_id");
                        if ($outletID) {
                            return $query->where("outlet_id", $outletID);
                        }
                        return $query;
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $price = 0;
                        $package = Package::find($state);
                        if (!empty($package)) {
                            $price = $package->price;
                        }
                        $set("base_price", $price);
                        self::calculateTotal($set, $get);
                    }),
                Forms\Components\TextInput::make('additional_costs')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => self::calculateTotal($set, $get)), // Hitung total setelah perubahan selesai
                Forms\Components\TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => self::calculateTotal($set, $get)),
                Forms\Components\TextInput::make('tax')
                    ->required()
                    ->numeric()
                    ->default(11)
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => self::calculateTotal($set, $get)),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(fn($state, Set $set, Get $get) => self::calculateTotal($set, $get)),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('deadline')
                    ->required(),
                Forms\Components\Toggle::make("is_payed")->required(),
                Forms\Components\Select::make("status")
                ->options([
                    "new" => "New",
                    "process" => "Process",
                    "retrieved" => "Retrieved"
                ])
            ]);
    }

    // Refactor calculateTotal untuk memperhitungkan base_price dan total baru
    protected static function calculateTotal(Set $set, Get $get)
    {
        $basePrice = (float) ($get('base_price') ?? 0);
        $additionalCosts = (float) ($get('additional_costs') ?? 0);
        $discountPercentage = (float) ($get('discount') ?? 0); // Discount in percentage
        $taxPercentage = (float) ($get('tax') ?? 0); // Tax in percentage
        $quantity = (int) ($get('quantity') ?? 1);

        // Hitung subtotal berdasarkan harga dasar dan biaya tambahan
        $subtotal = ($basePrice + $additionalCosts) * $quantity;

        // Hitung diskon sebagai persentase dari subtotal
        $discountAmount = $subtotal * ($discountPercentage / 100);

        // Hitung total setelah diskon
        $subtotalAfterDiscount = $subtotal - $discountAmount;

        // Hitung pajak sebagai persentase dari subtotal setelah diskon
        $taxAmount = $subtotalAfterDiscount * ($taxPercentage / 100);

        // Total akhir dengan menambahkan pajak
        $total = $subtotalAfterDiscount + $taxAmount;

        // Set total
        $set('total', $total);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('outlet.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cashier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('additional_costs')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_payed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
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
                DateRangeFilter::make("created_at")
                    ->label("Created At"),
                SelectFilter::make("status")
                    ->options([
                        "new" => "New",
                        "process" => "Process",
                        "retrieved" => "Retrieved"
                    ]),
                BooleanFilter::make("is_payed")
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(TransactionExporter::class),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
