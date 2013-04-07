<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\ColorInterface as Color;
use Conserva\Config\Model as Config;

class MysqlController extends AbstractActionController {
    
    public function backupAction() {
        $request = $this->getRequest();
        $console = $this->getServiceLocator()->get('console');
        
        $configFile = $request->getParam('config', null);
        
        if (!empty($configFile)) {
            return $this->backupByConfigAction();
        }
        
        $dbUser = $request->getParam('user', null);
        $dbPass = $request->getParam('password', null);
        
        /* @var $service \Conserva\Mysql\Service */
        $service = $this->getServiceLocator()->get('MysqlService');
        
        try {
            /* @var $databaseCollection \Conserva\Database\Collection */
            $databaseCollection = $service->getDatabases($dbUser, $dbPass);
        } catch (\Zend\Db\Adapter\Exception\RuntimeException $e) {
            $console->writeLine('Break with error:');
            $console->writeLine("  " . $e->getPrevious()->getMessage(), Color::RED);
            return;
        }
        
        $service->processDump($databaseCollection);
        $service->processZip($databaseCollection);
        
        $console->writeLine('done', Color::GREEN);
    }
    
    public function backupByConfigAction() {
        $request = $this->getRequest();
        $console = $this->getServiceLocator()->get('console');
        
        $configFile = $request->getParam('config', null);
        
        if (!file_exists($configFile)) {
            throw new \RuntimeException("file $configFile does not exists");
        }
        
        $config = new Config($configFile);
        
//        $reader = new \Zend\Config\Reader\Ini();
//        $config = $reader->fromFile($configFile);
        
        $dbUser = $config->getDatabase('username');
        $dbPass = $config->getDatabase('password');
        
        /* @var $service \Conserva\Mysql\Service */
        $service = $this->getServiceLocator()->get('MysqlService');
        
        $service->setConfig($config);
        
        try {
            /* @var $databaseCollection \Conserva\Database\Collection */
            $databaseCollection = $service->getDatabases($dbUser, $dbPass);
        } catch (\Zend\Db\Adapter\Exception\RuntimeException $e) {
            $console->writeLine('Break with error:');
            $console->writeLine("  " . $e->getPrevious()->getMessage(), Color::RED);
            return;
        }
        
        if ($databaseCollection->isEmpty()) {
            throw new \RuntimeException("nothing to dump");
        }
        
        $service->processDump($databaseCollection);
        $service->processZip($databaseCollection);
        $service->removeRedundant($databaseCollection);
        
        $console->writeLine('done', Color::GREEN);
    }
}
