<?php
namespace CaptainJas;
require_once('bootstrap.php');

$watch = new Watcher\Basecamp\Events\Message($_GET['account'], $_GET['project'], $_GET['user'], $_GET['pass']);
$sender = new Sender\Hall($_GET['roomurl'], 'Basecamp', 'https://avatars1.githubusercontent.com/u/13131?v=3&s=200');

$message = $watch->process();
if ($message) {
    $sender->send($message);
}