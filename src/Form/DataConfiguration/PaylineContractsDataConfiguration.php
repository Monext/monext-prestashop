<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataConfiguration;

class PaylineContractsDataConfiguration extends AbstractDataConfiguration
{

    public function getConfiguration(): array
    {
        return [
            'PAYLINE_CONTRACTS' => $this->configuration->get('PAYLINE_CONTRACTS') ?: '[]',
            'PAYLINE_ALT_CONTRACTS_AS_MAIN' => (bool) $this->configuration->get('PAYLINE_ALT_CONTRACTS_AS_MAIN'),
            'PAYLINE_ALT_CONTRACTS' => $this->configuration->get('PAYLINE_ALT_CONTRACTS') ?: '[]',
        ];
    }

    public function updateConfigurationField($configKey, $configValue)
    {
        if ($configKey == 'PAYLINE_CONTRACTS' || $configKey == 'PAYLINE_ALT_CONTRACTS') {
            $jsonData = json_decode($configValue);
            $contractsList = array();
            foreach ($jsonData as $val) {
                if ($val != '') {
                    $contractsList[] = $val;
                }
            }
            $configValue = json_encode($contractsList);
        }
        parent::updateConfigurationField($configKey, $configValue);
    }
}
