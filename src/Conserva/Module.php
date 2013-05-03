<?php

namespace Conserva;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\Mvc\MvcEvent;
use Conserva\View\Console\RouteNotFoundStrategy;

class Module implements ConsoleUsageProviderInterface, AutoloaderProviderInterface, ConfigProviderInterface {

    const VERSION = '0.8';
    const NAME = 'Conserva - Database backup command line Tool';

    protected $config;
    protected $routeNotFoundStrategy;
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $sm;
    
    public function onBootstrap(MvcEvent $event) {
        $em         = $event->getApplication()->getEventManager();
        $this->sm   = $event->getApplication()->getServiceManager();
        $em->attach($this->getRouteNotFoundStrategy());
        
        $this->prepareExceptionStrategy();
        $this->prepareRouteNotFoundStrategy();
    }
    
    public function prepareExceptionStrategy() {
        $exceptionStrategy = $this->sm->get('ExceptionStrategy');
        
        $message = <<<EOT
 An error occurred:
    :message
----------------------------------------------------------------------
:file::line

EOT;
        
        $exceptionStrategy->setMessage($message);
    }
    
    public function prepareRouteNotFoundStrategy() {
        $notFoundStrategy = $this->sm->get('Conserva-RouteNotFoundStrategy');
        
        $message = <<<EOT
 :banner
 An error occurred:
   :report

EOT;
        
        $notFoundStrategy->setMessage($message);
    }
    
    public function getRouteNotFoundStrategy() {
        if ($this->routeNotFoundStrategy) {
            return $this->routeNotFoundStrategy;
        }

        $this->routeNotFoundStrategy = new RouteNotFoundStrategy();

        $this->sm->setService('Conserva-RouteNotFoundStrategy', $this->routeNotFoundStrategy);

        return $this->routeNotFoundStrategy;
    }

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
            '--version'         => 'display current version',
            
            'Backup MySQL all databases:',
            'mysql --user=<user> --password=<password>' => 'access to database',
            array('<user>', 'database user'),
            array('<password>', 'database password'),
            
            'Backup MySQL databases by config:',
            'mysql --config=<configFile>' => 'run dump by config (more options)',
            array('<configFile>', 'path to config file'),
            
            'conserva-backup.org',
            '[t4web.com.ua production]',
        );
    }

}
