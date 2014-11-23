<?php
namespace CaptainJas;

use CaptainJas\Connectors\Hook\HookAbstract;
use CaptainJas\Connectors\Sender\SenderAbstract;

require_once('..\CaptainJas.php');
$captain = new CaptainJas();
$hookname = CaptainJas::p('hook');

if (!$hookname) {
    dieInHelp();
} else {
    runHook($hookname, $captain);
}

/**
 * @param $hookname
 * @param $captain
 */
function runHook($hookname, $captain)
{
    if (!isset($captain->getConfig()->hooks->$hookname)) {
        throw new \InvalidArgumentException($hookname . ' is not defined in config.json');
    }

    $hookConfig = $captain->getConfig()->hooks->$hookname;

    if (isset($hookConfig->hook->params)) {
        $params = $hookConfig->hook->params;
    } else {
        $params = array();
    }
    /** @var HookAbstract $hook */
    $hook = $captain->getHook($hookConfig->hook->connector, '', $params);

    if (isset($hookConfig->sender->params)) {
        $params = $hookConfig->sender->params;
    } else {
        $params = array();
    }
    /** @var SenderAbstract $sender */
    $sender = $captain->getSender($hookConfig->sender->connector, '', $params);

    $data = $hook->process();
    if ($data) {
        $sender->send($data);
    }
}

function dieInHelp()
{
    ?>
    <h1>Captain Jas Hook</h1>
    <h2>A really light php hook for interconnection of services.</h2>
    <h3>Usage :</h3>

    <p>Add this file to your system hook manager with
        <code>http://<?php echo $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'] ?>
            ?hook=&lt;hookname&gt;</code>
        The hookname must be configured in config.json file.</p>
    <?php
    exit(-1);
}