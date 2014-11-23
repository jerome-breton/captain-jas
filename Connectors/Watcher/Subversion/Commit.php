<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 20:09
 */

namespace CaptainJas\Connectors\Watcher\Subversion;
use CaptainJas\Connectors\Watcher\Subversion;

/**
 * Class Commit
 * @package CaptainJas\Connectors\Watcher\Subversion
 */
abstract class Commit extends Subversion
{
    /**
     * @return array
     */
    public function process()
    {
        $version = $this->_getVersion();
        $lastVersion = $this->_getData('version');
        $responses = array();

        if ($version == $lastVersion) {
            return $responses;
        }

        if (!is_null($lastVersion)) {
            $response = $this->_processCommit($this->_getRepositoryLogs());
            if (!empty($response)) {
                $responses[] = $response;
            }
        }

        $this->_saveData('version', $version);

        return $responses;
    }

    /**
     * @param $commit
     * @return mixed
     */
    abstract protected function _processCommit($commit);
}