<?php

namespace Conserva;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;

class Module implements ConsoleUsageProviderInterface, AutoloaderProviderInterface, ConfigProviderInterface {

    const VERSION = '0.8';
    const NAME = 'Conserva - Database backup command line Tool';

    protected $config;

    public function getConfig() {
        return $this->config = include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConsoleBanner(ConsoleAdapterInterface $console) {
        return self::NAME . ' ver. ' . self::VERSION;
    }

    public function getConsoleUsage(ConsoleAdapterInterface $console) {
        if (!empty($this->config->disableUsage)) {
            return null; // usage information has been disabled
        }

        // TODO: Load strings from a translation container
        return array(
            'Basic information:',
            '--version'         => 'display current Zend Framework version',
            
            'Backup MySQL databases:',
            'mysql --user=<user> --password=<password>' => 'access to database',
            array('<user>', 'database user'),
            array('<password>', 'database password'),
        );
    }

}
