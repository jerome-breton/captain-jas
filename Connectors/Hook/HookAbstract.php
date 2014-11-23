<?php
namespace CaptainJas\Connectors\Hook;

abstract class HookAbstract
{
    public function __construct()
    {

    }

    /**
     * Processes the event
     */
    abstract function process();
}