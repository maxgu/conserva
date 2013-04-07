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
                        'route'    => 'mysql [--user=] [--password=]',
                        'defaults' => array(
                            'controller' => 'Conserva\Controller\Mysql',
                            'action'     => 'backup',
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
        ),
    ),

);
