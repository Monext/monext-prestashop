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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShopBundle\Translation\TranslatorInterface;

class PaylineGeneralFormType extends TranslatorAwareType
{
    private PaylineFormChoicesProvider $choicesProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        PaylineFormChoicesProvider $choicesProvider
    ) {
        parent::__construct($translator, $locales);
        $this->choicesProvider = $choicesProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('PAYLINE_LIVE_MODE', SwitchType::class, [
                'label' => $this->trans('Live mode', 'Modules.Payline.Admin'),
                'help' => $this->trans('Set the payment as live (real charge) or test mode (no charge)', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_MERCHANT_ID', TextType::class, [
                'label' => $this->trans('Merchant Id', 'Modules.Payline.Admin'),
                'required' => true,
                'attr' => ['autocomplete' => 'off'],
                'row_attr' => ['class' => $this->getCredentialFieldClass()],
            ])
            ->add('PAYLINE_ACCESS_KEY', TextType::class, [
                'label' => $this->trans('Access key', 'Modules.Payline.Admin'),
                'required' => true,
                'attr' => ['autocomplete' => 'off'],
                'row_attr' => ['class' => $this->getCredentialFieldClass()],
            ])
            ->add('PAYLINE_POS', ChoiceType::class, [
                'label' => $this->trans('Point of sale', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getPointOfSaleChoices(),
                'required' => $this->choicesProvider->checkCredentials(),
                'placeholder' => '-- Select a point of sale --',
                'row_attr' => ['class' => $this->getCredentialFieldClass()],
            ])
            ->add('PAYLINE_SMARTDISPLAY_PARAM', TextType::class, [
                'label' => $this->trans('Smartdisplay parameter', 'Modules.Payline.Admin'),
                'help' => $this->trans('Added in doWebPayment privateData as display.rule.param', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_PROXY_HOST', TextType::class, [
                'label' => $this->trans('Host', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_PROXY_PORT', TextType::class, [
                'label' => $this->trans('Port', 'Modules.Payline.Admin'),
                'required' => false,
                'attr' => ['maxlength' => 5],
            ])
            ->add('PAYLINE_PROXY_LOGIN', TextType::class, [
                'label' => $this->trans('Login', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_PROXY_PASSWORD', TextType::class, [
                'label' => $this->trans('Password', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_ERROR_REFUSED', TextType::class, [
                'label' => $this->trans('Type Refused', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_ERROR_CANCELLED', TextType::class, [
                'label' => $this->trans('Type Cancelled', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_ERROR_ERROR', TextType::class, [
                'label' => $this->trans('Type Error', 'Modules.Payline.Admin'),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Modules.Payline.Admin',
        ]);
    }

    /**
     * Returns CSS class for credential fields based on API status.
     */
    private function getCredentialFieldClass(): string
    {
        return $this->choicesProvider->checkCredentials() ? 'has-success' : 'has-error';
    }
}
