<?php

namespace T4\DomainModels\DataSource;

use Zend\Db\TableGateway\TableGateway;
use T4\DomainModels\DataSource\Conditions;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class TableException extends \Exception {}

class Table {
    
    protected $name;
    
    /**
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $db;
    
    /**
     *
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;
    
    /**
     *
     * @var \Zend\Db\Sql\Select;
     */
    protected $select;
    
    /**
     *
     * @var type \Zend\Db\Sql\Sql
     */
    protected $sql;


    /**
     * @var array 
     */
    protected $dependentTables = array();
    
    /**
     * for fast detect table
     * 
     * build automaticly in join()
     * @var array
     */
    protected $dependentFields;

    public function __construct($dbAdapter) {
        if (empty($this->name)) {
            throw new TableException("Table name cannot be empty");
        }
        
        $this->db = $dbAdapter;
        $this->sql = new Sql($this->db);
    }
    
    public function assemble(Select $select) {
        return $this->sql->getSqlStringForSqlObject($select);
    }

    public function getName() {
        return $this->name;
    }
    
    public function getSelect() {
        if (!$this->select) {
            $this->select = new Select($this->getName());
        }
        
        return $this->select;
    }
    
    /**
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getGateway() {
        if (empty($this->tableGateway)) {
            $this->tableGateway = new TableGateway($this->getName(), $this->db);
        }
        
        return $this->tableGateway;
    }

    /**
     * 
     * @param \T4\DomainModels\DataSource\Conditions $cond
     * @param array $sort
     * @param int $limitOrPage
     * @param int $offsetOrLimit
     * @param boolean $isPage
     * @return array
     */
    public function getAll(Conditions $conds, array $sort, $limitOrPage, $offsetOrLimit) {
        return $this->getGateway()->select();
    }
    
    public function getOne(Conditions $conds, array $sort) {
        
        $this->prepareConditions($conds);
        
        return $this->getGateway()->selectWith($this->getSelect());
    }
    
    /**
     * 
     * @param array $set
     * @return int
     */
    public function insert(array $set) {
        return $this->getGateway()->insert($set);
    }
    
    public function update(array $set, $where = null) {
        return $this->getGateway()->update($set, $where);
    }

    public function getLastInsertValue() {
        return $this->getGateway()->getLastInsertValue();
    }

    /**
     * 
     * @param \T4\DomainModels\DataSource\Conditions $cond
     */
    protected function prepareConditions(Conditions $conds) {
        
        /* @var $condition \T4\DomainModels\DataSource\Condition */
        foreach ($conds as $condition) {
            
            $this->getSelect()->where(
                array(
                    $this->getName() . '.' . $condition->getExpression() => $condition->getValue()
                )
            );
        }
    }
    
}
