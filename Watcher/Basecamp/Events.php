<?php
/**
 * ${File_Description}
 *
 * @category   ${NameSpace}
 * @package    ${NameSpace}_${NomDuModule}
 * @author     jbreton
 * @date       23/11/14 10:23
 */

namespace CaptainJas\Watcher\Basecamp;

use CaptainJas\Watcher\Basecamp;

/**
 * Basecamp Events watch class
 * @package CaptainJas\Watcher\Basecamp
 */
abstract class Events extends Basecamp
{

    public function process()
    {
        var_dump($this->_request('events'));
    }
}
