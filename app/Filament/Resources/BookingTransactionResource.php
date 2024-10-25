<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use App\Models\BookingTransaction;
use App\Models\Workshop;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and price')
                    ->schema([
                        Forms\Components\Select::make('workshop_id')
                            ->relationship('workshop', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set){
                                $workshop = Workshop::find($state);
                                $set('price', $workshop ? $workshop->price : 0);
                            })
                            ->afterStateHydrated(function($state, callable $get, callable $set){
                                $workshop = Workshop::find($state);
                                $set('price', $workshop ? $workshop->price : 0);
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->prefix('Qty People')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set){
                                $price = $get('price');
                                $subTotal = $price * $state;
                                $totalPpn = $subTotal * 0.11;
                                $totalAmount = $subTotal + $totalPpn;

                                $set('total_amount', $totalAmount);

                                $participants = $get('participants') ?? [];
                                $currentCount = count($participants);

                                if($state > $currentCount){
                                    for($i = $currentCount; $i < $state; $i++){
                                        $participants[] = ['name' => '', 'occupation' => '', 'email' => ''];
                                    }
                                } else {
                                    $participants = array_slice($participants, 0, $state);
                                }

                                $set('participants', $participants);
                            })
                            ->afterStateHydrated(function($state, callable $get, callable $set){
                                $price = $get('price');
                                $subTotal = $price * $state;
                                $totalPpn = $subTotal * 0.11;
                                $totalAmount = $subTotal + $totalPpn;

                                $set('total_amount', $totalAmount);
                            }),
                        Forms\Components\TextInput::make('total_amount')
                        ->required()
                        ->numeric()
                        ->prefix('IDR')
                        ->readOnly()
                        ->helperText('Harga sudah include PPN 11%'),

                        Repeater::make('participants')
                            ->schema([
                                Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                    ->label('Participants Name')
                                        ->required(),
                                    Forms\Components\TextInput::make('occupation')
                                        ->label('Occupation')
                                        ->required(),
                                    Forms\Components\TextInput::make('email')
                                        ->label('Email')
                                        ->required()
                                        ->email(),
                                ]),
                            ])
                            ->columns(1)
                            ->label('Participants Details'),
                    ]),

                Forms\Components\Wizard\Step::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')->required()->email(),
                        Forms\Components\TextInput::make('phone')->required()->numeric(),

                        Forms\Components\Select::make('customer_bank_name')->required()
                            ->options([
                                'BCA' => 'BCA',
                                'Mandiri' => 'Mandiri',
                                'BNI' => 'BNI',
                                'BSI' => 'BSI',
                            ]),

                        Forms\Components\TextInput::make('customer_bank_account')->required(),
                        Forms\Components\TextInput::make('customer_bank_number')->required(),
                        Forms\Components\TextInput::make('booking_trx_id')->required()
                    ]),

                Forms\Components\Wizard\Step::make('Payment Information')
                    ->schema([
                        ToggleButtons::make('is_paid')
                            ->label('Apakah Sudah Membayar ?')
                            ->boolean()
                            ->grouped()
                            ->icons([
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            ])->required(),

                        Forms\Components\FileUpload::make('proof')
                            ->image()
                            ->required(),
                    ])
                ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('workshop.thumbnail'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')->searchable(),

                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Terverivikasi')
            ])
            ->filters([
                SelectFilter::make('workshop_id')
                    ->label('workshop')
                        ->relationship('workshop', 'name'),
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
