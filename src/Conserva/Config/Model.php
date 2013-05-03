<?php

namespace Conserva\Config;

use Conserva\Database\Model as Database;

class Model extends \T4\DomainModels\Model {
    
    protected $databases;

    protected $data = array(
        'database'          => null,
        'save_last'         => null,
        'own_folder_for_db' => null,
        'folder_prefix'     => null,
    );
    
    public function __construct($configFile) {
        $reader = new \Zend\Config\Reader\Ini();
        $configData = $reader->fromFile($configFile);
        
        $this->fill($configData);
    }
    
    public function getDatabase($paramName) {
        if (!isset($this->data['database'][$paramName])) {
            return;
        }
        
        return $this->data['database'][$paramName];
    }
    
    public function setDatabase($paramName, $value) {
        $this->data['database'][$paramName] = $value;
        
        return $this;
    }
    
    public function getDatabases() {
        if (!is_null($this->databases)) {
            return $this->databases;
        }
        
        $this->databases = array();
        
        if (!isset($this->data['database']['dbname'])) {
            return $this->databases;
        }
        
        $this->databases = explode(',', $this->data['database']['dbname']);
        
        array_walk($this->databases , function (&$value, $key){
            $value = trim($value);
        });

        return $this->databases;
    }
    
    public function getDatabasePath(Database $database) {
        return $this->getFolderPath($database) . $database->getFileName();
    }
    
    public function getDatabaseZipPath(Database $database) {
        return $this->getFolderPath($database) . $database->getZipName();
    }
    
    public function getStoragePath(Database $database) {
        $path = getcwd();
        
        if ($this->getOwnFolderForDb()) {
            $path =  $path . DIRECTORY_SEPARATOR . $this->getFolderPath($database);
        }
        
        return $path;
    }
    
    public function prepareStorageFolder(Database $database) {
        if ($this->getOwnFolderForDb() && !is_dir($database->getName())) {
            mkdir($database->getName());
        }
        
        if (($this->getFolderPrefix() != '') && !is_dir($this->getFolderPath($database))) {
            mkdir($this->getFolderPath($database));
        }
    }
    
    private function getFolderPath(Database $database) {
        $path = '';
        
        if ($this->getOwnFolderForDb()) {
            $path = $database->getName() . DIRECTORY_SEPARATOR;
        }
        
        if ($this->getFolderPrefix() != '') {
            $path .= $this->getFolderPrefix() . DIRECTORY_SEPARATOR;
        }
        
        return $path;
    }
    
}
