<?php

namespace T4\DomainModels\Mapper;

use T4\DomainModels\Model;

class Redis extends MapperInterface {
    
    protected  $_redis;

    
    /**
     * 
     * @param \T4\DomainModels\DataSource\Redis $redis
     * @return \T4\DomainModels\Mapper\Redis
     */
    protected function _setRedis(\T4\DomainModels\DataSource\Redis $redis) {
        $this->_redis = $redis;
        return $this;
    }
    
    
    public function create(Model $model) {}
    
    public function update(Model $model) {}
    
    public function delete(Model $model) {}
    
    public function fetchOne(array $cond, array $sort) {}
    
    public function fetchAll(array $cond, array $sort, $limit, $offset) {}
    
    public function getCount(array $cond) {}
    
}