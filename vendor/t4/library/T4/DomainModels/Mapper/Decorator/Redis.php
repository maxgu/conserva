<?php

namespace T4\DomainModels\Mapper\Decorator;

class Redis {
    
    /**
     * Не обязательно маппер Db, может быть маппер API, XML и т.п.
     * 
     * @var \T4\DomainModels\Mapper\MapperInterface 
     */
    protected $_mapper;
    
    /**
     *
     * @var \T4\DomainModels\DataSource\Redis 
     */
    protected  $_redisDataSource;
    protected  $_redisDataSourceClass = '\T4\DomainModels\DataSource\Redis';
    
    public function __construct(\T4\DomainModels\Mapper\MapperInterface $mapper) {
        $this->setMapper($mapper);
    }
    
    /**
     * 
     * @param \T4\DomainModels\DataSource\Redis $redis
     * @return \T4\DomainModels\Mapper\Redis
     */
    public function setRedisDataSource(\T4\DomainModels\DataSource\Redis $redisDataSource) {
        $this->_redisDataSource = $redisDataSource;
        return $this;
    }
    
    /**
     * 
     * @return \T4\DomainModels\DataSource\Redis $redis
     */
    public function getRedisDataSource() {
        if (null === $this->_redisDataSource) {
            $class = $this->_redisDataSourceClass;
            $this->_redisDataSource = new $class();
        }
        
        return $this->_redisDataSource;
    }
    
    /**
     * 
     * @param \T4\DomainModels\Mapper\MapperInterface $mapper
     * @return \T4\DomainModels\Mapper\Decorator\Redis
     */
    public function setMapper(\T4\DomainModels\Mapper\MapperInterface $mapper) {
        $this->_mapper = $mapper;
        return $this;
    }
    
    /**
     * @return \T4\DomainModels\Mapper\Db
     */
    protected function _getMapper() {
        return $this->_mapper;
    }
}