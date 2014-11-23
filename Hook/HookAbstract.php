<?php
namespace CaptainJas\Hook;

abstract class HookAbstract
{
    //Processes the event
    abstract function process();
    
    public function __construct(){

    }
}