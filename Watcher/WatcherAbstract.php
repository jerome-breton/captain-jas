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
        parent::__construct();
    }

    protected function _saveData($key, $val){
        $this->_data[$key] = $val;
        ftruncate($this->_getFile(), 0);
        rewind($this->_getFile());

        foreach($this->_data as $key => $val){
            fputcsv($this->_getFile(), array(trim($key), trim($val)));
        }
    }
    
    protected function _getData($key){
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    private function _loadData(){
        while($row = fgetcsv($this->_getFile())){
            list($key, $val) = $row;
            $this->_data[$key] = $val;
        }

        return $this;
    }

    private function _getFile(){
        if(!$this->_file){
            $dir = join(DS, array(JAS_ROOT,'var','watcher',$this->_getClass()));
            if(!file_exists($dir)){
                mkdir($dir, 0777, true);
            }
            $this->_file = fopen($dir . DS . $this->_getDataIdentifier() . '.csv', 'c+');
        }

        return $this->_file;
    }

    protected function _getClass(){
        return str_replace('\\','_',get_class($this));
    }
}