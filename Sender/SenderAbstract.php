<?php
/**
 *
 */

namespace CaptainJas\Sender;


abstract class SenderAbstract
{
    public function send($messages){
        if(!empty($messages) && is_array($messages)){
            foreach($messages as $message){
                $this->sendOne($message);
            }
        }
        
        return $this;
    }
    
    abstract public function sendOne(\CaptainJas\Utils\Message $message);
}