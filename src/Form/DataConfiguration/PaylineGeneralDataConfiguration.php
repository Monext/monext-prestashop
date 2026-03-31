<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataConfiguration;

class PaylineGeneralDataConfiguration extends AbstractDataConfiguration
{

    /**
     * Returns current stored values, keyed by form field name.
     *
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return [
            'PAYLINE_LIVE_MODE'           => (bool) $this->configuration->get('PAYLINE_LIVE_MODE'),
            'PAYLINE_MERCHANT_ID'         => (string) ($this->configuration->get('PAYLINE_MERCHANT_ID') ?: ''),
            'PAYLINE_ACCESS_KEY'          => $this->maskAccessKey($this->configuration->get('PAYLINE_ACCESS_KEY') ?: ''),
            'PAYLINE_POS'                 => (string) ($this->configuration->get('PAYLINE_POS') ?: ''),
            'PAYLINE_SMARTDISPLAY_PARAM'  => (string) ($this->configuration->get('PAYLINE_SMARTDISPLAY_PARAM') ?: ''),
            'PAYLINE_PROXY_HOST'          => (string) ($this->configuration->get('PAYLINE_PROXY_HOST') ?: ''),
            'PAYLINE_PROXY_PORT'          => (string) ($this->configuration->get('PAYLINE_PROXY_PORT') ?: ''),
            'PAYLINE_PROXY_LOGIN'         => (string) ($this->configuration->get('PAYLINE_PROXY_LOGIN') ?: ''),
            'PAYLINE_PROXY_PASSWORD'      => (string) ($this->configuration->get('PAYLINE_PROXY_PASSWORD') ?: ''),
            'PAYLINE_ERROR_REFUSED'       => (string) ($this->configuration->get('PAYLINE_ERROR_REFUSED') ?: ''),
            'PAYLINE_ERROR_CANCELLED'     => (string) ($this->configuration->get('PAYLINE_ERROR_CANCELLED') ?: ''),
            'PAYLINE_ERROR_ERROR'         => (string) ($this->configuration->get('PAYLINE_ERROR_ERROR') ?: ''),
        ];
    }


    public function updateConfigurationField($configKey, $configValue)
    {
        if ($configKey === 'PAYLINE_ACCESS_KEY') {
            $currentValue = $this->configuration->get($configKey);
            $maskedValue = $this->maskAccessKey($currentValue);
            if ($configValue !== $maskedValue) {
                parent::updateConfigurationField($configKey, $configValue);
            }
        }else {
            parent::updateConfigurationField($configKey, $configValue);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function maskAccessKey($key)
    {
        if (empty($key)) {
            return '';
        }
        $length = strlen($key);
        if ($length <= 3) {
            return $key;
        }
        $visible = substr($key, -3);
        $masked = str_repeat('*', $length - 3);
        return $masked . $visible;
    }
}
