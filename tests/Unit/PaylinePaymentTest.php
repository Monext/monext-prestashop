<?php
namespace Monext\Tests\Unit {

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../class/PaylinePayment.php';

use PHPUnit\Framework\TestCase;

class PaylinePaymentTest extends TestCase
{
    protected function setUp(): void
    {
        tests_reset_db_stub();
    }

    public function testInsertWebPaymentExecutesSqlAndReturnsTrue()
    {
        $db = \Db::getInstance();
        $db->executeReturn = true;

        $result = \PaylinePayment::insert(
            123,
            'token-abc',
            '02500',
            'Transaction accepted',
            'CP',
            '1234567890',
            'tx-001',
            array('key1' => 'value1', 'key2' => 'value2')
        );

        $this->assertTrue($result);
        $this->assertStringContainsString('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'payline_web_payment`', $db->lastQuery);
        $this->assertStringContainsString('123', $db->lastQuery);
        $this->assertStringContainsString('token-abc', $db->lastQuery);
        $this->assertStringContainsString('02500', $db->lastQuery);
        $this->assertStringContainsString('1234567890', $db->lastQuery);
        $this->assertStringContainsString('tx-001', $db->lastQuery);
    }

    public function testInsertWebPaymentWithEmptyAdditionalData()
    {
        $db = \Db::getInstance();
        $db->executeReturn = true;

        $result = \PaylinePayment::insert(456, 'token-xyz', '02500', 'OK', 'CP', '9876543210', 'tx-002');

        $this->assertTrue($result);
        $this->assertStringContainsString('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'payline_web_payment`', $db->lastQuery);
        $this->assertStringContainsString('456', $db->lastQuery);
    }

    public function testGetPaymentByTokenReturnsFormattedResult()
    {
        $db = \Db::getInstance();
        $additionalData = json_encode(array('extra' => 'info', 'status' => 'active'));
        
        $db->getRowReturn = array(
            'id_cart' => 789,
            'token' => 'token-test',
            'result_code' => '02500',
            'message' => 'Payment OK',
            'type' => 'CP',
            'contract_number' => '5555555555',
            'transaction_id' => 'tx-003',
            'additional_data' => $additionalData,
        );

        $result = \PaylinePayment::getPaymentByToken('token-test');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('order', $result);
        $this->assertArrayHasKey('transaction', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertSame('02500', $result['result']['code']);
        $this->assertSame('Payment OK', $result['result']['shortMessage']);
        $this->assertSame(789, $result['order']['ref']);
        $this->assertSame('tx-003', $result['transaction']['id']);
        $this->assertSame('token-test', $result['token']);
        $this->assertSame('5555555555', $result['contractNumber']);
        $this->assertSame('info', $result['extra']);
        $this->assertSame('active', $result['status']);
    }

    public function testGetPaymentByTokenReturnsNullForMissingToken()
    {
        $db = \Db::getInstance();
        $db->getRowReturn = null;

        $result = \PaylinePayment::getPaymentByToken('non-existent-token');

        $this->assertFalse($result);
    }

    public function testGetPaymentByTokenReturnsNullForEmptyResult()
    {
        $db = \Db::getInstance();
        $db->getRowReturn = false;

        $result = \PaylinePayment::getPaymentByToken('empty-token');

        $this->assertFalse($result);
    }
}

}
