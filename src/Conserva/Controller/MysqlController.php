<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Version\Version;
use Zend\Console\ColorInterface as Color;
use Conserva\Module;

class MysqlController extends AbstractActionController {
    
    public function backupAction() {
        $request = $this->getRequest();
        $console = $this->getServiceLocator()->get('console');
        
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
}
