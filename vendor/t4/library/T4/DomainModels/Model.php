<?php

namespace T4\DomainModels;

use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Stdlib\ArraySerializableInterface;

/**
 *@method int|null getId(); 
 */
class Model implements ArraySerializableInterface {
    
    /**
     * The data for each column in the row (column_name => value).
     * The keys must match the physical names of columns in the
     * table for which this row is defined.
     *
     * @var array
     */
    protected $data = array();
    
    protected static $propertyMap = array();
    
    /**
     * 
     * @param \Zend\Filter\Word\UnderscoreToCamelCase
     */
    protected $filterWord;
    
    /**
     * 
     * @param \Zend\Filter\Word\CamelCaseToUnderscore
     */
    protected $filterCamelCaseToUnderscore;
    
    
    public function __construct(array $data = array()) {
        
        $this->fill($data);
    }
    
    public function getDataFields() {
        return array_keys($this->getData());
    }
    
    public function fill(array $data = array()) {
        
        foreach ($data as $columnName => $value) {
            
            if (!$this->issetColumnName($columnName)) {
                continue;
            }
            
            $this->setToData($columnName, $value);
        }
    }
    
    protected function getFilterWord() {
        
        if (null === $this->filterWord) {
            $this->filterWord = new UnderscoreToCamelCase();
        }
        
        return $this->filterWord;
    }
    
    protected function getFilterCamelCaseToUnderscore() {
        
        if (null === $this->filterCamelCaseToUnderscore) {
            $this->filterCamelCaseToUnderscore = new CamelCaseToUnderscore();
        }
        return $this->filterCamelCaseToUnderscore;
    }

    protected function issetColumnName($columnName) {
        
        if (array_key_exists($columnName, $this->toArray())) {
            return true;
        }
        return false;
    }
    
    protected function setToData($columnName, $value) {
        
        if (!$this->issetColumnName($columnName)) {
            return;
        }
        
        $this->data[$columnName] = $value;
    }
    
    protected function getFromData($columnName) {
        
        if (!$this->issetColumnName($columnName)) {
            return null;
        }
        
        return $this->data[$columnName];
    }
    
    /**
     * Returns the column/value data as an array.
     * @return array
     */
    public function toArray() {
        return (array)$this->data;
    }
    
    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array) {
        $this->fill($array);
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy() {
        return $this->toArray();
    }
    
    public function __call($method, array $args) {
        
        $propertyRaw = substr($method, 3);
        
        // setCurrencyName
        $propertyName = $this->getPropertyName($propertyRaw);
        
        if (strpos($method, 'set') !== false) {
            return $this->setToData($propertyName, $args[0]);
        } elseif (strpos($method, 'get') !== false ) {
            return $this->getFromData($propertyName);
        }
        
        throw new \Exception('Invalid method "' . $method . '"');
    }
    
    protected function getPropertyName($word, $filter = null) {
        
        // TODO: учитывать другие фильтры
        if (!isset(self::$propertyMap[$word])) {
            if (!$filter) {
                $filter = $this->getFilterCamelCaseToUnderscore();
            }

            self::$propertyMap[$word] = strtolower($filter->filter($word));
        }
        
        return self::$propertyMap[$word];
    }
    
}