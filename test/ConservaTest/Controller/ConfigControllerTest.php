<?php

namespace ConservaTest\Controller;

class ConfigControllerTest extends AbstractControllerTest {
    
    public function testCreateAction() {
        
        $currentDir = getcwd();
        
        chdir(dirname(dirname(dirname(__DIR__))));
        
        @unlink('./config.ini');
        
        $this->dispatch('create config');
        
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('conserva');
        $this->assertControllerName('conserva\controller\config');
        $this->assertActionName('create');
        $this->assertFileExists('./config.ini');
        
        chdir($currentDir);
    }

}
