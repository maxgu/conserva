<?php

return array(
    'modules' => array(
        'Conserva',
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            '../../../config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            '.',
            'vendor',
        ),
    ),
);