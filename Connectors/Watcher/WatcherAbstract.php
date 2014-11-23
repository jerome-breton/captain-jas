<?php
/**
 * Works the same than hooks but provide functions to save state
 */

namespace CaptainJas\Connectors\Watcher;

use CaptainJas\Connectors\Hook\HookAbstract;
use CaptainJas\Connectors\Watcher;

/**
 * Class WatcherAbstract
 * @package CaptainJas\Connectors\Watcher
 */
abstract class WatcherAbstract extends HookAbstract
{
    private $_data = null;
    private $_file = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->_loadData();
        parent::__construct();
    }

    /**
     * @return $this
     */
    private function _loadData()
    {
        $this->_data = array();
        while ($row = fgetcsv($this->_getFile())) {
            list($key, $val) = $row;
            $this->_data[$key] = $val;
        }

        return $this;
    }

    /**
     * @return null|resource
     */
    private function _getFile()
    {
        if (!$this->_file) {
            $dir = join(DS, array(JAS_ROOT, 'var', 'watcher', $this->_getClass()));
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $this->_file = fopen($dir . DS . $this->_getDataIdentifier() . '.csv', 'c+');
        }

        return $this->_file;
    }

    /**
     * @return mixed
     */
    protected function _getClass()
    {
        return str_replace('\\', '_', get_class($this));
    }

    /**
     * The unique key that will be used for this instance state file
     *
     * @return string
     */
    abstract protected function _getDataIdentifier();

    public function __destruct()
    {
        fclose($this->_getFile());
    }

    /**
     * Save key value in state file
     *
     * @param $key
     * @param $val
     */
    protected function _saveData($key, $val)
    {
        $this->_data[$key] = $val;
        ftruncate($this->_getFile(), 0);
        rewind($this->_getFile());

        foreach ($this->_data as $key => $val) {
            fputcsv($this->_getFile(), array(trim($key), trim($val)));
        }
    }

    /**
     * Get key value in state file
     *
     * @param $key
     * @return null
     */
    protected function _getData($key)
    {
        if (!is_array($this->_data)) {
            throw new \LogicException('$this->_data is not inited. Have you called parent::__construct() ?');
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
}