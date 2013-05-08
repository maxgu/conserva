<?php

namespace ConservaTest\Controller;

class InfoControllerTest extends AbstractControllerTest {
    
    public function testVersionAction() {
        ob_start();
        $this->dispatch('--version');
        $output = ob_get_contents();
        ob_end_clean();
        
        $this->getResponse()->setContent($output);
        
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('conserva');
        $this->assertControllerName('conserva\controller\info');
        $this->assertActionName('version');
        $this->assertConsoleOutputContains('The Conserva is using Zend Framework');
    }

}
