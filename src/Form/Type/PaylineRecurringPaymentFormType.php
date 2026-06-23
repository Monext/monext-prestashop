<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Form\Type;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\Module\Payline\Service\PaylineFormChoicesProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Translation\TranslatorInterface;

class PaylineRecurringPaymentFormType extends TranslatorAwareType
{
    private PaylineFormChoicesProvider $choicesProvider;
    private $router;
    private LegacyContext $context;

    public function __construct(
        TranslatorInterface        $translator,
        array                      $locales,
        PaylineFormChoicesProvider $choicesProvider,
        $router,
        LegacyContext              $legacyContext
    )
    {
        parent::__construct($translator, $locales);
        $this->choicesProvider = $choicesProvider;
        $this->router = $router;
        $this->context = $legacyContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('PAYLINE_SUBSCRIBE_ENABLE', SwitchType::class, [
                'label' => $this->trans('Enable recurring payment', 'Modules.Payline.Admin'),
                'help' => $this->trans('choose wether to display Monext subscribe payment in your checkout or not', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_SUBSCRIBE_TITLE', TranslatableType::class, [
                'label' => $this->trans('Title', 'Modules.Payline.Admin'),
                'required' => true,
            ])
            ->add('PAYLINE_SUBSCRIBE_SUBTITLE', TranslatableType::class, [
                'label' => $this->trans('Subtitle', 'Modules.Payline.Admin'),
                'required' => false,
            ])
            ->add('PAYLINE_SUBSCRIBE_START_DATE', ChoiceType::class, [
                'label' => $this->trans('Start date of scheduler', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getSubscribeStartDateChoices(),
            ])
            ->add('PAYLINE_SUBSCRIBE_NUMBER', ChoiceType::class, [
                'label' => $this->trans('Number of payments', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getRecurringPeriodsChoices(true),
            ])
            ->add('PAYLINE_SUBSCRIBE_PERIODICITY', ChoiceType::class, [
                'label' => $this->trans('Periodicity of payments', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getRecurringFrequencyChoices(),
            ])
            ->add('PAYLINE_SUBSCRIBE_DAY', ChoiceType::class, [
                'label' => $this->trans('Recurring days', 'Modules.Payline.Admin'),
                'help' => $this->trans('0 if you want the payment to take place the same day as the date of the first order', 'Modules.Payline.Admin'),
                'choices' => $this->choicesProvider->getSubscribeDaysChoices(),
            ]);

        if(class_exists('PrestaShopBundle\Form\Admin\Type\ProductSearchType')){
            $builder->add('PAYLINE_SUBSCRIBE_PLIST', ProductSearchType::class, [
                'include_combinations' => false,
                'label' => $this->trans('Allowed product list', 'Modules.Payline.Admin'),
                'label_tag_name' => 'h3',
                'min_length' => 3,
                'limit' => 0,
                'required' => false,
            ]);
        }else{
            $builder->add('PAYLINE_SUBSCRIBE_PLIST', TypeaheadProductCollectionType::class, [
                'label' => $this->trans('Allowed product list', 'Modules.Payline.Admin'),
                'remote_url' => $this->context->getLegacyAdminLink('AdminProducts', true, [
                    'ajax' => 1,
                    'action' => 'productsList',
                    'forceJson' => 1,
                    'disableCombination' => 1,
                    'exclude_packs' => 0,
                    'excludeVirtuals' => 0,
                    'limit' => 20
                ]) . '&q=%QUERY',
                'mapping_value' => 'id',
                'mapping_name' => 'name',
                'placeholder' => $this->trans('Search products (min 3 characters)...', 'Modules.Payline.Admin'),
                'template_collection' => '<span class="product-name">%s</span><button type="button" class="btn btn-sm delete"><i class="material-icons">delete</i></button>',
                'limit' => 0,
                'required' => false,
            ]);
        }

        $builder->add('PAYLINE_SUBSCRIBE_EXCLUSIVE', SwitchType::class, [
                'label' => $this->trans('Set this product list as exclusive', 'Modules.Payline.Admin'),
                'help' => $this->trans('If a product from the cart is in this list, only this method will be shown. Else, this method will only be available if a product will be in your cart.', 'Modules.Payline.Admin'),
                'required' => false,
            ])
        ;
    }
}
