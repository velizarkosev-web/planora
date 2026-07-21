<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Bulgarian label for the sidebar (the shop owner works in bg).
    protected static ?string $navigationLabel = 'Категории';

    protected static ?string $modelLabel = 'категория';

    protected static ?string $pluralModelLabel = 'категории';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // A text input. Because the panel has the translatable plugin,
                // this field automatically follows the BG/EN locale switcher —
                // typing here saves into name->{"bg"} or name->{"en"}.
                Forms\Components\TextInput::make('name')
                    ->label('Име')
                    ->required()
                    ->maxLength(255)
                    ->rules(['regex:/\p{L}/u'])
                    ->validationMessages([
                        'regex' => 'Форматът на полето Име е невалиден.',
                    ]),

                Forms\Components\TextInput::make('slug')
                    ->label('URL адрес')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->label('Описание'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),

                Forms\Components\TextInput::make('position')
                    ->label('Подредба')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('Име')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Продукти'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Активен' : 'Неактивен')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->label('Подредба'),
            ])
            ->defaultSort('position')
            ->searchDebounce('750ms')
            ->filters([
                //
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
