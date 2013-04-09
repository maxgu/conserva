<?php

namespace T4\DomainModels\DataSource;

class Condition extends \T4\DomainModels\Model {
    
    protected $data = array(
        'field_alias' => null, 
        'field_name' => null, 
        'expression' => null, 
        'value'      => null);
    
    public function getFieldName() {
        return $this->getFromData('field_name');
    }
    public function getFieldAlias() {
        return $this->getFromData('field_alias');
    }
    
    public function getExpression() {
        return $this->getFromData('expression');
    }
    
    public function getValue() {
        return $this->getFromData('value');
    }
    
    public function setFieldName($value) {
        $this->setToData('field_name', $value);
    }
    public function setFieldAlias($value) {
        $this->setToData('field_alias', $value);
    }
    
    public function setExpression($value) {
        $this->setToData('expression', $value);
    }
    
    public function setValue($value) {
        $this->setToData('value', $value);
    }
}
