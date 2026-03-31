<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataProvider;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Abstract base class for Payline form data providers.
 * Provides common implementation for all form data providers that delegate
 * to a DataConfiguration instance.
 */
abstract class AbstractPaylineFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    protected $dataConfiguration;

    public function __construct(DataConfigurationInterface $dataConfiguration)
    {
        $this->dataConfiguration = $dataConfiguration;
    }

    public function getData(): array
    {
        return $this->dataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
