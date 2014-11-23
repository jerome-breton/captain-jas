<?php
namespace CaptainJas;

use CaptainJas\Connectors\Sender\SenderAbstract;
use CaptainJas\Connectors\Watcher\WatcherAbstract;

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CaptainJas.php');
$captain = new CaptainJas();
$watchername = CaptainJas::p('watcher');

if ($watchername) {
    runWatcher($watchername, $captain);
} else {
    foreach ($captain->getConfig()->watchers as $name => $config) {
        runWatcher($name, $captain);
    }
}

/**
 * @param $watchername
 * @param $captain
 */
function runWatcher($watchername, $captain)
{
    if (!isset($captain->getConfig()->watchers->$watchername)) {
        throw new \InvalidArgumentException($watchername . ' is not defined in config.json');
    }

    $watcherConfig = $captain->getConfig()->watchers->$watchername;

    if (isset($watcherConfig->watcher->params)) {
        $params = $watcherConfig->watcher->params;
    } else {
        $params = array();
    }
    /** @var WatcherAbstract $watcher */
    $watcher = $captain->getWatcher($watcherConfig->watcher->connector, '', $params);

    if (isset($watcherConfig->sender->params)) {
        $params = $watcherConfig->sender->params;
    } else {
        $params = array();
    }
    /** @var SenderAbstract $sender */
    $sender = $captain->getSender($watcherConfig->sender->connector, '', $params);

    $data = $watcher->process();
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