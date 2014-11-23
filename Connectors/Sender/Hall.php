<?php
/**
 * Api Doc https://hall.com/docs/integrations/generic/?uuid=5470233a57f060d89b00007b
 */

namespace CaptainJas\Connectors\Sender;

use CaptainJas\Connectors\Sender;

class Hall extends Message
{
    protected $_roomUrl;
    protected $_botName;
    protected $_botIcon;

    public function __construct($roomUrl, $botName, $botIcon)
    {
        $this->_roomUrl = $roomUrl;
        $this->_botName = $botName;
        $this->_botIcon = $botIcon;
    }

    /**
     * Send a message to hall
     *
     * Message can be HTML but with only this tags/attribute :
     *   <a>      href, title, target
     *   <audio>  controls, loop, muted, src, volume
     *   <b>
     *   <br>
     *   <code>
     *   <em>
     *   <i>
     *   <p>
     *   <source> src, type
     *   <strong>
     *   <track>  kind, label, src, srclang
     *
     * @param  \CaptainJas\Utils\Message $message
     * @return null
     */
    public function sendOne(\CaptainJas\Utils\Message $message)
    {
        if ($message->getText()) {

            $data = array(
                'title' => $this->_botName . ($message->getTitle() ? ' - ' . $message->getTitle() : ''),
                'message' => $message->getText(),
                'picture' => $message->getIcon() ? $message->getIcon() : $this->_botIcon
            );

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode($data),
                ),
            );

            $context = stream_context_create($options);
            file_get_contents($this->_roomUrl, false, $context);
        }
    }
}