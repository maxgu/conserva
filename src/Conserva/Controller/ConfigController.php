<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ConfigController extends AbstractActionController {
    
    const DEFAULT_CONFIG_PATH = 'config/example.config.ini';
    
    public function createAction() {
        if (!file_exists(self::DEFAULT_CONFIG_PATH)) {
            throw new \RuntimeException('Can\'t find example config.');
        }
        
        if (!is_writable('./')) {
            throw new \RuntimeException(sprintf('Directory (%s) is not writable.', realpath('.')));
        }
        
        copy(self::DEFAULT_CONFIG_PATH, './config.ini');
    }
}
