<?php
/**
 *
 */

namespace CaptainJas\Sender;


class Hall extends SenderAbstract{

    protected $_roomUrl;
    protected $_botName;
    protected $_botIcon;

    public function __construct($roomUrl, $botName, $botIcon){
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
     * @param  \CaptainJas\Utils\Message    $message
     * @return null
     */
    public function send(\CaptainJas\Utils\Message $message){
        if($message->getText()) {

            $data = array(
                'title' => $this->_botName . ($message->getTitle()) ? ' - ' . $title : ''),
                'message' => $message->getText(),
                'picture' => empty($message->getIcon()) ? $this->_botIcon : $message->getIcon()
            );

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode($data),
                ),
            );
            $context = stream_context_create($options);
            $result = file_get_contents($this->_roomUrl, false, $context);
        }
    }
}