<?php
/**
 * Works the same than hooks but provide functions to save state
 */

namespace CaptainJas\Watcher;


abstract class WatcherAbstract extends \CaptainJas\Hook\HookAbstract
{
    private $_data = array();
    private $_file = null;

    abstract protected function _getDataIdentifier();

    public function __destruct(){
        fclose($this->_getFile());
    }

    public function __construct(){
        $this->_loadData();
    }

    protected function _save($key, $val){
        $this->_data[$key] = $val;
        ftruncate($this->_getFile(), 0);

        foreach($this->_data as $key => $val){
            fputcsv($this->_getFile(), array($key, $val));
        }
    }

    private function _loadData(){
        while($row = fgetcsv($this->_getFile()))){
            list($key, $val) = $row;
            $this->_data[$key] = $val;
        }

        return $this;
    }

    private function _getFile(){
        if(!$_file){
            $this->_file = fopen(join(DS, array(
                JAS_ROOT,'var','watcher',$this->_getClass(),$this->_getDataIdentifier(),'.csv'
            )), 'c+');
        }

        return $this->_file;
    }

    private function _getClass(){
        return str_replace('\\','_',get_class($this));
    }
}