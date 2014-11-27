<?php
/**
 * Works the same than hooks but provide functions to save state
 */

namespace CaptainJas\Connectors\Websocket;

use CaptainJas\Connectors\Sender\SenderAbstract;
use CaptainJas\Connectors\Watcher\WatcherAbstract;
use CaptainJas\Connectors\Websocket;
use React\EventLoop\Factory;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;
use Zend\Log\Writer\Stream;

/**
 * Class WebsocketAbstract
 * @package CaptainJas\Connectors\Websocket
 */
abstract class WebsocketAbstract extends WatcherAbstract
{
    //Must be redifined
    protected $_url = null;

    //Could be redefined
    protected $_streamOptions = array();
    protected $_timeout = null;

    //@TODO implement global mecanism
    protected $_client = null;
    protected $_loop = null;
    protected $_writer = null;
    protected $_logger = null;

    protected $_sender = null;

    public function process()
    {
        $this->_run($this->_getUrl(), $this->_getStreamOptions(), $this->_getTimeout());
    }

    protected function _run($url, array $streamOptions = null, $timeout = null)
    {
        $this->_getClient($url, $streamOptions)->open($timeout);

        $this->_registerEvents($this->_client, $this->_sender, $this->_logger);

        $this->_getLoop()->run();
    }

    protected function _getClient($url, array $streamOptions = null)
    {
        if (!$this->_client) {
            $this->_client = new \Devristo\Phpws\Client\WebSocket(
                $url, $this->_getLoop(), $this->_getLogger(), $streamOptions
            );
        }

        return $this->_client;
    }

    protected function _getLoop()
    {
        if (!$this->_loop) {
            $this->_loop = Factory::create();
        }

        return $this->_loop;
    }

    protected function _getLogger()
    {
        if (!$this->_logger) {
            $this->_logger = new Logger();
            $this->_logger->addWriter($this->_getWriter());
        }

        return $this->_logger;
    }

    protected function _getWriter()
    {
        if (!$this->_writer) {
            $this->_writer = new Stream("php://output");
        }

        return $this->_writer;
    }

    abstract protected function _registerEvents(
        \Devristo\Phpws\Client\WebSocket $client,
        SenderAbstract $sender,
        LoggerInterface $logger
    );

    protected function _getUrl()
    {
        return $this->_url;
    }

    protected function _getStreamOptions()
    {
        return $this->_streamOptions;
    }

    protected function _getTimeout()
    {
        return $this->_timeout;
    }

    public function setSender(SenderAbstract $sender)
    {
        $this->_sender = $sender;
    }
}