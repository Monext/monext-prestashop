<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Glues PaylineGeneralDataConfiguration to the Symfony form handler.
 * getData() feeds the form with stored values.
 * setData() persists form submissions via the DataConfiguration.
 */
class PaylineGeneralFormDataProvider extends AbstractPaylineFormDataProvider
{
}
