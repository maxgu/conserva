<?php

namespace ConservaTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use Zend\Mvc\MvcEvent;
use Zend\Console\Response;

class AbstractControllerTest extends AbstractConsoleControllerTestCase {
    
    protected $traceError = true;
    
    public function setUp() {
        $this->setApplicationConfig(
                array(
                    'modules' => array(
                        'Conserva',
                    ),
                    'module_listener_options' => array(
                        'config_glob_paths' => array(
                            'config/autoload/{,*.}{global,local}.php',
                        ),
                        'module_paths' => array(
                            '.',
                            './vendor',
                        ),
                    ),
                )
        );
        parent::setUp();
        
        // prevent exit(0);
        $events = $this->getApplication()->getEventManager();
        $events->attach(MvcEvent::EVENT_FINISH, function($e) {
            $response = $e->getResponse();
            if (!$response instanceof Response) {
                return false; // there is no response to send
            }
            
            $e->stopPropagation();
            
        });
    }
}
