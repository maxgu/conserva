<?php

namespace ConservaTest\Controller;

class HelpControllerTest extends AbstractControllerTest {
    
    public function testShowAction() {
        
        $this->dispatch('help');
        
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('conserva');
        $this->assertControllerName('conserva\controller\help');
        $this->assertActionName('show');
        $this->assertConsoleOutputContains('Basic information:');
    }

}
