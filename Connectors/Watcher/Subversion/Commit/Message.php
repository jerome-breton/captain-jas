<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 20:10
 */

namespace CaptainJas\Connectors\Watcher\Subversion\Commit;

use CaptainJas\Connectors\Watcher\Subversion;
use CaptainJas\Connectors\Watcher\Subversion\Commit;


class Message extends Commit
{


    protected function _processCommit($commits)
    {
        if (empty($commits)) {
            return false;
        }

        $message = '<code>';
        $messageArray = array();
        foreach ($commits as $commit) {
            $messageArray[] = $this->_displayCommit($commit);
        }
        $message .= join('<br>', $messageArray);
        $message .= '</code>';

        return new \CaptainJas\Utils\Message($message, 'New commit');
    }

    /**
     * Return the HTML of a commit
     *
     * @param array $commit
     * @param int $namePad pad and truncate names to this length
     * @param int $versionPad pad and truncate versions to this length
     * @return string
     */
    protected function _displayCommit($commit, $namePad = 20, $versionPad = 6)
    {
        return join(' ', array(
            '<b>' . str_pad($commit['version'], $versionPad) . '</b>',
            '<i>' . substr(str_pad($commit['author'], $namePad), 0, $namePad) . '</i>',
            $commit['comment'],
            $this->_displayCommitFiles($commit['changes'], '<br>' . str_pad('', $versionPad))
        ));
    }

    protected function _displayCommitFiles($changes, $linePrefix)
    {
        $html = '';
        foreach ($changes as $change) {
            switch ($change['action']) {
                case Subversion::ACTION_ADD:
                    $actionIcon = '&#10010; ';
                    break;
                case Subversion::ACTION_MOD:
                    $actionIcon = '&#9998; ';
                    break;
                case Subversion::ACTION_DEL:
                    $actionIcon = '&#9473; ';
                    break;
                default:
                    $actionIcon = '';
            }

            $html .= $linePrefix . $actionIcon . $change['path'];
        }

        return $html;
    }

    protected function _processLockChanges($createdLocks, $releasedLocks, $namePad = 20)
    {
        $html = '';
        if (!empty($createdLocks)) {
            $html .= '<b>New locked path:</b><br>';
            foreach ($createdLocks as $lock) {
                $html .= '<i>' . substr(str_pad($lock['author'], $namePad), 0, $namePad) . '</i> ' . $lock['path'] . '<br>';
            }
        }
        if (!empty($releasedLocks)) {
            $html .= '<b>Lock have been released:</b><br>';
            foreach ($createdLocks as $lock) {
                $html .= '<i>' . substr(str_pad($lock['author'], $namePad), 0, $namePad) . '</i> ' . $lock['path'] . '<br>';
            }
        }

        if ($html) {
            return new \CaptainJas\Utils\Message($html, 'Path locks');
        }
        return false;
    }
} 









