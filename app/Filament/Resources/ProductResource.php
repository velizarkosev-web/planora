<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Продукти';

    protected static ?string $modelLabel = 'продукт';

    protected static ?string $pluralModelLabel = 'продукти';

    /** The two editorial states, with their Bulgarian labels. */
    public const STATES = [
        'draft' => 'Чернова',
        'published' => 'Публикуван',
    ];

    /**
     * Variant-owned fields that appear on the product form but must be saved onto the
     * hidden default variant instead of the product. See the Create/Edit pages.
     */
    public const VARIANT_KEYS = ['price', 'stock_quantity', 'sale_price', 'sale_starts_at', 'sale_ends_at'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна информация')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Категория')
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Име')
                            ->required()
                            ->maxLength(255)
                            ->rules(['regex:/\p{L}/u'])
                            ->validationMessages(['regex' => 'Форматът на полето Име е невалиден.']),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL адрес')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Цена и наличност')
                    ->description('Тези стойности се записват към основния вариант на продукта.')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Цена (€)')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Наличност')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('Промоцена (€)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Оставете празно, ако няма промоция.'),

                        Forms\Components\DateTimePicker::make('sale_starts_at')
                            ->label('Промоция от')
                            ->seconds(false)
                            ->timezone('Europe/Sofia'),

                        Forms\Components\DateTimePicker::make('sale_ends_at')
                            ->label('Промоция до')
                            ->seconds(false)
                            ->timezone('Europe/Sofia')
                            ->after('sale_starts_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Характеристики')
                    ->schema([
                        Forms\Components\KeyValue::make('specs')
                            ->label('Характеристики')
                            ->keyLabel('Характеристика')
                            ->valueLabel('Стойност')
                            ->addActionLabel('Добави характеристика'),
                    ]),

                Forms\Components\Section::make('Снимки')
                    ->schema([
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Снимки')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('products')
                            ->imageEditor()
                            ->maxSize(20480)
                            ->helperText('Първата снимка е основната — тя се показва в списъци и на началната страница. Плъзнете, за да пренаредите.'),
                    ]),

                Forms\Components\Section::make('Публикуване')
                    ->schema([
                        Forms\Components\Select::make('state')
                            ->label('Състояние')
                            ->options(self::STATES)
                            ->default('draft')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Статус')
                            ->default(true),

                        Forms\Components\TextInput::make('position')
                            ->label('Подредба')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryMedia.path')
                    ->label('Снимка')
                    ->disk('public')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Име')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категория')
                    ->sortable(),

                Tables\Columns\TextColumn::make('defaultVariant.price')
                    ->label('Цена')
                    ->money('EUR', divideBy: 100),

                Tables\Columns\TextColumn::make('defaultVariant.stock_quantity')
                    ->label('Наличност'),

                Tables\Columns\TextColumn::make('state')
                    ->label('Състояние')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATES[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Активен' : 'Неактивен')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('position')
                    ->label('Подредба')
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->searchDebounce('750ms')
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Състояние')
                    ->options(self::STATES),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Статус')
                    ->placeholder('Всички')
                    ->trueLabel('Активни')
                    ->falseLabel('Неактивни'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
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

    /**
     * The read-only "Преглед" (View) screen. Uses TextEntry (the infolist twin of a
     * table column) and pulls price/stock straight from the defaultVariant relationship —
     * no lifecycle hooks needed here, because we're only displaying, not editing.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Основна информация')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('Име'),
                        Infolists\Components\TextEntry::make('category.name')->label('Категория'),
                        Infolists\Components\TextEntry::make('slug')->label('URL адрес'),
                        Infolists\Components\TextEntry::make('description')->label('Описание')->placeholder('—'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Снимка')
                    ->schema([
                        Infolists\Components\ImageEntry::make('primaryMedia.path')
                            ->label('Основна снимка')
                            ->disk('public'),
                    ]),

                Infolists\Components\Section::make('Цена и наличност')
                    ->schema([
                        Infolists\Components\TextEntry::make('defaultVariant.price')
                            ->label('Цена')->money('EUR', divideBy: 100),
                        Infolists\Components\TextEntry::make('defaultVariant.stock_quantity')
                            ->label('Наличност'),
                        Infolists\Components\TextEntry::make('defaultVariant.sale_price')
                            ->label('Промоцена')->money('EUR', divideBy: 100)->placeholder('—'),
                        Infolists\Components\TextEntry::make('defaultVariant.sale_starts_at')
                            ->label('Промоция от')->dateTime('d.m.Y H:i')->timezone('Europe/Sofia')->placeholder('—'),
                        Infolists\Components\TextEntry::make('defaultVariant.sale_ends_at')
                            ->label('Промоция до')->dateTime('d.m.Y H:i')->timezone('Europe/Sofia')->placeholder('—'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Характеристики')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('specs')
                            ->label('Характеристики'),
                    ]),

                Infolists\Components\Section::make('Публикуване')
                    ->schema([
                        Infolists\Components\TextEntry::make('state')
                            ->label('Състояние')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => self::STATES[$state] ?? $state)
                            ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray'),
                        Infolists\Components\TextEntry::make('is_active')
                            ->label('Статус')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Активен' : 'Неактивен')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                    ])
                    ->columns(2),
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

    // ── Default-variant helpers ─────────────────────────────────────────────────
    // Price fields are shown in euros but stored as integer cents. These helpers
    // convert between the two and split the variant-owned keys out of the form data.

    public static function toCents(mixed $euros): ?int
    {
        return ($euros === null || $euros === '') ? null : (int) round(((float) $euros) * 100);
    }

    public static function toEuros(?int $cents): ?string
    {
        return $cents === null ? null : number_format($cents / 100, 2, '.', '');
    }

    /**
     * Pull the variant-owned fields out of $data (mutating it) and return them,
     * converted to their DB shape (cents for money).
     */
    public static function extractVariantData(array &$data): array
    {
        $variant = [
            'price' => self::toCents($data['price'] ?? null),
            'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
            'sale_price' => self::toCents($data['sale_price'] ?? null),
            'sale_starts_at' => $data['sale_starts_at'] ?? null,
            'sale_ends_at' => $data['sale_ends_at'] ?? null,
        ];

        Arr::forget($data, self::VARIANT_KEYS);

        return $variant;
    }

    /**
     * Load the default variant's values back into the form data (in euros) for editing.
     */
    public static function injectVariantData(array $data, Product $product): array
    {
        $variant = $product->defaultVariant;

        $data['price'] = self::toEuros($variant?->price);
        $data['stock_quantity'] = $variant?->stock_quantity ?? 0;
        $data['sale_price'] = self::toEuros($variant?->sale_price);
        $data['sale_starts_at'] = $variant?->sale_starts_at;
        $data['sale_ends_at'] = $variant?->sale_ends_at;

        return $data;
    }

    /**
     * The gallery paths (ordered) for the edit form. FileUpload's afterStateHydrated
     * wraps this list into its internal [uuid => path] shape for us.
     */
    public static function galleryFormState(Product $product): array
    {
        return $product->media()->orderBy('position')->pluck('path')->all();
    }

    /**
     * Pull the ordered gallery paths out of submitted form data (mutating it).
     * A multiple FileUpload hands us an array (possibly keyed) — return a plain ordered list.
     */
    public static function extractGallery(array &$data): array
    {
        $gallery = $data['gallery'] ?? [];
        unset($data['gallery']);

        return array_values(is_array($gallery) ? $gallery : []);
    }

    /**
     * Reconcile a product's media rows to match the given ordered paths: drop removed
     * images, upsert the rest with their new position, and mark the first as primary.
     */
    public static function syncGallery(Product $product, array $paths): void
    {
        $paths = array_values($paths);

        // Remove media whose file is no longer in the submitted set.
        $product->media()->whereNotIn('path', $paths)->delete();

        foreach ($paths as $index => $path) {
            $product->media()->updateOrCreate(
                ['path' => $path],
                ['type' => 'image', 'is_primary' => $index === 0, 'position' => $index],
            );
        }
    }
}
