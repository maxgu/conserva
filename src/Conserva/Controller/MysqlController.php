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
        
        if (empty($configFile)) {
            $configFile = 'config.ini';
        }
        
        if (!file_exists($configFile)) {
            throw new \RuntimeException("file $configFile does not exists");
        }
        
        $config = new Config($configFile);
        
        /* @var $configService \Conserva\Config\Service */
        $configService = $this->getServiceLocator()->get('ConfigService');
        
        $configService->check($config);
        
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
        
        //$console->writeLine('done', Color::GREEN);
    }
}
