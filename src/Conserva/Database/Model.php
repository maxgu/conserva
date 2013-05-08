<?php

namespace Conserva\Database;

class Model extends \T4\DomainModels\Model {
    
    protected $zipName;

    protected $data = array(
        'name'      => null,
    );
    
    public function getFileName() {
        return $this->getName() . '.sql';
    }
    
    public function getZipName() {
        if (empty($this->zipName)) {
            $this->zipName = sprintf('%s-%s.tar.gz', $this->getName(), date('YmdH'));
        }
        
        return $this->zipName;
    }
    
}
