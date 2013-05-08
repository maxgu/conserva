<?php

namespace T4\DomainModels;

class Service {
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    
    protected $errors = array();
    
    protected $mapperDb;
    protected $mapperDbClass = 'T4\DomainModels\Mapper\Db';
    
    protected $mapperRedis;
    protected $mapperRedisClass = 'T4\DomainModels\Mapper\Redis';
    
    protected $mapperDecoratorRedis;
    protected $mapperDecoratorRedisClass = 'T4\DomainModels\Mapper\Decorator\Redis';
    
    public function __construct(\Zend\ServiceManager\ServiceManager $sm) {
        $this->serviceManager = $sm;
    }
    
    /**
     * 
     * @return \Zend\ServiceManager\ServiceManager
     */
    protected function getServiceManager() {
        return $this->serviceManager;
    }


    /**
     * @return \T4\DomainModels\Mapper\Db
     */
    protected function getMapperDb() {
        
        if (null === $this->mapperDb) {
            $this->mapperDb = $this->getServiceManager()->get($this->mapperDbClass);
        }
        
        return $this->mapperDb;
    }
    
    /**
     * 
     * @param array $conds
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return \T4\DomainModels\Collection
     */
    public function getAll(array $conds = array(), array $sort = array(), $limit = 20, $offset = 0) {
        
        $conds = new DataSource\Conditions($conds);
        
        return $this->getMapperDb()->getAll($conds, $sort, $limit, $offset);
    }
    
    public function getOne(array $conds = array(), array $sort = array()) {
        
        $conds = new DataSource\Conditions($conds);
        
        return $this->getMapperDb()->getOne($conds, $sort);
    }
    
    public function create(Model $model) {
        return $this->getMapperDb()->create($model);
    }
    
    public function update(Model $model, $where = null) {
        return $this->getMapperDb()->update($model, $where);
    }
    
    public function save(Model $model) {
        
        if($id = $model->getId()){
            return $this->create($model);
        } else{
            return $this->update($model, array('id' => $id));
        }
       
    }
}
