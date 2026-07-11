<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = ProductResource::class;

    /** Variant-owned form values, stashed between the two lifecycle hooks below. */
    protected array $variantData = [];

    protected array $galleryPaths = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * When opening the edit form: read the default variant's values back into the form
     * (converted to euros) so the shop owner sees the current price/stock/sale.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = ProductResource::injectVariantData($data, $this->record);
        $data['gallery'] = ProductResource::galleryFormState($this->record);

        return $data;
    }

    /**
     * Before saving the product: peel the variant-owned fields back out and stash them.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->variantData = ProductResource::extractVariantData($data);
        $this->galleryPaths = ProductResource::extractGallery($data);

        return $data;
    }

    /**
     * After saving the product: create-or-update its default variant with the new values.
     */
    protected function afterSave(): void
    {
        $this->record->defaultVariant()->updateOrCreate([], $this->variantData + [
            'is_active' => true,
            'position' => 0,
        ]);

        ProductResource::syncGallery($this->record, $this->galleryPaths);
    }
}
