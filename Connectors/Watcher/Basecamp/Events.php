<?php
/**
 * Api doc : https://github.com/basecamp/bcx-api/blob/master/sections/events.md
 */

namespace CaptainJas\Connectors\Watcher\Basecamp;

use CaptainJas\Connectors\Watcher\Basecamp;

/**
 * Basecamp Events watch class
 * @package CaptainJas\Connectors\Watcher\Basecamp
 */
abstract class Events extends Basecamp
{

    public function process()
    {
        $since = $this->_getData('since');
        $responses = array();

        if (is_null($since)) {
            $since = date('c');
        }

        $events = $this->_request('events', array('since' => str_replace('+', '%2B', $since)));

        if (!empty($events)) {
            $firstEvent = reset($events);
            $since = $firstEvent->created_at;
        }

        $response = $this->_processEvents($events);

        if (!empty($response)) {
            $responses[] = $response;
        }

        $this->_saveData('since', $since);

        return $responses;
    }

    abstract protected function _processEvents($events);
}
