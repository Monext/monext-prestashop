<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\Type;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShop\Module\Payline\Service\PaylineFormChoicesProvider;
use PrestaShopBundle\Translation\TranslatorInterface;

class PaylineNxPaymentFormType extends TranslatorAwareType
{
    private PaylineFormChoicesProvider $choicesProvider;

    public function __construct(
        TranslatorInterface        $translator,
        array                      $locales,
        PaylineFormChoicesProvider $choicesProvider
    )
    {
        parent::__construct($translator, $locales);
        $this->choicesProvider = $choicesProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('PAYLINE_RECURRING_ENABLE', SwitchType::class, [
                'label' => $this->trans('Enable Nx payment', 'Modules.Payline.Admin'),
                'help' => $this->trans('choose wether to display Monext recurring payment in your checkout or not', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_RECURRING_TITLE', TranslatableType::class, [
                'label' => $this->trans('Title', 'Modules.Payline.Admin'),
                'required' => true,
            ])
            ->add('PAYLINE_RECURRING_SUBTITLE', TranslatableType::class, [
                'label' => $this->trans('Subtitle', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_RECURRING_TRIGGER', TextType::class, [
                'label' => $this->trans('Minimal order total to allow recurring', 'Modules.Payline.Admin'),
                'help' => $this->trans('Amount under which payment in several times is not displayed', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_RECURRING_NUMBER', ChoiceType::class, [
                'label' => $this->trans('Number of payments', 'Modules.Payline.Admin'),
                'choices' =>  $this->choicesProvider->getRecurringPeriodsChoices(true),
            ])
            ->add('PAYLINE_RECURRING_PERIODICITY', ChoiceType::class, [
                'label' => $this->trans('Periodicity of payments', 'Modules.Payline.Admin'),
                'choices' =>  $this->choicesProvider->getRecurringFrequencyChoices(),
            ])
            ->add('PAYLINE_RECURRING_FIRST_WEIGHT', ChoiceType::class, [
                'label' => $this->trans('First payment weight', 'Modules.Payline.Admin'),
                'help' => $this->trans('Percentage of total amount for first payment', 'Modules.Payline.Admin'),
                'choices' =>  $this->choicesProvider->getFirstPeriodWeightChoices(),
            ])
            ->add('PAYLINE_WEB_CASH_BY_WALLET', SwitchType::class, [
                'label' => $this->trans('Payment by wallet', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_RECURRING_UX', ChoiceType::class, [
                'label' => $this->trans('User experience', 'Modules.Payline.Admin'),
                'help' => $this->trans('Redirect customer to secure payment page or display secure form in the checkout', 'Modules.Payline.Admin'),
                'choices' =>  $this->choicesProvider->getUserExperienceChoices(),
                'attr' => [
                    'class' => 'ux_field',
                ],
            ])
            ->add('PAYLINE_RECURRING_CUSTOM_CODE', TextType::class, [
                'label' => $this->trans('Payment page customization ID', 'Modules.Payline.Admin'),
                'help' => $this->trans('Apply customization created through administration center to the payment page', 'Modules.Payline.Admin'),
                'required' => false,
            ])
        ;
    }
}
