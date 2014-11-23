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

/**
 * Class Message
 * @package CaptainJas\Connectors\Watcher\Subversion\Commit
 */
class Message extends Commit
{

    /**
     * @param $commits
     * @return bool|\CaptainJas\Utils\Message
     */
    protected function _processCommit($commits)
    {
        if (empty($commits)) {
            return false;
        }

        $message = '';
        $messageArray = array();
        foreach ($commits as $commit) {
            $messageArray[] = $this->_displayCommit($commit);
        }
        $message .= join('<br>', $messageArray);
        $message .= '';

        return new \CaptainJas\Utils\Message($message);
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
            $this->_displayCommitFiles($commit['changes'], '<br> ')
        ));
    }

    /**
     * @param $changes
     * @param $linePrefix
     * @return string
     */
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

            $html .= $linePrefix . $actionIcon . $this->_displayPathLink($change['path']);
        }

        return $html;
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









