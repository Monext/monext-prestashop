<?php return array(
    'root' => array(
        'name' => 'prestashop/payline',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => null,
        'type' => 'prestashop-module',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'ddtraceweb/monolog-parser' => array(
            'pretty_version' => '1.3.1',
            'version' => '1.3.1.0',
            'reference' => 'd6715cb28700d691b20d713626856aacd3d5606c',
            'type' => 'library',
            'install_path' => __DIR__ . '/../ddtraceweb/monolog-parser',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'monext/monext-php' => array(
            'pretty_version' => '25.9',
            'version' => '25.9.0.0',
            'reference' => 'a1f716547efc0f0e74ba3171ffef102236082211',
            'type' => 'library',
            'install_path' => __DIR__ . '/../monext/monext-php',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'monolog/monolog' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'prestashop/payline' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'prestashop-module',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/cache' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'psr/container' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'psr/log' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'symfony/cache' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'symfony/cache-contracts' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'symfony/service-contracts' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
        'symfony/var-exporter' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '*',
            ),
        ),
    ),
);
