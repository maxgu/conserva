<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Version\Version;
use Zend\Console\ColorInterface as Color;
use Conserva\Module;

class InfoController extends AbstractActionController
{

    public function versionAction()
    {
        $console = $this->getServiceLocator()->get('console');

        $zf2Path = $this->getZF2Path();
        if (file_exists($zf2Path . '/Zend/Version/Version.php')) {
            require_once $zf2Path . '/Zend/Version/Version.php';
            $msg = 'The application in this folder is using Zend Framework ';
        } else {
            $msg = 'The Conserva is using Zend Framework ';
        }

        $console->writeLine(Module::NAME . ' ver. ' . Module::VERSION, Color::GREEN);
        $console->writeLine($msg . Version::VERSION);
    }

}
