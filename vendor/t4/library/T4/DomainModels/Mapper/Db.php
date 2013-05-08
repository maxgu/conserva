<?php

namespace T4\DomainModels\Mapper;

use T4\DomainModels;
use T4\DomainModels\DataSource\Conditions;

class Db extends MapperInterface {
    
    protected $table;
    protected $tableClass       = 'T4\DomainModels\DataSource\Table';
    protected $modelClass       = 'T4\DomainModels\Model';
    protected $collectionClass  = 'T4\DomainModels\Collection';
    
    protected $fieldsMap = array();

    public function __construct($sm) {
        $this->setTable($sm->get($this->tableClass));
    }
    
    /**
     * @return DomainModels\DataSource\Table
     */
    public function getTable() {
        return $this->table;
    }
    
    public function setTable(DomainModels\DataSource\Table $table) {
        $this->table = $table;
    }
    
    public function getFieldsMap() {
        return $this->fieldsMap;
    }
    
    public function create(DomainModels\Model $model) {
        $affectedRows = $this->getTable()->insert($model->toArray());
        
        if (method_exists($model, 'setId')) {
            $model->setId($this->getTable()->getLastInsertValue());
        }
        
        return $affectedRows;
    }
    
    public function update(DomainModels\Model $model, $where = null) {
        return $this->getTable()->update($model->toArray(), $where);
    }
    
    public function delete($where) {
        return $this->getTable()->delete($where);
    }
    
    /**
     * 
     * @param array | \T4\DomainModels\DataSource\Conditions $cond
     * @param array $sort
     * @return T4\DomainModels\Model
     */
    public function getOne($cond, array $sort) {
        /* @var $row \Zend\Db\ResultSet\ResultSet */
        $row = $this->getTable()->getOne($cond, $sort);
        
        if (!$row) {
            return false;
        }
        
        $data = $row->toArray();
        
        $className = $this->modelClass;
        
        return new $className($data[0]);
    }
    
    /**
     * 
     * @param \T4\DomainModels\DataSource\Conditions $cond
     * @param array $sort
     * @param integer $limit
     * @param integer $offset
     * @return T4\DomainModels\Collection
     */
    public function getAll(Conditions $conds, array $sort, $limit, $offset) {
        $rows = $this->getTable()->getAll($conds, $sort, $limit, $offset);
        
        $modelClass = $this->modelClass;
        $collectionClass = $this->collectionClass;
        $collection = new $collectionClass();
        
        foreach($rows as $row) {
            $collection->add(new $modelClass((array)$row), $row->id);
        }
        
        return $collection;
    }
    
    public function getCount($cond) {
        return $this->getTable()->getCount($cond);
    }
    
    protected function prepareFields($rows) {
        
        $fieldsMap = $this->getFieldsMap();
        if (empty($fieldsMap)) {
            return $rows;
        }
        
        foreach ($rows as $key => $row) {
            foreach ($this->getFieldsMap() as $modelField => $sourceField) {
                $rows[$key][$modelField] = $row[$sourceField];
            }
        }
        
        return $rows;
    }
    
    public function fetchAllWithUserFilter($cond, array $userFilterParams, $bindField, array $sort, $limit, $offset) {
        $rows = $this->getTable()->fetchAllWithUserFilter($cond, $userFilterParams, $bindField, $sort, $limit, $offset);
        
        $className = $this->collectionClass;
        return new $className($this->prepareFields($rows));
    }    
}