<?php
namespace Monext\Tests\Unit {

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../class/PaylineWallet.php';

use PHPUnit\Framework\TestCase;

class PaylineWalletTest extends TestCase
{
    protected function setUp(): void
    {
        tests_reset_db_stub();
        \Tools::$passwd = 'stubbed_password';
    }

    public function testInsertReturnsFalseForEmptyCustomer()
    {
        $this->assertFalse(\PaylineWallet::insert('', 'wallet1'));
    }

    public function testInsertExecutesSqlAndReturnsTrue()
    {
        $db = \Db::getInstance();
        $db->executeReturn = true;

        $result = \PaylineWallet::insert(123, 'myWalletId');

        $this->assertTrue($result);
        $this->assertStringContainsString('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'payline_wallet_id`', $db->lastQuery);
        $this->assertStringContainsString('123', $db->lastQuery);
        $this->assertStringContainsString('myWalletId', $db->lastQuery);
    }

    public function testGetWalletByIdCustomerReturnsValueOrNull()
    {
        $db = \Db::getInstance();
        $db->valueMap['`id_customer`=55'] = 'wallet-abc';

        $this->assertSame('wallet-abc', \PaylineWallet::getWalletByIdCustomer(55));

        $db->valueMap['`id_customer`=55'] = '';
        $this->assertNull(\PaylineWallet::getWalletByIdCustomer(55));
    }

    public function testGetIdCustomerByWalletIdNumericAndNonNumeric()
    {
        $db = \Db::getInstance();
        // numeric wallet id case -> wallet_id=42
        $db->valueMap['`wallet_id`=42'] = '42';
        $this->assertSame('42', \PaylineWallet::getIdCustomerByWalletId('42'));

        // non-numeric wallet id is cast to int -> wallet_id=0
        $db->valueMap['`wallet_id`=0'] = null;
        $this->assertNull(\PaylineWallet::getIdCustomerByWalletId('abc123'));
    }

    public function testGenerateWalletIdSuccessAndFailure()
    {
        $db = \Db::getInstance();
        \Tools::$passwd = 'generated_wallet_id';

        $db->executeReturn = true;
        $this->assertSame('generated_wallet_id', \PaylineWallet::generateWalletId(99));

        $db->executeReturn = false;
        $this->assertNull(\PaylineWallet::generateWalletId(99));
    }
}

}
