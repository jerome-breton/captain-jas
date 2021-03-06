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
abstract class Lock extends Subversion
{
    /**
     * @return array
     */
    public function process()
    {
        $locks = $this->_getLocks();
        $locksHash = $this->_getLocksHash($locks);
        $lastLocks = (array)json_decode($this->_getData('locks'), true);
        $lastLocksHash = $this->_getData('locksHash');
        $responses = array();

        if ($locksHash == $lastLocksHash) {
            return $responses;
        }

        if (!is_null($lastLocksHash)) {
            $response = $this->_processLocks($lastLocks, $locks);
            if (!empty($response)) {
                $responses[] = $response;
            }
        }

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