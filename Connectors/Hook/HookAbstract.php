<?php
namespace CaptainJas\Connectors\Hook;

abstract class HookAbstract
{
    //Processes the event
    public function __construct()
    {

    }

    abstract function process();
}