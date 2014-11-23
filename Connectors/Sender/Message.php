<?php
/**
 * Abstract for classes that will send data to a service
 */
namespace CaptainJas\Connectors\Sender;

abstract class Message extends SenderAbstract
{
    public function send($messages)
    {
        if (!empty($messages) && is_array($messages)) {
            foreach ($messages as $message) {
                $this->sendOne($message);
            }
        }

        return $this;
    }

    abstract public function sendOne(\CaptainJas\Utils\Message $message);
}