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

class PaylineNxPaymentDataConfiguration extends AbstractDataConfiguration
{

    public function getConfiguration(): array
    {
        return [
            'PAYLINE_RECURRING_ENABLE' => (bool) $this->configuration->get('PAYLINE_RECURRING_ENABLE'),
            'PAYLINE_RECURRING_TITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_RECURRING_TITLE'),
            'PAYLINE_RECURRING_SUBTITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_RECURRING_SUBTITLE'),
            'PAYLINE_RECURRING_TRIGGER' => $this->configuration->get('PAYLINE_RECURRING_TRIGGER'),
            'PAYLINE_RECURRING_NUMBER' => $this->configuration->get('PAYLINE_RECURRING_NUMBER'),
            'PAYLINE_RECURRING_PERIODICITY' => $this->configuration->get('PAYLINE_RECURRING_PERIODICITY'),
            'PAYLINE_RECURRING_FIRST_WEIGHT' => $this->configuration->get('PAYLINE_RECURRING_FIRST_WEIGHT'),
            'PAYLINE_WEB_CASH_BY_WALLET' => $this->configuration->get('PAYLINE_WEB_CASH_BY_WALLET'),
            'PAYLINE_RECURRING_UX' => $this->configuration->get('PAYLINE_RECURRING_UX'),
            'PAYLINE_RECURRING_CUSTOM_CODE' => $this->configuration->get('PAYLINE_RECURRING_CUSTOM_CODE'),
        ];
    }
}
