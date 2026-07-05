<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ProductResource::class;

    /** Variant-owned form values, stashed between the two lifecycle hooks below. */
    protected array $variantData = [];

    protected ?string $imagePath = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Runs before the product row is created: peel the variant-owned fields out of the
     * form data (so they aren't mass-assigned onto Product, which has no such columns)
     * and stash them for afterCreate().
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->variantData = ProductResource::extractVariantData($data);
        $this->imagePath = $data['primary_image'] ?? null;
        unset($data['primary_image']);
        return $data;
    }

    /**
     * Runs after the product exists: create its single default variant with the
     * price/stock/sale values we stashed.
     */
    protected function afterCreate(): void
    {
        $this->record->defaultVariant()->create($this->variantData + [
            'is_active' => true,
            'position' => 0,
        ]);

        if ($this->imagePath !== null) {
            $this->record->media()->create([
                'path' => $this->imagePath,
                'type' => 'image',
                'is_primary' => true,
                'position' => 0,
            ]);
        }
    }
}
