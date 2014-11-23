<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 20:09
 */

namespace CaptainJas\Watcher\Subversion;


abstract class Commit extends \CaptainJas\Watcher\Subversion{

    public function process(){
        $version = $this->_getVersion();
        $lastVersion = $this->_getData('version');
        
        if($version == $lastVersion){
            return false;
        }
        
        if(!is_null($lastVersion)){
            $response = $this->_processCommit($this->_getRepositoryLogs());
        } else {
            $response = false;
        }
        
        $this->_saveData('version', $version-20);
        
        return $response;
    }
    
    abstract protected function _processCommit($commit);
} 