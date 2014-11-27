<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 27/11/14
 * Time: 06:54
 */

namespace CaptainJas\Connectors\Websocket;


use CaptainJas\Connectors\Sender\SenderAbstract;
use CaptainJas\Utils\Message;
use Devristo\Phpws\Client\WebSocket;
use Zend\Log\LoggerInterface;

class Demo extends WebsocketAbstract
{

    protected $_url = 'ws://echo.websocket.org/?encoding=text';

    /**
     * The unique key that will be used for this instance state file
     *
     * @return string
     */
    protected function _getDataIdentifier()
    {
        return md5($this->_url);
    }

    protected function _registerEvents(WebSocket $client, SenderAbstract $sender, LoggerInterface $logger)
    {
        $client->on("request", function ($headers) use ($sender, $logger) {
            $sender->send(array(new Message('Request object created!')));
        });

        $client->on("handshake", function () use ($sender, $logger) {
            $sender->send(array(new Message("Handshake received!")));
        });

        $client->on("connect", function () use ($sender, $logger, $client) {
            $sender->send(array(new Message("Connected!")));
            $client->send("Hello world!");
        });

        $client->on("message", function ($message) use ($sender, $client, $logger) {
            $sender->send(array(new Message("Got message: " . $message->getData())));
            $client->close();
        });
    }
}