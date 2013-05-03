<?php

namespace Conserva\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class HelpController extends AbstractActionController {

    public function showAction() {
        // чушь, не понятно как это вывести по-нормальному
        $message = <<<EOT
Conserva - Database backup command line Tool ver. 0.8

Basic information:
   --version    display current version                                                        

Backup MySQL databases:
   mysql --config=<configFile>    run dump by config (more options)                            

  <configFile>    path to config file (if not set, search in current directory                 
                  (./config.ini))                                                              

conserva-backup.org
[t4web.com.ua production]

EOT;
        
        echo $message;
        
    }

}
