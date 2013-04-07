<?php

namespace Conserva\Mysql;

use Conserva\Database;
use Symfony\Component\Process\Process;

class Service extends \T4\DomainModels\Service {
    
    protected $excludeDBs = array('information_schema');
    
    protected $user;
    protected $pass;

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
        
        $collection = new Database\Collection();
        foreach ($results as $result) {
            if (in_array($result['Database'], $this->excludeDBs)) {
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
            $dumpCommand = sprintf(
                    'mysqldump -u%s --password=%s %s > %s', 
                    $this->user,
                    $this->pass,
                    $database->getName(),
                    $database->getFileName()
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
                    $database->getZipName(),
                    $database->getFileName()
            );
            
            $process = new Process($zipCommand, getcwd());
            if ($process->run() != 0) {
                throw new \RuntimeException('Can\'t run tar.');
            }
            
            unlink($database->getFileName());
        }
        
        return $this;
    }

}
