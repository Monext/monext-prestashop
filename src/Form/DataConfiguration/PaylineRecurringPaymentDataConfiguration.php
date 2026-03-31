<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataConfiguration;

use Configuration;
use Context;
use Product;
use Validate;

class PaylineRecurringPaymentDataConfiguration extends AbstractDataConfiguration
{

    public function getConfiguration(): array
    {
        return [
            'PAYLINE_SUBSCRIBE_ENABLE' => (bool) $this->configuration->get('PAYLINE_SUBSCRIBE_ENABLE'),
            'PAYLINE_SUBSCRIBE_TITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_SUBSCRIBE_TITLE'),
            'PAYLINE_SUBSCRIBE_SUBTITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_SUBSCRIBE_SUBTITLE'),
            'PAYLINE_SUBSCRIBE_START_DATE' => $this->configuration->get('PAYLINE_SUBSCRIBE_START_DATE'),
            'PAYLINE_SUBSCRIBE_NUMBER' => $this->configuration->get('PAYLINE_SUBSCRIBE_NUMBER'),
            'PAYLINE_SUBSCRIBE_PERIODICITY' => $this->configuration->get('PAYLINE_SUBSCRIBE_PERIODICITY'),
            'PAYLINE_SUBSCRIBE_DAY' => $this->configuration->get('PAYLINE_SUBSCRIBE_DAY'),
            'PAYLINE_SUBSCRIBE_PLIST' => $this->getProductsDataForForm(),
            'PAYLINE_SUBSCRIBE_EXCLUSIVE' => $this->configuration->get('PAYLINE_SUBSCRIBE_EXCLUSIVE'),
        ];
    }

    public function updateConfigurationField($configKey, $configValue)
    {
        if ($configKey === 'PAYLINE_SUBSCRIBE_PLIST') {
            $ids = $this->extractProductIdsFromFormData($configValue);
            parent::updateConfigurationField($configKey, implode(',', $ids));
        } else {
            parent::updateConfigurationField($configKey, $configValue);
        }
    }

    /**
     * Extract product IDs from form data with version compatibility.
     * @param mixed $formData
     * @return array Product IDs
     * @deprecated This function may be partially removed when the module no longer supports PrestaShop 1.7
     */
    private function extractProductIdsFromFormData($formData): array
    {
        if (!is_array($formData)) {
            return [];
        }

        // LEGACY: TypeaheadProductCollectionType (PS 1.7.8-8.x) format
        if (isset($formData['data']) && is_array($formData['data'])) {
            return array_filter(array_map('intval', $formData['data']));
        }

        // Modern: ProductSearchType (PS 9.x+) format
        return array_filter(array_column($formData, 'id'));
    }

    /**
     * Get products data for form display with automatic format detection.
     * @return array Products data in version-appropriate format
     * @deprecated This function may be partially removed when the module no longer supports PrestaShop 1.7
     */
    private function getProductsDataForForm(): array
    {
        $configValue = $this->configuration->get('PAYLINE_SUBSCRIBE_PLIST');
        if (empty($configValue)) {
            // Empty state: format depends on PrestaShop version
            return $this->supportsProductSearchType() ? [] : ['data' => []];
        }

        if ($this->supportsProductSearchType()) {
            // Modern (PS 8/9): ProductSearchType expects full product objects
            return $this->loadProductsFromIds($configValue);
        } else {
            // Legacy (PS 1.7.8): TypeaheadProductCollectionType expects just IDs
            // It will load product details itself in buildView()
            $productIds = array_map('intval', explode(',', $configValue));
            return ['data' => $productIds];
        }
    }

    /**
     * Check if ProductSearchType is available (PS 8/9+).
     * @return bool
     * @deprecated This function may be partially removed when the module no longer supports PrestaShop 1.7
     */
    private function supportsProductSearchType(): bool
    {
        return class_exists('PrestaShopBundle\Form\Admin\Type\ProductSearchType');
    }

    /**
     * Load product details from comma-separated IDs.
     * Used by ProductSearchType (PS 8/9) which expects full product objects.
     * @param string $configValue Comma-separated product IDs
     * @return array Array of product data with id, name, image
     */
    private function loadProductsFromIds(string $configValue): array
    {
        $productIds = array_map('intval', explode(',', $configValue));
        $context = Context::getContext();
        $langId = (int) $context->employee->id_lang;
        $products = [];

        foreach ($productIds as $idProduct) {
            if (empty($idProduct)) {
                continue;
            }
            $product = new Product($idProduct, false, $langId);
            if (Validate::isLoadedObject($product)) {
                $products[] = [
                    'id' => (int) $product->id,
                    'name' => $product->name . ' (ref: ' . $product->reference . ')',
                    'image' => $context->link->getImageLink($product->reference, $product->getCoverWs(), 'small_default'),
                ];
            }
        }

        return $products;
    }
}
