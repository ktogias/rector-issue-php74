<?php return array(
    'root' => array(
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => 'samsonasik/rector-issue-php74',
        'dev' => true,
    ),
    'versions' => array(
        'phpstan/phpstan' => array(
            'pretty_version' => '1.0.2',
            'version' => '1.0.2.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../phpstan/phpstan',
            'aliases' => array(),
            'reference' => 'e9e2a501102ba0b126b2f63a7f0a3b151056fe91',
            'dev_requirement' => true,
        ),
        'rector/rector' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'type' => 'library',
            'install_path' => __DIR__ . '/../rector/rector',
            'aliases' => array(
                0 => '0.11.x-dev',
            ),
            'reference' => '8db8a4be7b606e491ba74f706965b946f7c9c9e8',
            'dev_requirement' => true,
        ),
        'samsonasik/rector-issue-php74' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
    ),
);
