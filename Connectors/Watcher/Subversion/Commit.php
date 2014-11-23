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
        $locks = $this->_getLocks();
        $locksHash = $this->_getLocksHash($locks);
        $version = $this->_getVersion();
        $lastVersion = $this->_getData('version');
        $lastLocks = (array)json_decode($this->_getData('locks'), true);
        $lastLocksHash = $this->_getData('locksHash');
        $responses = array();

        if ($version == $lastVersion && $locksHash == $lastLocksHash) {
            return $responses;
        }

        if (!is_null($lastVersion)) {
            $response = $this->_processCommit($this->_getRepositoryLogs());
            if (!empty($response)) {
                $responses[] = $response;
            }
        }

        if (!is_null($lastLocksHash)) {
            $response = $this->_processLocks($lastLocks, $locks);
            if (!empty($response)) {
                $responses[] = $response;
            }
        }

        $this->_saveData('version', $version);
        $this->_saveData('locks', json_encode($locks));
        $this->_saveData('locksHash', $locksHash);

        return $responses;
    }

    /**
     * @param $locks
     * @return string
     */
    protected function _getLocksHash($locks)
    {
        return md5(join('|', array_keys($locks)));
    }

    /**
     * @param $commit
     * @return mixed
     */
    abstract protected function _processCommit($commit);

    /**
     * @param $oldLocks
     * @param $newLocks
     * @return mixed
     */
    protected function _processLocks($oldLocks, $newLocks)
    {
        $createdLocks = array_diff_key($newLocks, $oldLocks);
        $releasedLocks = array_diff_key($oldLocks, $newLocks);
        return $this->_processLockChanges($createdLocks, $releasedLocks);
    }

    /**
     * @param $createdLocks
     * @param $releasedLocks
     * @return mixed
     */
    abstract protected function _processLockChanges($createdLocks, $releasedLocks);
}