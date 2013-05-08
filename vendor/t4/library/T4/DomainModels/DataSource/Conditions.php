<?php

namespace T4\DomainModels\DataSource;

class Conditions extends \T4\DomainModels\Collection {
    
    protected $_model = 'T4\DomainModels\DataSource\Condition';
    
    protected $_fields = array();
    protected $_fieldAliasees = array();
    
    public function fill(array $searchFields) {
        $className = $this->_model;
        
        $searchFields = $this->_prepare($searchFields);
       
        foreach ($this->_fields as $fieldAlias => $expression) {
            if (!array_key_exists($fieldAlias, $searchFields)) {
                continue;
            }
            
            $this->addKey($fieldAlias);
            
            $data = array(
                'field_alias' => $fieldAlias, 
                'field_name'  => $this->getFieldNameByAlias($fieldAlias), 
                'expression'  => $expression, 
                'value'       => $searchFields[$fieldAlias]
            );
            
            $this->offsetSet($fieldAlias, new $className($data));
        }
    }
    
    private function _prepare(array $searchFields) {
        return array_filter($searchFields, function($value){
            return ($value != -1) && ($value != '');
        });
    }
    
    public function getFieldNameByAlias($alias) {
        
        if (isset($this->_fieldAliasees[$alias])) {
            return $this->_fieldAliasees[$alias];
        }
        
        return $alias;
    }
    
    public function getQueryFields() {
        
        $fields = array();
        
        foreach ($this->keys() as $alias) {
            $fields[] = $this->getFieldNameByAlias($alias);
        }
        
        return $fields;
    }
}
