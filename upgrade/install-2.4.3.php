<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_4_3(payline $module)
{
    $paylineTabCollection = \Tab::getCollectionFromModule($module->name);
    foreach ($paylineTabCollection as $paylineTab) {
        if ($paylineTab->class_name == 'AdminModules') {
            $paylineTab->delete();
        }
    }

    return true;
}