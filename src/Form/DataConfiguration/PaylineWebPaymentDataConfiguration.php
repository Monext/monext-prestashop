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

class PaylineWebPaymentDataConfiguration extends AbstractDataConfiguration
{

    public function getConfiguration(): array
    {
        return [
            'PAYLINE_WEB_CASH_ENABLE' => (bool) $this->configuration->get('PAYLINE_WEB_CASH_ENABLE'),
            'PAYLINE_WEB_CASH_TITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_WEB_CASH_TITLE'),
            'PAYLINE_WEB_CASH_SUBTITLE' => Configuration::getConfigInMultipleLangs('PAYLINE_WEB_CASH_SUBTITLE'),
            'PAYLINE_WEB_CASH_ACTION' => $this->configuration->get('PAYLINE_WEB_CASH_ACTION') ?? '101',
            'PAYLINE_WEB_CASH_VALIDATION' => $this->configuration->get('PAYLINE_WEB_CASH_VALIDATION') ?? '',
            'PAYLINE_WEB_CASH_BY_WALLET' => (bool) $this->configuration->get('PAYLINE_WEB_CASH_BY_WALLET'),
            'PAYLINE_WEB_CASH_UX' => $this->configuration->get('PAYLINE_WEB_CASH_UX') ?? 'redirect',
            'PAYLINE_WEB_CASH_CUSTOM_CODE' => $this->configuration->get('PAYLINE_WEB_CASH_CUSTOM_CODE') ?? '',
            'PAYLINE_DEFAULT_CATEGORY' => $this->configuration->get('PAYLINE_DEFAULT_CATEGORY') ?? '1',
            'PAYLINE_WEB_WIDGET_CUSTOM' => $this->configuration->get('PAYLINE_WEB_WIDGET_CUSTOM'),
            'PAYLINE_WEB_WIDGET_CTA_LABEL' => Configuration::getConfigInMultipleLangs('PAYLINE_WEB_WIDGET_CTA_LABEL'),
            'PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR'),
            'PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMAL' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMAL'),
            'PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HOVER' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HOVER'),
            'PAYLINE_WEB_WIDGET_CSS_CTA_TEXT_COLOR' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_CTA_TEXT_COLOR'),
            'PAYLINE_WEB_WIDGET_CSS_FONT_SIZE' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_FONT_SIZE'),
            'PAYLINE_WEB_WIDGET_CSS_BORDER_RADIUS' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_BORDER_RADIUS'),
            'PAYLINE_WEB_WIDGET_CSS_BG_COLOR' => $this->configuration->get('PAYLINE_WEB_WIDGET_CSS_BG_COLOR'),
            'PAYLINE_WEB_WIDGET_TEXT_UNDER_CTA' => Configuration::getConfigInMultipleLangs('PAYLINE_WEB_WIDGET_TEXT_UNDER_CTA'),
        ];
    }
}
