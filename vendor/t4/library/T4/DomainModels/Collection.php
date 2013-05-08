<?php

namespace T4\DomainModels;

use Closure;

class Collection implements \SeekableIterator, \ArrayAccess, \Countable {

    /**
     * @var array
     */
    protected $_collection = array();
    
    /**
     * @var array
     */
    protected $_keys = array();
    
    /**
     * @var integer
     */
    protected $_position = 0;
    
    /**
     * @var string
     */
    protected $_model = 'T4\DomainModels\Model';
    
    public function __construct(array $data = array()) {
        
        $this->fill($data);
    }

    public function fill(array $data) {
        $className = $this->_model;
        foreach ($data as $key => $value) {
            $this->addKey($key);
            $this->offsetSet($key, new $className($value));
        }
    }
    
    public function count() {
        return count($this->_collection);
    }
    
    public function isEmpty() {
        return empty($this->_collection);
    }
    
    public function seek($position) {
      $this->position = $position;
      
      if (!$this->valid()) {
          throw new OutOfBoundsException("invalid seek position ($position)");
      }
    }

    public function toArray() {
        return $this->_collection;
    }
    
    /**
     * Returns all data as an array.
     * @return array
     */
    public function toArrayRecursive() {
        
        $data = array();
        
        foreach ($this->_collection as $id => $entry) {
            $data[$id] = $entry->toArray();
        }
        
        return $data;
    }
    
    public function getLast() {
        return end($this->_collection);
    }
    
    public function getFirst() {
        return reset($this->_collection);
    }
    
    public function toKeyValue($keyGetter, $valueGetter) {
        
        $keyValues = array();
        foreach ($this->_collection as $object){
            $keyValues[$object->{$keyGetter}()] = $object->{$valueGetter}();
        }
        
        return $keyValues;
    }
    
    public function keys() {
        return $this->_keys;
    }
    
    function rewind() {
        $this->_position = 0;
    }

    function current() {
        
        if ($this->valid() === false) {
            return null;
        }
        
        return $this->_collection[$this->_keys[$this->_position]];
    }

    function key() {
        return $this->_keys[$this->_position];
    }

    function next() {
        ++$this->_position;
    }

    function valid() {
        return (isset($this->_keys[$this->_position]) && isset($this->_collection[$this->_keys[$this->_position]]));
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_collection[] = $value;
        } else {
            $this->_collection[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->_collection[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->_collection[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->_collection[$offset]) ? $this->_collection[$offset] : null;
    }
    
    public function accept($function) {
        $function($this->_collection);
        $this->_keys = array_keys($this->_collection);
    }
    
    public function rebuildByKey($keyProperty) {
        if (empty($this->_collection)) return;
        
        $newCollection = $this->_collection;
      
        $this->_resetCollection();
        
        foreach ($newCollection as $object){
            $data = $object->toArray();
            $this->_collection[$data[$keyProperty]] = $object;
            $this->_keys[] = $data[$keyProperty];
        }
    }
    
    protected function _resetCollection(){
        $this->_keys = array();
        $this->_collection = array();
        $this->_position = 0;
    }
    
    public function setCollection(array $collection){
        
        $this->_resetCollection();
        
        if (empty($collection)) return;
        
        $this->_keys = array_keys($collection);
        $this->_collection = $collection;
    }
    
    public function addCollection($collection){
        
        foreach ($collection as $value) {
            $this->add($value);
        }
    }
    
    public function addKey($value) {
        if (!is_null($value)) {
            $this->_keys[] = $value;
        }
    }
    
    public function add($element, $key = null) {
        
        if ($key === null) {
            $key = $this->count();
        }
        
        $this->addKey($key);
        $this->offsetSet($key, $element);
    }
    
    /**
     * Applies the given function to each element in the collection and returns
     * a new collection with the elements returned by the function.
     *
     * @param Closure $func
     * @return Collection
     */
    public function map(Closure $func) {
        return new static(array_map($func, $this->_collection));
    }
    
    /**
     * Applies the given predicate p to all elements of this collection,
     * returning true, if the predicate yields true for all elements.
     *
     * @param Closure $p The predicate.
     * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(Closure $func) {
        foreach ($this->_collection as $key => $element) {
            if (!$func($key, $element)) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Tests for the existence of an element that satisfies the given predicate.
     *
     * @param Closure $p The predicate.
     * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(Closure $p) {
        foreach ($this->_collection as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns all the elements of this collection that satisfy the predicate p.
     * The order of the elements is preserved.
     *
     * @param Closure $p The predicate used for filtering.
     * @return Collection A collection with the results of the filter operation.
     */
    public function filter(Closure $p) {
        return new static(array_filter($this->_collection, $p));
    }
    
    /**
     * Partitions this collection in two collections according to a predicate.
     * Keys are preserved in the resulting collections.
     *
     * @param Closure $p The predicate on which to partition.
     * @return array An array with two elements. The first element contains the collection
     *               of elements where the predicate returned TRUE, the second element
     *               contains the collection of elements where the predicate returned FALSE.
     */
    public function partition(Closure $p) {
        $coll1 = $coll2 = array();
        foreach ($this->_collection as $key => $element) {
            if ($p($key, $element)) {
                $coll1[$key] = $element;
            } else {
                $coll2[$key] = $element;
            }
        }
        return array(new static($coll1), new static($coll2));
    }
    
    /**
     * Extract a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return the
     * selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function slice($offset, $length = null) {
        return array_slice($this->_collection, $offset, $length, true);
    }
    
    /**
     * Searches for a given element and, if found, returns the corresponding key/index
     * of that element. The comparison of two elements is strict, that means not
     * only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element The element to search for.
     * @return mixed The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element) {
        return array_search($element, $this->_collection, true);
    }
    
    /**
     * Checks whether the given element is contained in the collection.
     * Only element values are compared, not keys. The comparison of two elements
     * is strict, that means not only the value but also the type must match.
     * For objects this means reference equality.
     *
     * @param mixed $element
     * @return boolean TRUE if the given element is contained in the collection,
     *          FALSE otherwise.
     */
    public function contains($element) {
        foreach ($this->_collection as $collectionElement) {
            if ($element === $collectionElement) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * @return array
     */
    public function getValues($geterName) {
        $ids = array();
        
        $this->forAll(function ($key, $el) use (&$ids, $geterName) {
            $value = $el->$geterName();
            
            if (is_object($value)) {
                $ids[spl_object_hash($value)] = $value;
            } else {
                $ids[$value] = $value;
            }
            
            return true;
        });
        
        return array_values($ids);
    }
    
    public function removeDuplicates($geterName) {
        $uniq = array();
        
        $newCollection = $this->filter(function ($el) use (&$uniq, $geterName) {
            if (isset($uniq[$el->$geterName()])) return false;
            
            $uniq[$el->$geterName()] = $el;
            return true;
        });

        $this->setCollection($newCollection->toArray());
    }
}