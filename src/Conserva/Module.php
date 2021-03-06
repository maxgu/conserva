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
                
see ./conserva help

EOT;
        
        $exceptionStrategy->setMessage($message);
    }
    
    public function prepareRouteNotFoundStrategy() {
        $notFoundStrategy = $this->sm->get('Conserva-RouteNotFoundStrategy');
        
        $message = <<<EOT
:banner

:usage

An error occurred:
   :report

see ./conserva help

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
            
            'Config:',
            'create config'     => 'will create config.ini in current folder',
            
            'Backup MySQL databases:',
            'mysql --config=<configFile>' => 'run dump by config (more options)',
            array('<configFile>', 'path to config file (if not set, search in current directory (./config.ini))'),
            
            'conserva-backup.org',
            '[t4web.com.ua production]',
        );
    }

}
