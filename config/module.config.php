<?php
return array(
    'Conserva' => array(
        'disableUsage' => false,    // set to true to disable showing available Conserva commands in Console.
    ),

    // -----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=

    'controllers' => array(
        'invokables' => array(
            'Conserva\Controller\Info'     => 'Conserva\Controller\InfoController',
            'Conserva\Controller\Mysql'    => 'Conserva\Controller\MysqlController',
            'Conserva\Controller\Config'   => 'Conserva\Controller\ConfigController',
            'Conserva\Controller\Help'     => 'Conserva\Controller\HelpController',
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'version' => array(
                    'options' => array(
                        'route'    => '--version',
                        'defaults' => array(
                            'controller' => 'Conserva\Controller\Info',
                            'action'     => 'version',
                        ),
                    ),
                ),
                'mysql' => array(
                    'options' => array(
                        'route'    => 'mysql [--config=]',
                        'defaults' => array(
                            'controller' => 'Conserva\Controller\Mysql',
                            'action'     => 'backup',
                        ),
                    ),
                ),
                'config' => array(
                    'options' => array(
                        'route'    => 'create config',
                        'defaults' => array(
                            'controller' => 'Conserva\Controller\Config',
                            'action'     => 'create',
                        ),
                    ),
                ),
                'help' => array(
                    'options' => array(
                        'route'    => 'help',
                        'defaults' => array(
                            'controller' => 'Conserva\Controller\Help',
                            'action'     => 'show',
                        ),
                    ),
                ),
            ),
        ),
    ),
    
    'service_manager' => array(
        'factories' => array(
            'MysqlService'     => function ($sm) {
                return new Conserva\Mysql\Service($sm);
            },
            'ConfigService'     => function ($sm) {
                return new Conserva\Config\Service($sm);
            },
        ),
    ),

);
