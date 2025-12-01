<?php

require_once __DIR__ . '/../lib/autoload.php';

if (!defined('_DB_PREFIX_')) {
    define('_DB_PREFIX_', 'ps_');
}

if (!class_exists('Order')) {
    class Order { public $id; }
}

if (!class_exists('Cart')) {
    class Cart { public $id; }
}

if (!function_exists('pSQL')) {
    function pSQL($s) { return addslashes($s); }
}

if (!class_exists('Tools')) {
    class Tools
    {
        public static $passwd = 'stubbed_password';

        public static function passwdGen($length = 8)
        {
            return self::$passwd;
        }
    }
}

if (!class_exists('Configuration')) {
    class Configuration {
        private static $values = array();

        public static function get($key, $idLang = null, $idShop = null, $idShopGroup = null) {
            if (isset(self::$values[$key])) {
                if(is_array(self::$values[$key])) {
                    return json_encode(self::$values[$key]);
                }
                return self::$values[$key];
            }
            return null;

            // return isset(self::$values[$key]) ? self::$values[$key] : null;
        }

        public static function set($key, $values, $idShop = null) {
            self::$values[$key] = $values;
            return true;
        }

        public static function deleteByName($key, $idShop = null) {
            unset(self::$values[$key]);
            return true;
        }
    }
}

if (!class_exists('Db')) {
    class Db {
        public $lastQuery;
        public $executeReturn;
        public $getValueReturn;
        public $executeSReturn;
        public $getRowReturn;
        public $valueMap = array();
        public $executeSReturnMap = array();

        private static $instance;

        public static function getInstance() {
            if (!self::$instance) {
                self::$instance = new self(null, null, null, null);
            }
            return self::$instance;
        }

        public function execute($query) {
            $this->lastQuery = $query;
            return $this->executeReturn;
        }

        public function getValue($query) {
            $this->lastQuery = $query;
            foreach ($this->valueMap as $key => $value) {
                if (strpos($query, $key) !== false) {
                    return $value;
                }
            }
            return $this->getValueReturn;
        }

        public function getRow($query) {
            $this->lastQuery = $query;
            return $this->getRowReturn;
        }

        public function executeS($query) {
            $this->lastQuery = $query;
            foreach ($this->executeSReturnMap as $key => $value) {
                if (strpos($query, $key) !== false) {
                    return $value;
                }
            }
            return $this->executeSReturn;
        }
    }
}

// Ensure a fresh Db instance state helper for tests
function tests_reset_db_stub()
{
    $db = \Db::getInstance();
    $db->lastQuery = null;
    $db->executeReturn = true;
    $db->getValueReturn = null;
    $db->executeSReturn = array();
    $db->getRowReturn = null;
    $db->valueMap = array();
    $db->executeSReturnMap = array();
}

tests_reset_db_stub();
