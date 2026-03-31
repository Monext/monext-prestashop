<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\DataConfiguration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

abstract class AbstractDataConfiguration implements DataConfigurationInterface
{

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        foreach ($configuration as $configKey => $configValue){
            $this->updateConfigurationField($configKey, $configValue);
        }

        return $errors;
    }

    public function updateConfigurationField ($configKey, $configValue)
    {
        $this->configuration->set($configKey, $configValue);
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration): bool
    {
        return true;
    }
}
