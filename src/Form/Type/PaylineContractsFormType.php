<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\Type;

use PrestaShop\Module\Payline\Service\PaylineFormChoicesProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Translation\TranslatorInterface;

class PaylineContractsFormType extends TranslatorAwareType
{

    public function __construct(
        TranslatorInterface $translator,
        array $locales
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Hidden field for main contracts (JSON array)
        $builder->add('PAYLINE_CONTRACTS', HiddenType::class, [
            'required' => false,
        ]);

        // Switch: use same contracts for alt
        $builder->add('PAYLINE_ALT_CONTRACTS_AS_MAIN', SwitchType::class, [
            'label' => $this->trans('Use same alt contracts as main', 'Modules.Payline.Admin'),
            'required' => false,
        ]);

        // Hidden field for alternative contracts (JSON array)
        $builder->add('PAYLINE_ALT_CONTRACTS', HiddenType::class, [
            'required' => false,
        ]);
    }
}
