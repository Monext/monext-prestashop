<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

class paylineNotificationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    /**
     * @var PaylineCallbacks
     */
    protected $callbacksHelper;

    public function __construct()
    {
        $this->callbacksHelper = new PaylineCallbacks();
        parent::__construct();
    }

    /**
     * @throws Exception
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $notificationType = Tools::getValue('notificationType');

        if ($notificationType == 'WEBTRS' && Tools::getValue('token')) {
            $this->callbacksHelper->processNotification(Tools::getValue('token'));
        } elseif ($notificationType == 'TRS' && Tools::getValue('transactionId')) {
            $this->callbacksHelper->processTransactionNotification(Tools::getValue('transactionId'));
        } elseif ($notificationType == 'BILL' && Tools::getValue('transactionId') && Tools::getValue('paymentRecordId') && Tools::getValue('paymentMode') == 'NX') {
            $this->callbacksHelper->processNxNotification(Tools::getValue('transactionId'), Tools::getValue('paymentRecordId'));
        } elseif ($notificationType == 'BILL' && Tools::getValue('transactionId') && Tools::getValue('paymentRecordId') && Tools::getValue('paymentMode') == 'REC') {
            $this->callbacksHelper->processRecNotification(Tools::getValue('transactionId'), Tools::getValue('paymentRecordId'));
        } else {
            PrestaShopLogger::addLog('Payline - Unknown notification type "'. $notificationType .'"');
        }
    }

    /**
     * @see FrontController::displayMaintenancePage()
     */
    protected function displayMaintenancePage()
    {
        // Prevent maintenance page to be triggered
    }
}
