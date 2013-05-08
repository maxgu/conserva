<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ConfigController extends AbstractActionController {
    
    const DEFAULT_CONFIG_PATH = '/config/example.config.ini';
    
    public function createAction() {
        
        $basedir = dirname(dirname(dirname(dirname(__FILE__))));
        
        if (!file_exists($basedir . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_PATH)) {
            throw new \RuntimeException('Can\'t find example config.');
        }
        
        if (!is_writable('./')) {
            throw new \RuntimeException(sprintf('Directory (%s) is not writable.', realpath('.')));
        }
        
        copy($basedir . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_PATH, './config.ini');
    }
}
