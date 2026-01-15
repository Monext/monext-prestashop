<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateOrderCartException;
class paylineValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /** @var PaylineCallbacks $callbacksHelper */
    protected  $callbacksHelper;

    public function __construct()
    {
        $this->callbacksHelper = new PaylineCallbacks();
        parent::__construct();
    }

    /**
     * @throws DuplicateOrderCartException
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $paylineToken = null;

        if (Tools::getValue('paylinetoken')) {
            // Token from widget
            $paylineToken = Tools::getValue('paylinetoken');
        } elseif (Tools::getValue('token')) {
            // Token from Payline (redirect)
            $paylineToken = Tools::getValue('token');
        }

        if (!empty($paylineToken)) {
            $this->callbacksHelper->processCustomerPaymentReturn($paylineToken);
        }else{
            //No token, redirect to homepage
            Tools::redirect('index');
        }
    }
}
