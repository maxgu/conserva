<?php
return array(
    'Conserva' => array(
        'disableUsage' => false,    // set to true to disable showing available Conserva commands in Console.
    ),

    // -----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=

    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Conserva\Controller\Info'     => 'Conserva\Controller\InfoController',
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
            ),
        ),
    ),

);
