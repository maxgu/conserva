<?php

namespace T4\DomainModels\DataSource;

class RedisException extends \t4_Exception {}

class Redis {
    
    protected $_namespace;
    
    /**
     * @var Rediska
     */
    protected $_db;

    public function __construct() {
        
        if (empty($this->_namespace)) {
            throw new RedisException("Namespace cannot be empty");
        }
        
        $this->_db = \Rediska_Manager::get();
    }

    public function getNamespace() {
        return $this->_namespace;
    }
    
    protected function _getKey($scope) {
        return sprintf($this->getNamespace(), $scope);
    }

    /**
     * @return Rediska
     */
    protected function _getDb() {
        return $this->_db;
    }

    
}
