<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\OutletCashierResource\Pages;
use App\Filament\Resources\OutletCashierResource\RelationManagers;
use App\Models\OutletCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutletCashierResource extends Resource
{
    protected static ?string $model = OutletCashier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas("outlet",function($subQuery){
            return $subQuery->where("owner_id", auth()->user()->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cashier_id')
                ->relationship('cashier', 'name', function($query) {
                    return $query->whereDoesntHave('cashier_outlet')
                                 ->where('role', 'cashier');
                })
                ->required()
                ->searchable(),
                Forms\Components\Select::make('outlet_id')
                    ->relationship('outlet', 'name',function($query){
                        return $query->where("owner_id", auth()->user()->id);
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cashier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('outlet.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cashier.email')
                    ->numeric()
                    ->label("Email")
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
            'index' => Pages\ListOutletCashiers::route('/'),
            'create' => Pages\CreateOutletCashier::route('/create'),
            'edit' => Pages\EditOutletCashier::route('/{record}/edit'),
        ];
    }
}
