<?php

namespace Conserva\Database;

class Model extends \T4\DomainModels\Model {

    protected $data = array(
        'name'      => null,
    );
    
    public function getFileName() {
        return $this->getName() . '.sql';
    }
    
    public function getZipName() {
        return sprintf('%s-%s.tar.gz', $this->getName(), date('YmdH'));
    }
    
}
