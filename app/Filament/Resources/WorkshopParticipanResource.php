<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkshopParticipanResource\Pages;
use App\Filament\Resources\WorkshopParticipanResource\RelationManagers;
use App\Models\WorkshopParticipan;
use App\Models\WorkshopPartisipan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkshopParticipanResource extends Resource
{
    protected static ?string $model = WorkshopPartisipan::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('occupation')->required(),
                Forms\Components\TextInput::make('email')->required()->email(),

                Forms\Components\Select::make('workshop_id')
                    ->relationship('workshop', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('booking_transaction_id')
                    ->relationship('bookingTransaction', 'booking_trx_id')
                    ->searchable()
                    ->preload()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('workshop.thumbnail'),

                Tables\Columns\TextColumn::make('bookingTransaction.booking_trx_id')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('occupation')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
            ])
            ->filters([
                SelectFilter::make('workshop_id')
                    ->label('workshop')
                    ->relationship('workshop', 'name')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListWorkshopParticipans::route('/'),
            'create' => Pages\CreateWorkshopParticipan::route('/create'),
            'edit' => Pages\EditWorkshopParticipan::route('/{record}/edit'),
        ];
    }
}
