<?php
/**
 *
 */

namespace CaptainJas\Sender;


abstract class SenderAbstract
{
    abstract public function send(\CaptainJas\Utils\Message $message);
}