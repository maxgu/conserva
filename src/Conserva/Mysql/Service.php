<?php

namespace Conserva\Mysql;

use Conserva\Database;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;

class Service extends \T4\DomainModels\Service {
    
    protected $excludeDBs = array('information_schema', 'mysql', 'performance_schema', 'phpmyadmin');
    
    protected $user;
    protected $pass;
    
    /**
     *
     * @var \Conserva\Config\Model
     */
    protected $config;

    /**
     * 
     * @param string $dbUser
     * @param string $dbPass
     * @return \Conserva\Database\Collection
     */
    public function getDatabases($dbUser, $dbPass) {
        
        $this->user = $dbUser;
        $this->pass = $dbPass;
        
        $adapter = new \Zend\Db\Adapter\Adapter(array(
            'driver'    => 'Pdo_Mysql',
            'username'  => $this->user,
            'password'  => $this->pass
        ));
        
        /* @var $results Zend\Db\ResultSet\ResultSet */
        $results = $adapter->query('SHOW DATABASES', \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        
        $onlyDatabases = $this->config->getDatabases();
        
        $collection = new Database\Collection();
        foreach ($results as $result) {
            if (in_array($result['Database'], $this->excludeDBs)) {
                continue;
            }
            
            if (!empty($onlyDatabases) && !in_array($result['Database'], $onlyDatabases)) {
                continue;
            }
            
            $collection->add(new Database\Model(array('name' => $result['Database'])));
        }
        
        return $collection;
    }
    
    /**
     * 
     * @param \Conserva\Database\Collection $collection
     * @return \Conserva\Mysql\Service
     * @throws \RuntimeException
     */
    public function processDump(Database\Collection $collection) {
        /* @var $database \Conserva\Database\Model */
        foreach ($collection as $database) {
            
            $this->config->prepareStorageFolder($database);
            
            $dumpCommand = sprintf(
                    'mysqldump -u%s --password=%s %s > %s', 
                    $this->user,
                    $this->pass,
                    $database->getName(),
                    $this->config->getDatabasePath($database)
            );
            
            $process = new Process($dumpCommand, getcwd());
            if ($process->run() != 0) {
                throw new \RuntimeException('Can\'t run mysqldump.');
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Conserva\Database\Collection $collection
     * @return \Conserva\Mysql\Service
     * @throws \RuntimeException
     */
    public function processZip(Database\Collection $collection) {
        /* @var $database \Conserva\Database\Model */
        foreach ($collection as $database) {
            
            $zipCommand = sprintf(
                    'tar -cvzf %s %s', 
                    $this->config->getDatabaseZipPath($database),
                    $this->config->getDatabasePath($database)
            );
            
            $process = new Process($zipCommand, getcwd());
            if ($process->run() != 0) {
                throw new \RuntimeException('Can\'t run tar.');
            }
            
            unlink($this->config->getDatabasePath($database));
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Conserva\Mysql\Conserva\Config\Model $config
     */
    public function setConfig(\Conserva\Config\Model $config) {
        $this->config = $config;
    }
    
    public function removeRedundant(Database\Collection $collection) {
        
        /* @var $database \Conserva\Database\Model */
        foreach ($collection as $database) {
            $finder = new Finder();
            
            $finder->files()
                ->ignoreVCS(true)
                ->name($database->getName() . '*.tar.gz')
                ->in($this->config->getStoragePath($database))
            ;
            
            if ($finder->count() <= (int)$this->config->getSaveLast()) {
                continue;
            }
            
            $this->_removeOld($finder, ($finder->count() - $this->config->getSaveLast()));
            
            unset($finder);
        }
    }
    
    /**
     * 
     * @param \Symfony\Component\Finder\Finder $finder
     * @param integer $count
     */
    private function _removeOld(Finder $finder, $count) {
        $finder->sortByName();
        
        foreach ($finder as $file) {
            if ($count == 0) {
                break;
            }
            
            unlink($file);
            $count--;
        }
    }

}
