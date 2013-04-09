<?php




$cont = $service->getMapper()->fetchAll();
$cont = $sevice->getCacheMapper($service->getMapper())->fetchAll();
$cont = $sevice->getRedisMapper($service->getImMapper())->fetchAll();



namespace T4\DomainModels\Mapper;

class IdentityMap implements MapperInterface {
    
    protected  $_redis;


    public function getRedis() {
        
        return $this->_redis;
    }
    
    public function setRedis(Table $table) {
        
        $this->_redis = $table;
        
    }
    
    public function create(Model $model) {}
    
    public function update(Model $model) {}
    
    public function delete(Model $model) {}
    
    public function fetchOne(array $cond, array $sort) {}
    
    public function fetchAll(array $cond, array $sort, $limit, $offset) {}
    
    public function getCount(array $cond) {}
    
}