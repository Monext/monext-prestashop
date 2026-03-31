<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Service;

use Configuration;
use OrderState;
use PaylinePaymentGateway;
use PrestaShopBundle\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;


/**
 * Provides dynamic choices for Payline configuration forms.
 */
class PaylineFormChoicesProvider
{
    private TranslatorInterface $translator;
    private ConfigurationInterface $configuration;

    public function __construct(TranslatorInterface $translator, ConfigurationInterface $configuration)
    {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * Translate a string.
     */
    private function trans(string $id, string $domain = 'Modules.Payline.Admin'): string
    {
        return $this->translator->trans($id, [], $domain);
    }
    /**
     * Check if API credentials are valid.
     */
    public function checkCredentials(): bool
    {
        return (bool) PaylinePaymentGateway::checkCredentials();
    }

    /**
     * Get Point of Sale choices for select field.
     *
     * @return array<string, string> ['label' => 'value']
     */
    public function getPointOfSaleChoices(): array
    {
        $choices = [];

        if ($this->checkCredentials()) {
            $this->configuration->set('PAYLINE_API_STATUS', true);
            $posList = PaylinePaymentGateway::getPointOfSales();
            foreach ($posList as $pos) {
                $choices[$pos['label']] = $pos['label'];
            }
        }else{
            $this->configuration->set('PAYLINE_API_STATUS', false);
        }

        return $choices;
    }

    /**
     * Get payment action choices.
     *
     * @return array<string, string>
     */
    public function getPaymentActionChoices(): array
    {
        return [
            $this->trans('Authorization + Capture') => '101',
            $this->trans('Authorization') => '100',
        ];
    }

    /**
     * Get user experience choices.
     *
     * @param string|null $paymentMethod Current payment method to check lightbox availability
     *
     * @return array<string, string>
     */
    public function getUserExperienceChoices(?string $paymentMethod = null): array
    {
        $choices = [
            $this->trans('In-shop tab') => 'tab',
            $this->trans('In-shop column') => 'column',
            $this->trans('Redirect to payment page') => 'redirect',
        ];

        // Only allow lightbox UX mode once across payment methods
        $lightboxAlreadyEnabled = false;
        if ($paymentMethod === PaylinePaymentGateway::WEB_PAYMENT_METHOD) {
            $lightboxAlreadyEnabled = (Configuration::get('PAYLINE_RECURRING_UX') === 'lightbox');
        } elseif ($paymentMethod === PaylinePaymentGateway::RECURRING_PAYMENT_METHOD) {
            $lightboxAlreadyEnabled = (Configuration::get('PAYLINE_WEB_CASH_UX') === 'lightbox');
        }

        if (!$lightboxAlreadyEnabled) {
            $choices[$this->trans('Lightbox')] = 'lightbox';
        }

        return $choices;
    }

    /**
     * Get default category choices.
     *
     * @return array<string, string>
     */
    public function getDefaultCategoryChoices(): array
    {
        return [
            $this->trans('Computer (hardware and software)') => '1',
            $this->trans('Electronics - TV - Hifi') => '2',
            $this->trans('Phone') => '3',
            $this->trans('Home appliance') => '4',
            $this->trans('Habitat and garden') => '5',
            $this->trans('Fashion Clothing') => '6',
            $this->trans('Beauty product') => '7',
            $this->trans('Jewelry') => '8',
            $this->trans('Sport') => '9',
            $this->trans('Hobbies') => '10',
            $this->trans('Automobiles / motorcycles') => '11',
            $this->trans('Furnishing') => '12',
            $this->trans('Children') => '13',
            $this->trans('Video games') => '14',
            $this->trans('Toys') => '15',
            $this->trans('Animals') => '16',
            $this->trans('Food') => '17',
            $this->trans('Food TRD eligible products') => '170001',
            $this->trans('Gifts') => '18',
            $this->trans('Shows') => '19',
            $this->trans('Traveling') => '20',
            $this->trans('Auction') => '21',
            $this->trans('Particular services') => '22',
            $this->trans('Professional Services') => '23',
            $this->trans('Music') => '24',
            $this->trans('Book') => '25',
            $this->trans('Photo') => '26',
        ];
    }

    /**
     * Get recurring periods choices.
     *
     * @param bool $includeNoLimit Include "No limit" option (for subscriptions)
     *
     * @return array<string, int>
     */
    public function getRecurringPeriodsChoices(bool $includeNoLimit = false): array
    {
        $choices = [];

        if ($includeNoLimit) {
            $choices[$this->trans('No limit')] = 0;
        }

        for ($period = 2; $period <= 99; $period++) {
            $choices[(string) $period] = $period;
        }

        return $choices;
    }

    /**
     * Get recurring frequency choices.
     *
     * @return array<string, string>
     */
    public function getRecurringFrequencyChoices(): array
    {
        return [
            $this->trans('Daily') => '10',
            $this->trans('Weekly') => '20',
            $this->trans('Bimonthly') => '30',
            $this->trans('Monthly') => '40',
            $this->trans('Two quarterly') => '50',
            $this->trans('Quarterly') => '60',
            $this->trans('Semiannual') => '70',
            $this->trans('Annual') => '80',
            $this->trans('Biannual') => '90',
        ];
    }

    /**
     * Get first period weight choices (0% to 70% in 5% increments).
     *
     * @return array<string, int>
     */
    public function getFirstPeriodWeightChoices(): array
    {
        $choices = [];

        for ($weight = 0; $weight <= 70; $weight += 5) {
            $choices[$weight . ' %'] = $weight;
        }

        return $choices;
    }

    /**
     * Get subscribe start date choices.
     *
     * @return array<string, int>
     */
    public function getSubscribeStartDateChoices(): array
    {
        return [
            $this->trans('Due day') => 0,
            $this->trans('After a period') => 1,
            $this->trans('After two periods') => 2,
        ];
    }

    /**
     * Get subscribe days choices (0-31).
     *
     * @return array<string, int>
     */
    public function getSubscribeDaysChoices(): array
    {
        $choices = [];

        for ($day = 0; $day <= 31; $day++) {
            $choices[(string) $day] = $day;
        }

        return $choices;
    }

    /**
     * Get widget CTA background color choices.
     *
     * @return array<string, string>
     */
    public function getWidgetCtaBgColorChoices(): array
    {
        return [
            $this->trans('Monext default') => '',
            $this->trans('Black') => '#000000',
            $this->trans('Red') => '#d64c1d',
            $this->trans('Green') => '#00786c',
            $this->trans('Dark grey') => '#42414f',
            $this->trans('Yellow') => '#e6d001',
            $this->trans('Hexadecimal value') => 'hexadecimal',
        ];
    }

    /**
     * Get widget CTA background hover choices.
     *
     * @return array<string, string>
     */
    public function getWidgetCtaBgHoverChoices(): array
    {
        return [
            $this->trans('30%') . ' ' . $this->trans('darker') => '+30',
            $this->trans('20%') . ' ' . $this->trans('darker') => '+20',
            $this->trans('10%') . ' ' . $this->trans('darker') => '+10',
            $this->trans('No') => '',
            $this->trans('10%') . ' ' . $this->trans('lighter') => '-10',
            $this->trans('20%') . ' ' . $this->trans('lighter') => '-20',
            $this->trans('30%') . ' ' . $this->trans('lighter') => '-30',
        ];
    }

    /**
     * Get widget CTA text color choices.
     *
     * @return array<string, string>
     */
    public function getWidgetCtaColorChoices(): array
    {
        return [
            $this->trans('Monext default') => '',
            $this->trans('Black') => '#000000',
            $this->trans('White') => '#FFFFFF',
        ];
    }

    /**
     * Get widget CTA font size choices.
     *
     * @return array<string, string>
     */
    public function getWidgetCtaFontSizeChoices(): array
    {
        return [
            $this->trans('Monext default') => '',
            $this->trans('Small') => 'small',
            $this->trans('Average') => 'average',
            $this->trans('Big') => 'big',
        ];
    }

    /**
     * Get widget CTA border radius choices.
     *
     * @return array<string, string>
     */
    public function getWidgetCtaBorderRadiusChoices(): array
    {
        return [
            $this->trans('Monext default') => '',
            $this->trans('None') => 'none',
            $this->trans('Small') => 'small',
            $this->trans('Average') => 'average',
            $this->trans('Big') => 'big',
        ];
    }

    /**
     * Get widget container background color choices.
     *
     * @return array<string, string>
     */
    public function getWidgetContainerBgColorChoices(): array
    {
        return [
            $this->trans('Monext default') => '',
            $this->trans('Lighter') => 'lighter',
            $this->trans('Darker') => 'darker',
        ];
    }

    public function getOrderStatesChoices(): array
    {
        $orderStatusListForSelect = [];
        $orderStates = \OrderState::getOrderStates((int) \Context::getContext()->language->id);

        foreach ($orderStates as $state) {
            // Ignore order states related to a specific module or error/refund/waiting to be paid states
            if ($state['logable'] != 1) {
                continue;
            }
            $orderStatusListForSelect[$state['name']] = (string) $state['id_order_state'];
        }

        return $orderStatusListForSelect;
    }
}
