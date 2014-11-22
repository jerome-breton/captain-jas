<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 22/11/14
 * Time: 09:57
 */

namespace CaptainJas\Sender;


class Hall {

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
     * @param  array    $message array('text' => to send, 'title' => to append to botName, 'icon' => replacing default botIcon)
     * @return null
     */
    public function send($message){
        if(!empty($message['text'])) {

            $data = array(
                'title' => $this->_botName . (empty($message['title']) ? '' : ' - ' . $title),
                'message' => $message['text'],
                'picture' => empty($message['icon']) ? $this->_botIcon : $message['icon']
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