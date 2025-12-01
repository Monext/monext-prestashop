<?php
namespace Monext\Tests\Unit {

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../class/PaylineToken.php';

use PHPUnit\Framework\TestCase;

class PaylineTokenTest extends TestCase
{
    protected function setUp(): void
    {
        tests_reset_db_stub();
    }

    public function testInsertBuildsSqlAndReturnsTrue()
    {
        $db = \Db::getInstance();
        $db->executeReturn = true;

        $order = new \Order(); $order->id = 10;
        $cart = new \Cart(); $cart->id = 20;

        $res = \PaylineToken::insert($order, $cart, "tok'123", 'payRec', 'tx123');
        $this->assertTrue($res);
        $this->assertStringContainsString('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'payline_token`', $db->lastQuery);
        $this->assertStringContainsString('(10, 20,', $db->lastQuery);
        $this->assertStringContainsString("tok\\'123", $db->lastQuery);
        $this->assertStringContainsString('payRec', $db->lastQuery);
    }

    public function testSetPaymentRecordIdByIdOrderUpdatesSql()
    {
        $db = \Db::getInstance();
        $db->executeReturn = true;

        $order = new \Order(); $order->id = 42;
        $res = \PaylineToken::setPaymentRecordIdByIdOrder($order, 'PR-1');
        $this->assertTrue($res);
        $this->assertStringContainsString('UPDATE `' . _DB_PREFIX_ . 'payline_token` SET `payment_record_id`', $db->lastQuery);
        $this->assertStringContainsString('WHERE `id_order`=42', $db->lastQuery);
    }

    public function testGettersReturnValuesOrNull()
    {
        $db = \Db::getInstance();
        $db->getValueReturn = 'tok-77';
        $this->assertSame('tok-77', \PaylineToken::getTokenByIdOrder(77));

        $db->getValueReturn = '';
        $this->assertNull(\PaylineToken::getTokenByIdOrder(77));

        $db->getValueReturn = 'payRec-55';
        $this->assertSame('payRec-55', \PaylineToken::getPaymentRecordIdByIdOrder(55));

        $db->getValueReturn = 'tx-99';
        $this->assertSame('tx-99', \PaylineToken::getIdTransactionByIdOrder(99));
    }

    public function testGetPaymentRecordIdListByIdCustomer()
    {
        $db = \Db::getInstance();
        $db->executeSReturnMap['FROM `' . _DB_PREFIX_ . 'payline_token`'] = array(
            array('payment_record_id' => 'A'),
            array('payment_record_id' => 'B'),
        );

        $list = \PaylineToken::getPaymentRecordIdListByIdCustomer(5);
        $this->assertSame(array('A', 'B'), $list);
    }

    public function testGetIdOrderListByPaymentRecordId()
    {
        $db = \Db::getInstance();
        $db->executeSReturnMap['FROM `' . _DB_PREFIX_ . 'payline_token`'] = array(
            array('id_order' => 101),
            array('id_order' => 102),
        );

        $list = \PaylineToken::getIdOrderListByPaymentRecordId('PR-2');
        $this->assertSame(array(101, 102), $list);
    }

    public function testGetIdOrderByIdTransactionTokenAndFallback()
    {
        $db = \Db::getInstance();

        // Case 1: token table has id_order
        $db->valueMap['FROM `' . _DB_PREFIX_ . 'payline_token`'] = '555';
        $this->assertSame('555', \PaylineToken::getIdOrderByIdTransaction('T1'));

        // Case 2: token doesn't have it, fallback to order_payment -> orders
        $db->valueMap['FROM `' . _DB_PREFIX_ . 'payline_token`'] = '';
        $db->valueMap['FROM `' . _DB_PREFIX_ . 'order_payment`'] = 'REF-XYZ';
        $db->valueMap['FROM `' . _DB_PREFIX_ . 'orders`'] = '777';

        $this->assertSame('777', \PaylineToken::getIdOrderByIdTransaction('T2'));
    }
}

}
