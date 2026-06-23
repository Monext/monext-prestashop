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
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\When;
use PrestaShopBundle\Translation\TranslatorInterface;

class PaylineWebPaymentFormType extends TranslatorAwareType
{
    const PAYLINE_WIDGET_CTA_MAX_LENGTH = 255;

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
            ->add('PAYLINE_WEB_CASH_ENABLE', SwitchType::class, [
                'label' => $this->trans('Enable simple payment', 'Modules.Payline.Admin'),
                'help' => $this->trans('choose wether to display Monext simple payment in your checkout or not', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_WEB_CASH_TITLE', TranslatableType::class, [
                'label' => $this->trans('Title', 'Modules.Payline.Admin'),
                'required' => true,
            ])
            ->add('PAYLINE_WEB_CASH_SUBTITLE', TranslatableType::class, [
                'label' => $this->trans('Subtitle', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_WEB_CASH_ACTION', ChoiceType::class, [
                'label' => $this->trans('Debit mode', 'Modules.Payline.Admin'),
                'help' => $this->trans('Select authorization+capture if you want to charge your customer at order creation. Charge him later with authorization', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getPaymentActionChoices(),
            ])
            ->add('PAYLINE_WEB_CASH_VALIDATION', ChoiceType::class, [
                'label' => $this->trans('Capture payment on', 'Modules.Payline.Admin'),
                'help' => $this->trans('Choose which order status will trigger payment capture', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getOrderStatesChoices(),
            ])
            ->add('PAYLINE_WEB_CASH_BY_WALLET', SwitchType::class, [
                'label' => $this->trans('Payment by wallet', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_WEB_CASH_UX', ChoiceType::class, [
                'label' => $this->trans('User experience', 'Modules.Payline.Admin'),
                'help' => $this->trans('Redirect customer to secure payment page or display secure form in the checkout', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getUserExperienceChoices(),
                'attr' => [
                    'class' => 'ux_field',
                ],
            ])
            ->add('PAYLINE_WEB_CASH_CUSTOM_CODE', TextType::class, [
                'label' => $this->trans('Payment page customization ID', 'Modules.Payline.Admin'),
                'help' => $this->trans('Apply customization created through administration center to the payment page', 'Modules.Payline.Admin'),
                'required' => false,
                'row_attr' => [
                    'class' => 'fixed-width-md',
                ]
            ])
            ->add('PAYLINE_DEFAULT_CATEGORY', ChoiceType::class, [
                'label' => $this->trans('Default category', 'Modules.Payline.Admin'),
                'help' => $this->trans('Category of item, needed for some contracts', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getDefaultCategoryChoices(),
                'required' => true,
            ])
            ->add('PAYLINE_WEB_WIDGET_CUSTOM', SwitchType::class, [
                'label' => $this->trans('Customisation', 'Modules.Payline.Admin'),
                'help' => $this->trans('Choose wether to customize the widget', 'Modules.Payline.Admin'),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization_head',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CTA_LABEL', TranslatableType::class, [
                'label' => $this->trans('CTA Label', 'Modules.Payline.Admin'),
                'help' => $this->trans('For example : "Confirm and pay [[amount]]" will display Confirm and pay 142.56 EUR -- [[amount]] is optional - No html tags allowed', 'Modules.Payline.Admin'),
                'required' => false,
                'attr' => [
                    'maxlength' => self::PAYLINE_WIDGET_CTA_MAX_LENGTH,
                ],
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR', ChoiceType::class, [
                'label' => $this->trans('CTA color', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetCtaBgColorChoices(),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HEXADECIMAL', ColorType::class, [
                'label' => $this->trans('CTA hexadecimal color', 'Modules.Payline.Admin'),
                'help' => $this->trans('For example : #123456', 'Modules.Payline.Admin'),
                'attr' => ['maxlength' => 7],
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization hexadecimal-input',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_CTA_BG_COLOR_HOVER', ChoiceType::class, [
                'label' => $this->trans('CTA hover', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetCtaBgHoverChoices(),
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_CTA_TEXT_COLOR', ChoiceType::class, [
                'label' => $this->trans('CTA color', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetCtaColorChoices(),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_FONT_SIZE', ChoiceType::class, [
                'label' => $this->trans('CTA font size', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetCtaFontSizeChoices(),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_BORDER_RADIUS', ChoiceType::class, [
                'label' => $this->trans('Border radius', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetCtaBorderRadiusChoices(),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_CSS_BG_COLOR', ChoiceType::class, [
                'label' => $this->trans('Widget backgound', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getWidgetContainerBgColorChoices(),
                'required' => false,
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
            ->add('PAYLINE_WEB_WIDGET_TEXT_UNDER_CTA', TranslatableType::class, [
                'label' => $this->trans('Text under CTA', 'Modules.Payline.Admin'),
                'help' => $this->trans('For example : Clicking on the button automatically implies acceptance of the T&Cs -- No html tags allowed', 'Modules.Payline.Admin'),
                'required' => false,
                'attr' => ['maxlength' => self::PAYLINE_WIDGET_CTA_MAX_LENGTH],
                'row_attr' => [
                    'class' => 'widget_customization',
                ],
            ])
        ;
    }
}
