<?php
namespace Monext\Tests\Unit {

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../class/PaylinePaymentGateway.php';

use PHPUnit\Framework\TestCase;
use \Payline\PaylineSDK as PaylineSDK;
    use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
    use ReflectionMethod;

class PaylinePaymentGatewayTest extends TestCase
{
    protected function setUp(): void
    {
        tests_reset_db_stub();
    }

    ////// Constants Tests //////

    public function testApprovedResponseCodeConstantIsArray()
    {
        $this->assertIsArray(\PaylinePaymentGateway::$approvedResponseCode);
        $this->assertContains('34230', \PaylinePaymentGateway::$approvedResponseCode);
        $this->assertContains('34330', \PaylinePaymentGateway::$approvedResponseCode);
        $this->assertContains('02400', \PaylinePaymentGateway::$approvedResponseCode);
        $this->assertContains('02500', \PaylinePaymentGateway::$approvedResponseCode);
        $this->assertContains('02501', \PaylinePaymentGateway::$approvedResponseCode);
    }

    public function testPendingResponseCodeConstantIsArray()
    {
        $this->assertIsArray(\PaylinePaymentGateway::$pendingResponseCode);
        $this->assertContains('02000', \PaylinePaymentGateway::$pendingResponseCode);
        $this->assertContains('02005', \PaylinePaymentGateway::$pendingResponseCode);
        $this->assertContains('02016', \PaylinePaymentGateway::$pendingResponseCode);
    }

    public function testWebPaymentMethodConstantValue()
    {
        $this->assertSame(1, \PaylinePaymentGateway::WEB_PAYMENT_METHOD);
        $this->assertIsInt(\PaylinePaymentGateway::WEB_PAYMENT_METHOD);
    }

    public function testRecurringPaymentMethodConstantValue()
    {
        $this->assertSame(2, \PaylinePaymentGateway::RECURRING_PAYMENT_METHOD);
        $this->assertIsInt(\PaylinePaymentGateway::RECURRING_PAYMENT_METHOD);
    }

    public function testSubscribePaymentMethodConstantValue()
    {
        $this->assertSame(3, \PaylinePaymentGateway::SUBSCRIBE_PAYMENT_METHOD);
        $this->assertIsInt(\PaylinePaymentGateway::SUBSCRIBE_PAYMENT_METHOD);
    }

    public function testApiVersionConstantValue()
    {
        $this->assertSame(34, \PaylinePaymentGateway::API_VERSION);
        $this->assertIsInt(\PaylinePaymentGateway::API_VERSION);
    }

    /////// Utility Method Tests //////

    // getCartIdFromOrderReference tests
    public function testGetCartIdFromOrderReferenceExtractsIdCorrectly()
    {
        // Test order reference format like "cart456try2"
        $reference = 'cart456try2';
        $cartId = \PaylinePaymentGateway::getCartIdFromOrderReference($reference);
        $this->assertSame(456, $cartId);
    }

    public function testGetCartIdFromOrderReferenceWithoutTryPart()
    {
        // Test order reference format like "cart789"
        $reference = 'cart789';
        $cartId = \PaylinePaymentGateway::getCartIdFromOrderReference($reference);
        $this->assertSame(789, $cartId);
    }

    public function testGetCartIdFromOrderReferenceReturnsNullForInvalidFormat()
    {
        $reference = 'invalid-reference';
        $cartId = \PaylinePaymentGateway::getCartIdFromOrderReference($reference);
        $this->assertNull($cartId);
    }

    // getEnvMode tests
    public function testGetEnvModeReturnsLiveWhenConfigurationIsSetToLive()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 1);
        $mode = \PaylinePaymentGateway::getEnvMode();
        $this->assertSame(PaylineSDK::ENV_PROD, $mode);
    }
    
    public function testGetEnvModeReturnsTestWhenConfigurationIsSetToTest()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 0);
        $mode = \PaylinePaymentGateway::getEnvMode();
        $this->assertSame(PaylineSDK::ENV_HOMO, $mode);   
    }

    //isProductionMode test

    public function testIsProductionModeReturnsWhenConfigurationIsSetToLive()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 1);
        $result = \PaylinePaymentGateway::isProductionMode();
        $this->assertTrue($result);
    }
    
    public function testIsProductionModeReturnsWhenConfigurationIsSetToTest()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 0);
        $result = \PaylinePaymentGateway::isProductionMode();
        $this->assertFalse($result);
    }

    //isHomologationMode test

    public function testIsHomologationModeReturnsWhenConfigurationIsSetToLive()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 1);
        $result = \PaylinePaymentGateway::isHomologationMode();
        $this->assertFalse($result);
    }
    
    public function testIsHomologationModeReturnsWhenConfigurationIsSetToTest()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 0);
        $result = \PaylinePaymentGateway::isHomologationMode();
        $this->assertTrue($result);
    }

    //isValidResponse test
    public function testIsValidResponseReturnsTrueForApprovedCodes()
    {
        foreach (\PaylinePaymentGateway::$approvedResponseCode as $code) {
            $result['result']['code'] = $code;
            $this->assertTrue(\PaylinePaymentGateway::isValidResponse($result, \PaylinePaymentGateway::$approvedResponseCode), "Failed asserting that code $code is valid.");
        }
    }

    public function testIsValidResponseReturnsFalseForNonApprovedCodes()
    {
        $invalidCodes = ['00000', '12345', '99999', '02000', '03000'];
        foreach ($invalidCodes as $code) {
            $result['result']['code'] = $code;
            $this->assertFalse(\PaylinePaymentGateway::isValidResponse($code), "Failed asserting that code $code is invalid.");
        }
    }

    //getErrorResponse tests
    public function testGetErrorResponseReturnsFormattedError()
    {
        $result['result']['code'] = '99999';
        $result['result']['shortMessage'] = 'Test error message';
        $result['result']['longMessage'] = 'Detailed test error message';
        $error = \PaylinePaymentGateway::getErrorResponse($result);
        $this->assertIsArray($error);
        $this->assertArrayHasKey('code', $error);
        $this->assertArrayHasKey('shortMessage', $error);
        $this->assertArrayHasKey('longMessage', $error);
        $this->assertSame('99999', $error['code']);
        $this->assertSame('Test error message', $error['shortMessage']);
        $this->assertSame('Detailed test error message', $error['longMessage']);
    }

    public function testGetErrorResponseHandlesValidResponseGracefully()
    {
        $result['result']['code'] = '00000';
        $error = \PaylinePaymentGateway::getErrorResponse($result);
        $this->assertNull($error);
    }

    // getTimestampFromPaylineDate tests
    public function testGetTimestampFromPaylineDateFormattedCorrectly()
    {
        // Payline date format is typically dd/mm/yyyy hh:mm or similar
        // Test conversion to timestamp
        $paylineDate = '01/01/2023 10:30';
        $timestamp = \PaylinePaymentGateway::getTimestampFromPaylineDate($paylineDate);
        $this->assertIsInt($timestamp);
        $this->assertGreaterThan(0, $timestamp);
    }
    
    // getAssetsToRegister tests
    public function testGetAssetsToRegisterReturnsCorrectStructure()
    {
        $assets = \PaylinePaymentGateway::getAssetsToRegister();
        $this->assertIsArray($assets);
        $this->assertArrayHasKey('css', $assets);
        $this->assertArrayHasKey('js', $assets);
        $this->assertIsArray($assets['css']);
        $this->assertIsArray($assets['js']);
    }

    public function testGetAssetsToRegisterContainsExpectedFiles()
    {
        // Test for both live and test mode URLs
        \Configuration::set('PAYLINE_LIVE_MODE', 1);
        $assetsLive = \PaylinePaymentGateway::getAssetsToRegister();
        $this->assertContains(PaylineSDK::PROD_WDGT_CSS, $assetsLive['css']);
        $this->assertContains(PaylineSDK::PROD_WDGT_JS, $assetsLive['js']);

        \Configuration::set('PAYLINE_LIVE_MODE', 0);
        $assets = \PaylinePaymentGateway::getAssetsToRegister();
        $this->assertContains(PaylineSDK::HOMO_WDGT_CSS, $assets['css']);
        $this->assertContains(PaylineSDK::HOMO_WDGT_JS, $assets['js']);
    }
    
    public function testGetAssetsToRegisterHandlesMissingConfigurationGracefully()
    {
        \Configuration::deleteByName('PAYLINE_LIVE_MODE');
        $assets = \PaylinePaymentGateway::getAssetsToRegister();
        $this->assertIsArray($assets);
        $this->assertArrayHasKey('css', $assets);
        $this->assertArrayHasKey('js', $assets);
        $this->assertContains(PaylineSDK::HOMO_WDGT_CSS, $assets['css']);
        $this->assertContains(PaylineSDK::HOMO_WDGT_JS, $assets['js']);
    }

    public function testGetAssetsToRegisterWithInvalidConfigurationValue()
    {
        \Configuration::set('PAYLINE_LIVE_MODE', 'invalid_value');
        $assets = \PaylinePaymentGateway::getAssetsToRegister();
        $this->assertIsArray($assets);
        $this->assertArrayHasKey('css', $assets);
        $this->assertArrayHasKey('js', $assets);
        $this->assertContains(PaylineSDK::HOMO_WDGT_CSS, $assets['css']);
        $this->assertContains(PaylineSDK::HOMO_WDGT_JS, $assets['js']);
    }

    //extractContractNumber tests

    public function testExtractContractNumberHandlesValidContractId()
    {
        // $extract = \PaylinePaymentGateway::extractContractNumber();
        $ref = new ReflectionMethod(\PaylinePaymentGateway::class, 'extractContractNumber');
        $ref->setAccessible(true);
        $contractId = 'CB-1234568';
        $extract = $ref->invoke(null, $contractId);
        $this->assertStringContainsString('1234568', $extract);
    }

    //getEnabledContracts tests

    public function testGetEnabledContractsHandlesValidConfiguration()
    {
        \Configuration::set('PAYLINE_CONTRACTS', array("CB-1234568","ONEY-ONEY_Prestashop"));
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertIsArray($configContracts);
        $this->assertArrayHasKey(0, $configContracts);
        $this->assertArrayHasKey(1, $configContracts);
        $this->assertSame("CB-1234568", $configContracts[0]);
        $this->assertSame("ONEY-ONEY_Prestashop", $configContracts[1]);
    }

    public function testGetEnabledContractsHandlesIncompleteContractId()
    {
        \Configuration::set('PAYLINE_CONTRACTS', array("1234568","ONEY"));
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertSame("1234568", $configContracts[0]);
        $this->assertSame("ONEY", $configContracts[1]);
    }

    public function testGetEnabledContractsReturnsEmptyArrayWhenNoContractsConfigured()
    {
        \Configuration::deleteByName('PAYLINE_CONTRACTS');
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertIsArray($configContracts);
        $this->assertEmpty($configContracts);
    }

    public function testGetEnabledContractsWithInvalidJsonConfiguration()
    {
        \Configuration::set('PAYLINE_CONTRACTS', 'invalid_json_string');
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertNull($configContracts);
        $this->assertEmpty($configContracts);
    }

    public function testGetEnabledContractsWithEmptyConfiguration()
    {
        \Configuration::set('PAYLINE_CONTRACTS', '');
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertIsArray($configContracts);
        $this->assertEmpty($configContracts);
    }

    public function testGetEnabledContractsWithContractNumberOnlyFlag()
    {
        \Configuration::set('PAYLINE_CONTRACTS', array("CB-1234568","ONEY-ONEY_Prestashop"));
        $configContracts = \PaylinePaymentGateway::getEnabledContracts(true);
        $this->assertIsArray($configContracts);
        $this->assertArrayHasKey(0, $configContracts);
        $this->assertArrayHasKey(1, $configContracts);
        $this->assertSame("1234568", $configContracts[0]);
        $this->assertSame("ONEY_Prestashop", $configContracts[1]);
    }

    public function testGetEnabledContractsWithEmptyArrayConfiguration()
    {
        \Configuration::set('PAYLINE_CONTRACTS', array());
        $configContracts = \PaylinePaymentGateway::getEnabledContracts();
        $this->assertIsArray($configContracts);
        $this->assertEmpty($configContracts);
    }

    //getNxConfiguration tests
    public function testGetNxConfigurationReturnsCorrectStructure()
    {
        $billingLeft = 3;
        $billingCycle = 10;
        \Configuration::set('PAYLINE_RECURRING_NUMBER', $billingLeft);
        \Configuration::set('PAYLINE_RECURRING_FIRST_WEIGHT', 0);
        \Configuration::set('PAYLINE_RECURRING_PERIODICITY', $billingCycle);
        $nxConfig = \PaylinePaymentGateway::getNxConfiguration('10.60');
        $this->assertIsArray($nxConfig);
        $this->assertArrayHasKey('firstAmount', $nxConfig);
        $this->assertEquals('5.000', $nxConfig['firstAmount']);
        $this->assertArrayHasKey('amount', $nxConfig);
        $this->assertEquals('3.000', $nxConfig['amount']);
        $this->assertArrayHasKey('billingLeft', $nxConfig);
        $this->assertEquals($billingLeft, $nxConfig['billingLeft']);
        $this->assertArrayHasKey('billingCycle', $nxConfig);
        $this->assertEquals($billingCycle, $nxConfig['billingCycle']);
    }

    // Todo: Add more tests for other utility methods as needed:  getMerchantSettings, checkCredentials, getPointOfSales, getContractsForCurrentPos, 
    // getContractsByPosLabel, assignLogoToContracts, createPaymentRequest, createSubscriptionRequest, getSubscriptionConfiguration, 
    // formatAddressForPaymentRequest, formatAndSortResult, getWebPaymentDetails, getPaymentInformations, getTransactionInformations, 
    // getPaymentRecord, disablePaymentRecord, getValidatedRecurringPayment, captureTransaction, refundTransaction, resetTransaction, 
    // cancelTransaction, getFallbackEnabledContracts, createManageWebWalletRequest
}

}
