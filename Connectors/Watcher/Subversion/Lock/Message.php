<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 20:10
 */

namespace CaptainJas\Connectors\Watcher\Subversion\Lock;

use CaptainJas\Connectors\Watcher\Subversion;
use CaptainJas\Connectors\Watcher\Subversion\Lock;

/**
 * Class Message
 * @package CaptainJas\Connectors\Watcher\Subversion\Lock
 */
class Message extends Lock
{
    /**
     * @param $createdLocks
     * @param $releasedLocks
     * @param int $namePad
     * @return bool|\CaptainJas\Utils\Message
     */
    protected function _processLockChanges($createdLocks, $releasedLocks, $namePad = 20)
    {
        $html = '';
        if (!empty($createdLocks)) {
            $html .= '<b>New locked path:</b><br>';
            foreach ($createdLocks as $lock) {
                $html .= '<i>' . substr(str_pad($lock['author'], $namePad), 0, $namePad) . '</i> '
                    . $this->_displayPathLink($lock['path']) . '<br>';
            }
        }
        if (!empty($releasedLocks)) {
            $html .= '<b>Lock have been released:</b><br>';
            foreach ($createdLocks as $lock) {
                $html .= '<i>' . substr(str_pad($lock['author'], $namePad), 0, $namePad) . '</i> '
                    . $this->_displayPathLink($lock['path']) . '<br>';
            }
        }

        if ($html) {
            return new \CaptainJas\Utils\Message($html);
        }
        return false;
    }

    /**
     * @param $path
     * @return string
     */
    protected function _displayPathLink($path)
    {
        return '<a target="' . $this->_getDataIdentifier() . '" href="' . $this->_svnurl . $path . '">'
        . $path . '</a>';
    }
}









