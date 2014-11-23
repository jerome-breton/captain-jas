<?php
/**
 * ${File_Description}
 *
 * @category   ${NameSpace}
 * @package    ${NameSpace}_${NomDuModule}
 * @author     jbreton
 * @date       23/11/14 10:24
 */
namespace CaptainJas\Watcher\Basecamp\Events;

use CaptainJas\Watcher\Basecamp\Events;

/**
 * Basecamp event watch building a message class
 * @package CaptainJas\Watcher\Basecamp\Events
 */
class Message extends Events
{
    protected function _processEvents($events){
        if (empty($events)) {
            return false;
        }

        $message = '';
        $messageArray = array();
        foreach ($events as $event) {
            $messageArray[] = $this->_displayEvent($event);
        }
        $message .= join('<br>', $messageArray);
        $message .= '';

        return new \CaptainJas\Utils\Message($message);
    }
    
    protected function _displayEvent($event){
        $html = '<b>' . $event->creator->name . '</b> ' . $event->action . ' <a href="' . $event->html_url . '">' .
        $event->target . '</a>';
        if(!empty($event->excerpt)){
            $html .= ' : <i>' . $event->excerpt . '</i>';
        }
        
        return $html;
    }
}