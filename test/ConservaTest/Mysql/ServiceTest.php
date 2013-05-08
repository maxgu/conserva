<?php

namespace ConservaTest\Mysql;

use ConservaTest\Controller\AbstractControllerTest;
use Conserva\Config\Model as Config;

class ServiceTest extends AbstractControllerTest {
    
    /**
     *
     * @var \Conserva\Mysql\Service
     */
    protected $service;
    
    public function setUp() {
        parent::setUp();
        
        $this->service = $this->getApplication()->getServiceManager()->get('MysqlService');
    }
    
    public function testCheckInstance() {
        $this->assertInstanceOf('\Conserva\Mysql\Service', $this->service);
    }
    
    public function testGetDatabaseInstance() {
        
        $config = new Config(dirname(dirname(dirname(__DIR__))) . '/config/example.config.ini');
        
        $this->service->setConfig($config);
        
        $databaseCollection = $this->service->getDatabases('root', '1');
        
        $this->assertInstanceOf('\Conserva\Database\Collection', $databaseCollection);
    }
    
    public function testProcessDump() {
        /*
        $config = new Config(dirname(dirname(dirname(__DIR__))) . '/config/example.config.ini');
        
        $this->service->setConfig($config);
        
        $databaseCollection = new Database\Collection();
        $databaseCollection->add(new Database\Model(array('name' => 'test')));
        
        $this->service->processDump($databaseCollection);
        
        $this->assertInstanceOf('\Conserva\Database\Collection', $databaseCollection);
         */
    }

}