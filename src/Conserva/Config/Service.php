<?php

namespace Conserva\Config;

class Service extends \T4\DomainModels\Service {
    
    public function check(Model $config) {
        $host       = $config->getDatabase('host');
        $username   = $config->getDatabase('username');
        $password   = $config->getDatabase('password');
        
        if (empty($host)) {
            throw new \RuntimeException('bad config: host cannot be empty');
        }
        
        if (empty($username)) {
            throw new \RuntimeException('bad config: username cannot be empty');
        }
        
        if (empty($password)) {
            throw new \RuntimeException('bad config: password cannot be empty');
        }
    }
    
}
