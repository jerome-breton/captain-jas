<?php
/**
 * Abstract for classes that will send data to a service
 */
namespace CaptainJas\Connectors\Sender;

abstract class SenderAbstract
{
    abstract public function send($data);
}