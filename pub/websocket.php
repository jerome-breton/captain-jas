<?php
namespace CaptainJas;

use CaptainJas\Connectors\Hook\HookAbstract;
use CaptainJas\Connectors\Sender\SenderAbstract;

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CaptainJas.php');
$captain = new CaptainJas();
$websocketName = CaptainJas::p('socket');

if (!$websocketName) {
    dieInHelp();
} else {
    runSocket($websocketName, $captain);
}

/**
 * @param $name
 * @param $captain
 */
function runSocket($name, CaptainJas $captain)
{
    if (!isset($captain->getConfig()->websockets->$name)) {
        throw new \InvalidArgumentException($name . ' is not defined in config.json');
    }

    $socketConfig = $captain->getConfig()->websockets->$name;

    if (isset($socketConfig->websockets->params)) {
        $params = $socketConfig->websockets->params;
    } else {
        $params = array();
    }
    /** @var HookAbstract $socket */
    $socket = $captain->getWebsocket($socketConfig->websocket->connector, '', $params);

    if (isset($socketConfig->sender->params)) {
        $params = $socketConfig->sender->params;
    } else {
        $params = array();
    }
    /** @var SenderAbstract $sender */
    $sender = $captain->getSender($socketConfig->sender->connector, '', $params);

    $socket->setSender($sender);
    $socket->process();
}

function dieInHelp()
{
    ?>
    <h1>Captain Jas Websocket</h1>
    <h2>A really light php websocket for interconnection of services.</h2>
    <h3>Usage :</h3>

    <p>Add this file to your system hook manager with
        <code>http://<?php echo $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'] ?>
            ?socket=&lt;socketname&gt;</code>
        The socketname must be configured in config.json file.</p>
    <?php
    exit(-1);
}